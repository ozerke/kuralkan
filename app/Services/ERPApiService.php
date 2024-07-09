<?php

namespace App\Services;

use App\Http\Requests\API\CCPaymentsListRequest;
use App\Http\Requests\API\ConsignedProductsListRequest;
use App\Http\Requests\API\EbondsListRequest;
use App\Http\Requests\API\OrderPaymentUpdateRequest;
use App\Http\Requests\API\OrderUpdateRequest;
use App\Http\Requests\API\PendingOrdersRequest;
use App\Http\Resources\API\ConsignedProductResource;
use App\Http\Resources\API\EbondResource;
use App\Http\Resources\API\OrderPaymentResource;
use App\Http\Resources\API\OrderResource;
use App\Mail\OrderInvoiceReadyMail;
use App\Jobs\SendEmailJob;
use App\Jobs\SendSMSJob;
use App\Jobs\UpdateConsignedProductsJob;
use App\Jobs\UpdateEbondsJob;
use App\Jobs\UpdateProductsJob;
use App\Jobs\UpdateProductSpecsJob;
use App\Jobs\UpdateSalesPointsJob;
use App\Jobs\UpdateSalesPointsStocksJob;
use App\Mail\OrderCreatedMail;
use App\Models\Bank;
use App\Models\ConsignedProduct;
use App\Models\Ebond;
use App\Models\LegalRegistration;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\OrderStatus;
use Carbon\Carbon;
use Exception;

class ERPApiService
{
    public function __construct()
    {
    }

    public function getPendingOrders(PendingOrdersRequest $request)
    {
        $data = $request->validated();

        LoggerService::logInfo(LogChannelsEnum::GetPendingOrders, 'Requested', ['request' => $request->all()]);

        $from = $data['from'];
        $to = $data['to'];
        $withConfirmed = (bool) $data['withConfirmed'];

        if ($from) {
            $from = explode('-', $from);
            $from = $from[2] . '-' . $from[1] . '-' . $from[0];
            $from = Carbon::parse($from);
        }

        if ($to) {
            $to = explode('-', $to);
            $to = $to[2] . '-' . $to[1] . '-' . $to[0];
            $to = Carbon::parse($to);
        }

        $orderQuery = Order::with([
            'invoiceUser.district.city',
            'deliveryUser',
            'user',
            'productVariation.product',
            'productVariation.color'
        ]);

        if ($from && !$to) {
            $orderQuery = $orderQuery->whereDate('created_at', '>=', $from->format('Y-m-d'));
        }

        if (!$from && $to) {
            $orderQuery = $orderQuery->whereDate('created_at', '<=', $to->format('Y-m-d'));
        }

        if ($from && $to) {
            $orderQuery = $orderQuery->whereBetween('created_at', [$from->format('Y-m-d 00:00'), $to->format('Y-m-d 23:59')]);
        }

        if ($withConfirmed) {
            $orderQuery = $orderQuery->where(function ($q) {
                $q->whereNot('payment_type', 'S')->orWhere('payment_type', null);
            })->confirmed(true)->get();

            $payload = OrderResource::collection($orderQuery);

            LoggerService::logSuccess(LogChannelsEnum::GetPendingOrders, 'API RESPONSE', ['data' => $payload]);

            return $payload;
        }

        $orderQuery = $orderQuery->where(function ($q) {
            $q->whereNot('payment_type', 'S')->orWhere('payment_type', null);
        })->confirmed(false)->get();

        $payload = OrderResource::collection($orderQuery);

        LoggerService::logSuccess(LogChannelsEnum::GetPendingOrders, 'API RESPONSE', ['data' => $payload]);

        return $payload;
    }

    public function updateOrder(OrderUpdateRequest $request)
    {
        try {
            LoggerService::logInfo(LogChannelsEnum::UpdateOrder, 'Requested', ['request' => $request->all()]);

            $data = $request->all();

            $wasPendingOrder = true;

            $orderNo = $data['order_no'];
            $erpOrderId = $data['erp_order_id'];

            if (str_contains($erpOrderId, 'SY')) {
                $erpOrderId = str_replace('SY', '', $erpOrderId);
            }

            if (str_contains($erpOrderId, 'SK')) {
                $erpOrderId = str_replace('SK', '', $erpOrderId);
            }

            $order = Order::where('order_no', $orderNo)->first();

            if ($order) {

                if (!empty($order->erp_order_id)) {
                    $wasPendingOrder = false;
                }

                if (isset($data['erp_order_status'])) {
                    $status = OrderStatus::where('erp_order_status', $data['erp_order_status'])->first();

                    if ($status) {
                        if ($order->latest_order_status_id != $status->id) {
                            $order->statusHistory()->create([
                                'order_status_id' => $status->id,
                                'user_id' => $order->invoice_user_id
                            ]);
                        }
                    } else {
                        LoggerService::logError(LogChannelsEnum::UpdateOrder, 'Status not found', ['status' => $data['erp_order_status']]);

                        return response()->json(['error' => 'This status is not defined'], 400);
                    }
                }

                $date = $data['delivery_date'] ?? null;

                if ($date) {
                    $date = explode('-', $date);
                    $date = $date[2] . '-' . $date[1] . '-' . $date[0];
                }

                if (empty($order->invoice_link) && !empty($data['invoice_link'])) {
                    $fullname = $order->invoiceUser->full_name;

                    $message = SMSTemplateParser::orderInvoiceReady(
                        $fullname,
                        $order->order_no
                    );

                    dispatch(new SendSMSJob($order->invoiceUser->phone, $message));
                    dispatch(new SendEmailJob($order->invoiceUser->email, new OrderInvoiceReadyMail($order)));
                }
                
                if ($order->erp_prefix === 'SK') {
                    $order->update([
                        'erp_order_id' => $data['erp_order_id'],
                        'motor_no' => $data['engine_no'] ?? null,
                        'invoice_link' => $data['invoice_link'] ?? null,
                        'temprorary_licence_doc_link' => $data['temprorary_licence_doc_link'] ?? null,
                        'plate_printing_doc_link' => $data['plate_printing_doc_link'] ?? null,
                        'delivery_date' => $date ?? null,
                        'total_amount' => $data['total_amount'] ?? $order->total_amount,
                        'erp_response_at' => Carbon::now()
                    ]);
                } else {
                    $order->update([
                        'erp_order_id' => $data['erp_order_id'],
                        'chasis_no' => $data['chasis_no'] ?? null,
                        'motor_no' => $data['engine_no'] ?? null,
                        'invoice_link' => $data['invoice_link'] ?? null,
                        'temprorary_licence_doc_link' => $data['temprorary_licence_doc_link'] ?? null,
                        'plate_printing_doc_link' => $data['plate_printing_doc_link'] ?? null,
                        'delivery_date' => $date ?? null,
                        'total_amount' => $data['total_amount'] ?? $order->total_amount,
                        'erp_response_at' => Carbon::now()
                    ]);
                }


                if (!empty($data['temprorary_licence_doc_link'])) {
                    $registrationForm = LegalRegistration::where('order_id', $order->id)->first();

                    if ($registrationForm) {
                        $registrationForm->update([
                            'approved_by_erp' => 'approved',
                            'params' => ''
                        ]);
                    }
                }

                if (!$order->invoiceUser->erp_user_id) {
                    $order->invoiceUser->update([
                        'erp_user_id' => $data['erp_user_id']
                    ]);
                }

                if ($wasPendingOrder) {
                    /*$message = SMSTemplateParser::orderProcessed(
                        $order->invoiceUser->full_name,
                        $order->order_no
                    );

                    dispatch(new SendSMSJob($order->invoiceUser->phone, $message));
                    dispatch(new SendEmailJob($order->invoiceUser->email, new OrderCreatedMail($order)));*/
                }
            } else {
                LoggerService::logError(LogChannelsEnum::UpdateOrder, 'Order not found', ['order_no' => $orderNo]);

                return response()->json('Order not found:' . $orderNo, 404);
            }

            return response()->json(['status' => true], 200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::UpdateOrder, 'updateOrder', ['e' => $e]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getCCPaymentList(CCPaymentsListRequest $request)
    {
        $data = $request->validated();

        LoggerService::logInfo(LogChannelsEnum::GetCCPaymentList, 'Requested', ['request' => $request->all()]);

        $from = $data['from'];
        $to = $data['to'];
        $withConfirmed = (bool) $data['withConfirmed'];

        if ($from) {
            $from = explode('-', $from);
            $from = $from[2] . '-' . $from[1] . '-' . $from[0];
            $from = Carbon::parse($from);
        }

        if ($to) {
            $to = explode('-', $to);
            $to = $to[2] . '-' . $to[1] . '-' . $to[0];
            $to = Carbon::parse($to);
        }

        $paymentQuery = OrderPayment::with([
            'order',
            'bankAccount.bank',
        ]);

        if ($from && !$to) {
            $paymentQuery = $paymentQuery->whereDate('created_at', '>=', $from->format('Y-m-d'));
        }

        if (!$from && $to) {
            $paymentQuery = $paymentQuery->whereDate('created_at', '<=', $to->format('Y-m-d'));
        }

        if ($from && $to) {
            $paymentQuery = $paymentQuery->whereBetween('created_at', [$from->format('Y-m-d 00:00'), $to->format('Y-m-d 23:59')]);
        }

        if ($withConfirmed) {
            $paymentQuery = $paymentQuery->where(function ($q) {
                $q->where('payment_type', 'K')->where('seen_by_erp', true)->where('failed', false);
            })->get();

            $payload = OrderPaymentResource::collection($paymentQuery);

            LoggerService::logInfo(LogChannelsEnum::GetCCPaymentList, 'API RESPONSE', ['data' => $payload]);

            return $payload;
        }

        $paymentQuery = $paymentQuery->where(function ($q) {
            $q->where('payment_type', 'K')->where('seen_by_erp', false)->where('failed', false);
        })->get();

        $payload = OrderPaymentResource::collection($paymentQuery);

        LoggerService::logInfo(LogChannelsEnum::GetCCPaymentList, 'API RESPONSE', ['data' => $payload]);

        return $payload;
    }

    public function updatePayment(OrderPaymentUpdateRequest $request)
    {
        try {
            LoggerService::logInfo(LogChannelsEnum::UpdatePayment, 'Requested', ['request' => $request->all()]);

            $data = $request->all();

            $erpOrderId = $data['erp_order_id'];
            $orderNo = $data['order_no'];

            if (str_contains($erpOrderId, 'SY')) {
                $erpOrderId = str_replace('SY', '', $erpOrderId);
            }

            if (str_contains($erpOrderId, 'SK')) {
                $erpOrderId = str_replace('SK', '', $erpOrderId);
            }

            $order = Order::where('order_no', $orderNo)->first();

            if ($order) {
                $order->orderPayments()->where('approved_by_erp', 'N')->delete();

                $paymentRef = $data['payment_ref_no'] ?? null;

                if ($paymentRef) {
                    $payment = OrderPayment::where('payment_ref_no', $data['payment_ref_no'])->first();

                    if ($payment) {
                        $payment->update([
                            'approved_by_erp' => (bool)$data['approved_by_erp'] ? 'Y' : 'N',
                            'seen_by_erp' => true,
                            'e_bond_no' => isset($data['e_bond_no']) ? $data['e_bond_no'] : $payment->e_bond_no
                        ]);
                    } else {
                        LoggerService::logError(LogChannelsEnum::UpdatePayment, 'Payment not found', ['request' => $request->all()]);

                        return response()->json(['error' => 'Payment not found'], 400);
                    }
                } else {
                    $bank = $data['bank_name'] ? Bank::where('erp_bank_name', $data['bank_name'])->first() : null;

                    $order->orderPayments()->create([
                        'payment_amount' => $data['amount_received'],
                        'payment_type' => 'H',
                        'approved_by_erp' => 'Y',
                        'number_of_installments' => 1,
                        'collected_payment' => $data['amount_received'],
                        'description' => 'ERP payment by SetOdemeDurum',
                        'payment_ref_no' => OrderPayment::generatePaymentRefNo($order->order_no),
                        'user_id' => $order->invoice_user_id,
                        'bank_account_id' => $bank ? $bank->bankAccount->id : null,
                        'e_bond_no' => isset($data['e_bond_no']) ? $data['e_bond_no'] : null
                    ]);
                }
            } else {
                LoggerService::logError(LogChannelsEnum::UpdatePayment, 'Order not found', ['order_no' => $orderNo]);

                return response()->json('Order not found:' . $orderNo, 404);
            }

            return response()->json(['status' => true], 200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::UpdatePayment, 'updatePayment', ['e' => $e]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateProductList($productsCollection)
    {
        dispatch(new UpdateProductsJob($productsCollection));
    }

    public function updateProductSpecifications($specsCollection)
    {
        dispatch(new UpdateProductSpecsJob($specsCollection));
    }

    public function updateShopServices($usersCollection)
    {
        dispatch(new UpdateSalesPointsJob($usersCollection));
    }

    public function updateStocks($stocksCollection)
    {
        dispatch(new UpdateSalesPointsStocksJob($stocksCollection));
    }

    public function updateConsignedProducts($consignedProducts)
    {
        dispatch(new UpdateConsignedProductsJob($consignedProducts));
    }

    public function getConsignedProductsList(ConsignedProductsListRequest $request)
    {
        $data = $request->validated();

        LoggerService::logInfo(LogChannelsEnum::GetConsignedProductsList, 'Requested', ['request' => $request->all()]);

        $erpUserId = $data['erp_user_id'] ?? null;
        $chasisNo = $data['chasis_no'] ?? null;
        $stockCode = $data['stock_code'] ?? null;

        $listQuery = ConsignedProduct::query()
            ->with([
                'user:id,erp_user_id',
                'productVariation.product:id,stock_code',
                'productVariation.color:id,color_code'
            ]);

        if ($erpUserId) {
            $listQuery = $listQuery->whereHas('user', function ($userQuery) use ($erpUserId) {
                $userQuery->where('erp_user_id', $erpUserId);
            });
        }

        if ($chasisNo) {
            $listQuery = $listQuery->where('chasis_no', $chasisNo);
        }

        if ($stockCode) {
            $listQuery = $listQuery->whereHas('productVariation.product', function ($productQuery) use ($stockCode) {
                $productQuery->where('stock_code', $stockCode);
            });
        }

        $listQuery = $listQuery->get();

        $payload = ConsignedProductResource::collection($listQuery);

        LoggerService::logSuccess(LogChannelsEnum::GetConsignedProductsList, 'API RESPONSE', ['data' => $payload]);

        return $payload;
    }

    public function updateEbonds($ebonds)
    {
        dispatch(new UpdateEbondsJob($ebonds));
    }

    public function getEbondsList(EbondsListRequest $request)
    {
        $data = $request->validated();

        LoggerService::logInfo(LogChannelsEnum::EbondsList, 'Requested', ['request' => $request->all()]);

        $from = $data['from'];
        $to = $data['to'];
        $erpOrderId = $data['erp_order_id'];
        $eBondNo = $data['e_bond_no'];

        $bondsQuery = Ebond::query();

        if ($erpOrderId) {
            $bondsQuery = $bondsQuery->where('erp_order_id', $erpOrderId);
        }

        if ($eBondNo) {
            $bondsQuery = $bondsQuery->where('e_bond_no', $eBondNo);
        }

        if(!$erpOrderId && !$eBondNo) {

            $today = Carbon::now();

            if ($from) {
                $from = explode('-', $from);
                $from = $from[2] . '-' . $from[1] . '-' . $from[0];
                $from = Carbon::parse($from);
            }

            if ($to) {
                $to = explode('-', $to);
                $to = $to[2] . '-' . $to[1] . '-' . $to[0];
                $to = Carbon::parse($to);
            }

            if ($from && $to && $from->diffInDays($to) > 14) {
                return response()->json(['error' => 'Bu tarihler arasındaki aralık 2 haftayı geçmemelidir.'], 422);
            }

            if ($from && !$to) {
                $to = $from->copy()->addDays(14);
            }

            if (!$from && $to) {
                $from = $to->copy()->subDays(14);
            }

            if (!$from && !$to) {
                $from = Carbon::today();
                $to = $from->copy()->addDays(14);
            }

            $bondsQuery = $bondsQuery->whereBetween('due_date', [$from->format('Y-m-d 00:00'), $to->format('Y-m-d 23:59')]);
        }

        $bondsQuery = $bondsQuery->orderBy('due_date', 'asc')->get();

        $payload = EbondResource::collection($bondsQuery);

        LoggerService::logSuccess(LogChannelsEnum::EbondsList, 'API RESPONSE', ['data' => $payload]);

        return $payload;
    }
}
