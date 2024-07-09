<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\CacheServiceInterface;
use App\Http\Controllers\Controller;
use App\Jobs\Products\TriggerPlansUpdate;
use App\Jobs\UpdateBankInstallmentsJob;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Services\CacheService;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class AdminController extends Controller
{
    protected $cacheService;

    public function __construct(CacheServiceInterface $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Parse date range from request
        $range = $request->input('date-range') ? explode(' - ', $request->input('date-range')) : [Carbon::today()->format('Y-m-d'), Carbon::today()->format('Y-m-d')];
        $startDate = Carbon::parse($range[0])->startOfDay();
        $endDate = Carbon::parse($range[1])->endOfDay();

        // Check if the range is more than a week
        if ($startDate->diffInDays($endDate) > 7) {
            return redirect()->back()->with(['error' => 'The date range should not be more than a week.']);
        }

        // Use start and end date as strings for the query
        $range[0] = $startDate->toDateTimeString();
        $range[1] = $endDate->toDateTimeString();

        $statsData = $this->cacheService->get("admin.stats.data.{$range[0]}.{$range[1]}", function () use ($range) {
            // Query orders with necessary relationships and filters
            $orders = $this->cacheService->get("orders.{$range[0]}.{$range[1]}", function () use ($range) {
                return Order::with([
                    'productVariation' => function ($query) {
                        $query->with(['product.currentTranslation:product_id,product_name', 'color.currentTranslation:color_id,color_name', 'firstMedia']);
                    },
                    'orderPayments',
                    'user',
                    'invoiceUser',
                    'orderCampaign',
                    'salesAgreement',
                    'latestStatusHistory.orderStatus.currentTranslation'
                ])->whereBetween('created_at', [$range[0], $range[1]])
                    ->get();
            }, CacheService::TEN_MINUTES);

            // Load statuses outside the main query and cache them
            $statuses = $this->cacheService->get('order.statuses', function () {
                return OrderStatus::with('currentTranslation')->get();
            }, CacheService::ONE_HOUR);

            // Aggregate data using collections
            $orderAmount = $orders->whereIn('latestStatusHistory.order_status_id', [2, 3, 4, 5])->sum('total_amount');
            $incompleteOrdersAmount = $orders->whereIn('latestStatusHistory.order_status_id', [1])->sum('total_amount');
            $cancelledOrdersAmount = $orders->whereIn('latestStatusHistory.order_status_id', [6])->sum('total_amount');

            $ordersByPaymentType = [
                'values' => [
                    $orders->where('payment_type', 'S')->count(),
                    $orders->where('payment_type', 'H')->count(),
                    $orders->where('payment_type', 'K')->count(),
                ],
                'translations' => [__('app.sales-agreement'), __('app.bank-transfer'), __('app.credit-card-payment')]
            ];

            $ordersByUsers = [
                'customers' => [
                    'label' => __('app.orders-by-customers'),
                    'value' => $orders->where('is_by_shop', false)->count(),
                ],
                'shops' => [
                    'label' => __('app.orders-by-sales-points'),
                    'value' => $orders->where('is_by_shop', true)->count(),
                ],
            ];

            $ordersStatuses = $statuses->map(function ($status) use ($orders) {
                return [
                    'value' => $orders->where('latestStatusHistory.order_status_id', $status->id)->count(),
                    'label' => $status->currentTranslation->status,
                    'color' => $this->getStatusColor($status->id),
                ];
            })->values()->all();

            $topVariations = $orders->groupBy('productVariation.variant_key')->map(function ($items) {
                $variation = $items->first()->productVariation;
                return [
                    'count' => $items->count(),
                    'variation' => $variation,
                    'product' => $variation->product->currentTranslation->product_name,
                    'color' => $variation->color->currentTranslation->color_name,
                    'color_code' => $variation->color->color_code,
                    'firstMedia' => $variation->firstMedia ? $variation->firstMedia->photo_url : URL::asset('build/images/kuralkanlogo-white.png'),
                ];
            });

            $data = [
                'orderAmount' => $orderAmount,
                'incompleteAmount' => $incompleteOrdersAmount,
                'cancelledAmount' => $cancelledOrdersAmount,
                'orderByPayment' => $ordersByPaymentType,
                'ordersByUser' => $ordersByUsers,
                'orderStatuses' => $ordersStatuses,
            ];

            return [
                'data' => $data,
                'topVariations' => $topVariations
            ];
        }, CacheService::ONE_HOUR);


        return view('admin.index')->with($statsData);
    }

    // Helper method to get status color
    private function getStatusColor($statusId)
    {
        $colors = [
            1 => '#FF5733',
            2 => '#4287f5',
            3 => '#a832e4',
            4 => '#7bb541',
            5 => '#f5921d',
            6 => '#4d9e9e',
        ];

        return $colors[$statusId] ?? '#000000';
    }

    public function triggerManualJob(Request $request, $jobName)
    {
        try {
            switch ($jobName) {
                case 'update-installments':
                    dispatch(new UpdateBankInstallmentsJob);
                    break;
                case 'update-payment-plans':
                    dispatch(new TriggerPlansUpdate);
                    break;
                default:
                    return redirect()->route('panel')->with('error', 'Job not found: ' . $jobName);
            }

            return redirect()->route('panel')->with('success', __('web.success'));
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Trigger admin manual job', ['e' => $e]);

            return redirect()->route('panel')->with('success', __('web.success'));
        }
    }
}
