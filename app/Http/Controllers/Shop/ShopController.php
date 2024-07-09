<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Orders\SoapSendOrderController;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function orders(Request $request)
    {
        $user = auth()->user();
        $ordersQuery = $user->createdOrders();
        $statusQuery = $user->createdOrders();

        if ($request->input('from') && $request->input('to')) {
            $from = explode('-', $request->input('from'));
            $from = $from[2] . '-' . $from[0] . '-' . $from[1];

            $to = explode('-', $request->input('to'));
            $to = $to[2] . '-' . $to[0] . '-' . $to[1];

            $day = Carbon::parse($from)->format('Y-m-d');

            $range[0] = Carbon::parse($from)->format('Y-m-d 00:00');
            $range[1] = Carbon::parse($to)->format('Y-m-d 23:59');;

            if ($range[0] === $range[1]) {
                $ordersQuery->whereDate('created_at', $day);
                $statusQuery->whereDate('created_at', $day);
            } else {
                $ordersQuery->whereBetween('created_at', [$range[0], $range[1]]);
                $statusQuery->whereBetween('created_at', [$range[0], $range[1]]);
            }
        }

        if ($request->input('status')) {
            $ordersQuery->where('latest_order_status_id', $request->input('status'));
        }

        if ($request->input('search')) {
            $ordersQuery
                ->where(function ($q) use ($request) {
                    $q->where('order_no', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('product_name', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('total_amount', 'like', '%' . $request->input('search') . '%')
                        ->orWhereHas('deliveryUser', function ($query) use ($request) {
                            $query->where('address', 'like', '%' . $request->input('search') . '%');
                        })
                        ->orWhereHas('invoiceUser', function ($query) use ($request) {
                            $query
                                ->where('site_user_name', 'like', '%' . $request->input('search') . '%')
                                ->orWhere('site_user_surname', 'like', '%' . $request->input('search') . '%');
                        });
                });


            $statusQuery->where(function ($q) use ($request) {
                $q->where('order_no', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('product_name', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('total_amount', 'like', '%' . $request->input('search') . '%')
                    ->orWhereHas('deliveryUser', function ($query) use ($request) {
                        $query->where('address', 'like', '%' . $request->input('search') . '%');
                    })
                    ->orWhereHas('invoiceUser', function ($query) use ($request) {
                        $query
                            ->where('site_user_name', 'like', '%' . $request->input('search') . '%')
                            ->orWhere('site_user_surname', 'like', '%' . $request->input('search') . '%');
                    });
            });
        }

        $orders = $ordersQuery->orderByDesc('created_at')->paginate(5);

        $orderStatuses = OrderStatus::all();

        $translations = collect($orderStatuses);

        $awaiting = $translations->where('id', 1)->first();
        $confirmed = $translations->where('id', 2)->first();
        $supplying = $translations->where('id', 3)->first();
        $servicePoint = $translations->where('id', 4)->first();
        $delivered = $translations->where('id', 5)->first();

        $translations = [
            'awaiting' => $awaiting->currentTranslation->status,
            'confirmed' =>  $confirmed->currentTranslation->status,
            'supplying' =>  $supplying->currentTranslation->status,
            'servicePoint' =>  $servicePoint->currentTranslation->status,
            'delivered' =>  $delivered->currentTranslation->status,
        ];

        $statuses = $statusQuery->get()->countBy(fn ($order) => $order->latest_order_status_id)->all();

        return view('shop.index')->with([
            'orders' => $orders,
            'translations' => $translations,
            'awaiting' => $statuses[1] ?? 0,
            'confirmed' => $statuses[2] ?? 0,
            'supplying' => $statuses[3] ?? 0,
            'servicePoint' => $statuses[4] ?? 0,
            'delivered' => $statuses[5] ?? 0,
        ]);
    }

    public function orderDetails(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->getOrderForUser($orderNo);

            if (!$order) {
                return redirect()->route('panel')->with('error', 'Order not found.');
            }

            $paidState = $order->getOrderPaymentsState();

            if ($order->isSalesAgreementOrder() && $paidState['is_paid'] && empty($order->salesAgreement->agreement_document_link)) {
                $url = (new SoapSendOrderController())->salesAgreementDocs($order->salesAgreement->findeks_request_id, 1);

                if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
                    LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'Shop Order details page: Bad document URL', ['url' => $url, 'order_no' => $order->order_no]);
                } else {
                    $order->salesAgreement->update([
                        'agreement_document_link' => $url
                    ]);
                }
            }

            $artesResponse = $order->checkLegalRegistrationState();

            if ($artesResponse) {
                return view('shop.orders.order-details')->with([
                    'order' => $order,
                    'artesResponse' => $artesResponse
                ]);
            }

            return view('shop.orders.order-details')->with([
                'order' => $order,
                'artesResponse' => null
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Shop Order Details', ['e' => $e]);

            return redirect()->route('panel')->with('error', 'Error occured');
        }
    }

    public function settingsPage(Request $request)
    {
        try {
            return view('shop.settings');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Shop Settings Page', ['e' => $e]);

            return redirect()->route('panel')->with('error', 'Error occured');
        }
    }

    public function paymentPlans(Request $request)
    {
        try {
            $products = Product::displayable()->get();

            $downPayments = null;
            $installments = null;

            $productId = $request->input('product');
            $downPaymentId = $request->input('down_payment');

            if (!empty($productId)) {
                $product = Product::with('downPayments.installmentOptions')->find($productId);


                if ($product) {
                    $downPayments = $product->downPayments;

                    if ($downPaymentId) {
                        $downPayment = $downPayments->where('id', $downPaymentId)->first();

                        if ($downPayment) {
                            $installments = $downPayment->installmentOptions;
                        }
                    }
                }
            }

            return view('shop.payment-plans')->with([
                'products' => $products,
                'downPayments' => $downPayments,
                'installments' => $installments
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Shop Payment Plans', ['e' => $e]);

            return redirect()->route('panel')->with('error', 'Error occured');
        }
    }
}
