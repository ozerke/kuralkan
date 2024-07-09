<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Orders\SoapSendOrderController;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\OrderStatus;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $orders = Order::query()->with(['productVariation' => ['product', 'color'], 'orderPayments', 'user', 'invoiceUser', 'orderCampaign', 'salesAgreement', 'latestStatusHistory.orderStatus']);
            $statusesQuery = Order::query();

            if ($request->input('search')) {
                $orders->search($request->input('search'), $request->input('sort_by_weight') == 'on');

                $statusesQuery->search($request->input('search'), $request->input('sort_by_weight') == 'on');
            }

            if ($request->input('date-range')) {
                $range = explode(' - ', $request->input('date-range'));

                $range[0] = Carbon::parse($range[0])->format('Y-m-d 00:00');
                $range[1] = Carbon::parse($range[1])->format('Y-m-d 23:59');

                $day = Carbon::parse($range[0])->format('Y-m-d');

                if ($range[0] === $range[1]) {
                    $orders->whereDate('created_at', $day);
                    $statusesQuery->whereDate('created_at', $day);
                } else {
                    $orders->whereBetween('created_at', [$range[0], $range[1]]);
                    $statusesQuery->whereBetween('created_at', [$range[0], $range[1]]);
                }
            } else {
                $orders->whereDate('created_at', Carbon::today()->format('Y-m-d'));
                $statusesQuery->whereDate('created_at', Carbon::today()->format('Y-m-d'));
            }

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

            if ($request->input('status')) {
                $orders->where('latest_order_status_id', $request->input('status'));
            }

            $orders = $orders->orderByDesc('created_at')->paginate(10);

            $statuses = $statusesQuery->get()->countBy(fn ($order) => $order->latest_order_status_id)->all();

            return view('admin.orders.index')->with([
                'orders' => $orders,
                'awaiting' => $statuses[1] ?? 0,
                'confirmed' => $statuses[2] ?? 0,
                'supplying' => $statuses[3] ?? 0,
                'servicePoint' => $statuses[4] ?? 0,
                'delivered' => $statuses[5] ?? 0,
                'orderStatuses' => $orderStatuses,
                'translations' => $translations
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin orders index', ['e' => $e]);

            return back()->with('error', 'Error ocurred');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function statusHistory(Request $request, string $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            $orderHistory = $order->statusHistory;

            return view('admin.orders.status-history')->with([
                'order' => $order,
                'orderHistory' => $orderHistory,
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin orders status history', ['e' => $e]);

            return back()->with('error', 'Error ocurred');
        }
    }

    public function orderDetails(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

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

            $artesResponse = null;

            if ($order->legalRegistration && $order->legalRegistration->approved_by_erp == 'pending') {
                $artesResponse = $order->checkLegalRegistrationState();
            }

            return view('admin.orders.order-details')->with([
                'order' => $order,
                'artesResponse' => $artesResponse
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin orders details', ['e' => $e]);

            return redirect()->route('panel')->with('error', 'Error occured');
        }
    }

    public function cancelPayment(Request $request, $paymentId)
    {
        try {
            $payment = OrderPayment::approved(false)->where('id', $paymentId)->first();

            if (!$payment) {
                return back()->with('error', __('app.payment-not-found'));
            }

            $payment->delete();

            return back()->with('success', __('app.payment-cancelled'));
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin orders cancel payment', ['e' => $e]);
            return back()->with('error',  __('app.error-occured'));
        }
    }

    public function rejectNotaryDocument(Request $request, $orderId)
    {
        try {
            $order = Order::with('salesAgreement')->findOrFail($orderId);

            if (!$order || !$order->salesAgreement) {
                return redirect()->route('panel')->with('error', 'Order not found.');
            }

            $rejectedDocuments = $request->input('rejected_documents');

            if (!$rejectedDocuments) {
                return redirect()->route('panel')->with('error', 'No documents selected');
            }

            $order->salesAgreement->update([
                'notary_document_rejected' => in_array('notary_front', $rejectedDocuments),
                'notary_document_back_rejected' => in_array('notary_back', $rejectedDocuments),
                'front_side_id_rejected' => in_array('front_side_id', $rejectedDocuments),
                'notary_document_rejection_reason' => $request->input('rejection_reason')
            ]);

            return back()->with('success', __('app.document-was-rejected'));
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin orders reject notary document', ['e' => $e]);
            return back()->with('error',  __('app.error-occured'));
        }
    }

    public function bondPaymentsList(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            if (!$order || !$order->isSalesAgreementOrder()) {
                return redirect()->route('panel')->with('error', 'Order not found.');
            }

            $paymentsList = [];

            if ($order->salesAgreement) {
                $paymentsList = $order->salesAgreement->ebonds;
            }

            return view('admin.orders.bond-payments-list')->with([
                'order' => $order,
                'paymentsList' => $paymentsList ?? []
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin orders details', ['e' => $e]);

            return redirect()->route('panel')->with('error', 'Error occured');
        }
    }
}
