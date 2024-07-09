<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\OrderStatus;
use App\Models\User;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    const SEARCH_FIELDS = ['email', 'site_user_name', 'site_user_surname', 'address', 'erp_user_id', 'erp_email', 'company_name', 'fullname'];
    const PAYMENT_SEARCH_FIELDS = ['payment_amount', 'collected_payment', 'number_of_installments', 'approved_by_erp', 'description', 'payment_ref_no'];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roleQuery = $request->input('filter');

        $usersQuery = User::query();

        if ($request->input('search')) {
            $usersQuery->search($request->input('search'), false);
        }

        $usersQuery = $usersQuery->withoutRole(User::ROLES['admin'])->filteredRoles($roleQuery);

        return view('admin.users.index')->with([
            'users' => $usersQuery->paginate(10),
        ]);
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
        try {
            $user = User::findOrFail($id);

            return view('admin.users.edit')->with([
                'user' => $user,
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin users edit', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
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

    public function shopVariationStocks(Request $request, $id)
    {
        try {
            $shop = User::findOrFail($id);

            $shopStocks = $shop->shopStocks()->with('product', 'variation')->get();

            return view('admin.users.shop-stocks')->with([
                'shop' => $shop,
                'shopStocks' => $shopStocks,
                'totalStock' => $shopStocks->sum('stock')
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin users shop stocks', ['e' => $e]);
            return back()->with('error', 'Could not get the products');
        }
    }

    public function userPayments(Request $request, $id)
    {

        $withBonds = false;

        if ($id == 0) {
            $payments = OrderPayment::query()->with(['order', 'bankAccount.bank', 'user']);
        } else {
            $payments = OrderPayment::query()->with(['order', 'bankAccount.bank', 'user'])->where('user_id', $id);
        }

        if ($request->input('with_bonds')) {
            $withBonds = true;
        }

        if ($request->input('search')) {
            $payments->search($request->input('search'), $request->input('sort_by_weight') == 'on');
        }

        if ($request->input('date-range')) {
            $range = explode(' - ', $request->input('date-range'));

            $range[0] = Carbon::parse($range[0])->format('Y-m-d 00:00');
            $range[1] = Carbon::parse($range[1])->format('Y-m-d 23:59');;

            if ($range[0] === $range[1]) {
                $payments->whereDate('created_at', $range[0]);
            } else {
                $payments->whereBetween('created_at', [$range[0], $range[1]]);
            }
        } else {
            $from = Carbon::today()->addMonths(-1)->format('Y-m-d 00:00');
            $to = Carbon::today()->format('Y-m-d 23:59');

            $payments->whereBetween('created_at', [$from, $to]);
        }

        if ($request->input('payment-type')) {
            $payments->where('payment_type', $request->input('payment-type'));
        }

        if ($request->input('bank')) {
            $payments->whereHas('bankAccount', function ($q) use ($request) {
                $q->whereBankId($request->input('bank'));
            });
        }

        $payments = $payments->orderByDesc('created_at')->paginate(10);

        $banks = Bank::all();

        return view('admin.users.payments')->with([
            'payments' => $payments,
            'banks' => $banks,
            'userId' => $id,
            'bonds' => $withBonds
        ]);
    }

    public function bondPayments(Request $request, $id)
    {

        if ($id == 0) {
            $payments = OrderPayment::query()->with(['order', 'bankAccount.bank', 'user'])->whereNotNull('e_bond_no');
        } else {
            $payments = OrderPayment::query()->with(['order', 'bankAccount.bank', 'user'])->whereNotNull('e_bond_no')->where('user_id', $id);
        }


        if ($request->input('search')) {
            $payments->search($request->input('search'), $request->input('sort_by_weight') == 'on');
        }

        if ($request->input('date-range')) {
            $range = explode(' - ', $request->input('date-range'));

            $range[0] = Carbon::parse($range[0])->format('Y-m-d 00:00');
            $range[1] = Carbon::parse($range[1])->format('Y-m-d 23:59');;

            if ($range[0] === $range[1]) {
                $payments->whereDate('created_at', $range[0]);
            } else {
                $payments->whereBetween('created_at', [$range[0], $range[1]]);
            }
        } else {
            $from = Carbon::today()->addMonths(-1)->format('Y-m-d 00:00');
            $to = Carbon::today()->format('Y-m-d 23:59');

            $payments->whereBetween('created_at', [$from, $to]);
        }

        if ($request->input('payment-type')) {
            $payments->where('payment_type', $request->input('payment-type'));
        }

        if ($request->input('bank')) {
            $payments->whereHas('bankAccount', function ($q) use ($request) {
                $q->whereBankId($request->input('bank'));
            });
        }

        $payments = $payments->orderByDesc('created_at')->paginate(10);

        $banks = Bank::all();

        return view('admin.users.payments')->with([
            'payments' => $payments,
            'banks' => $banks,
            'userId' => $id,
            'bonds' => true
        ]);
    }

    public function orders(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);

            $orders = Order::query()->where(function ($q) use ($userId) {
                $q->where('invoice_user_id', $userId)->orWhere('user_id', $userId);
            })->with(['productVariation.product', 'orderPayments']);

            if ($request->input('date-range')) {
                $range = explode(' - ', $request->input('date-range'));

                $range[0] = Carbon::parse($range[0])->format('Y-m-d 00:00');
                $range[1] = Carbon::parse($range[1])->format('Y-m-d 23:59');;

                if ($range[0] === $range[1]) {
                    $orders->whereDate('created_at', $range[0]);
                } else {
                    $orders->whereBetween('created_at', [$range[0], $range[1]]);
                }
            } else {
                $orders->whereDate('created_at', Carbon::today()->format('Y-m-d'));
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
                $orders->whereHas('statusHistory', function ($q) use ($request) {
                    $q->latest()->take(1)->where('order_status_id', $request->input('status'));
                });
            }

            $orders = $orders->get();

            $statuses = Order::where(function ($q) use ($userId) {
                $q->where('invoice_user_id', $userId)->orWhere('user_id', $userId);
            })->whereHas('statusHistory')->get()->countBy(fn ($order) => optional($order->latest_status)->order_status_id)->all();

            return view('admin.users.orders')->with([
                'orders' => $orders,
                'awaiting' => $statuses[1] ?? 0,
                'confirmed' => $statuses[2] ?? 0,
                'supplying' => $statuses[3] ?? 0,
                'servicePoint' => $statuses[4] ?? 0,
                'delivered' => $statuses[5] ?? 0,
                'orderStatuses' => $orderStatuses,
                'translations' => $translations,
                'user' => $user
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin users orders', ['e' => $e]);

            return back()->with('error', 'Error ocurred');
        }
    }
}
