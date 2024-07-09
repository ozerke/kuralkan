<?php

namespace App\Http\Controllers\Customer;

use App\Contracts\CacheServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Orders\SoapSendOrderController;
use App\Http\Controllers\SoapServicesLibController;
use App\Models\Country;
use App\Models\OrderStatus;
use App\Models\VerificationCode;
use App\Services\CacheService;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Utils\SoapUtils;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $cacheService;

    public function __construct(CacheServiceInterface $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the resource.
     */
    public function orders(Request $request)
    {
        $user = auth()->user();
        $ordersQuery = $user->orders();
        $statusQuery = $user->orders();

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
            $ordersQuery->where(function ($q) use ($request) {
                $q->where('order_no', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('product_name', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('total_amount', 'like', '%' . $request->input('search') . '%')
                    ->orWhereHas('deliveryUser', function ($query) use ($request) {
                        $query->where('address', 'like', '%' . $request->input('search') . '%');
                    });
            });

            $statusQuery->where(function ($q) use ($request) {
                $q->where('order_no', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('product_name', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('total_amount', 'like', '%' . $request->input('search') . '%')
                    ->orWhereHas('deliveryUser', function ($query) use ($request) {
                        $query->where('address', 'like', '%' . $request->input('search') . '%');
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

        return view('customer.index')->with([
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
                return view('customer.orders.order-details')->with([
                    'order' => $order,
                    'artesResponse' => $artesResponse
                ]);
            }

            return view('customer.orders.order-details')->with([
                'order' => $order,
                'artesResponse' => null
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Customer Order Details', ['e' => $e]);

            return redirect()->route('panel')->with('error', 'Error occured');
        }
    }

    public function profilePage(Request $request)
    {
        try {
            $information = auth()->user()->getInvoiceInformation();


            $countries = Country::all();
            $cities = $information['country'] ? $information['country']->cities()->get('id') : [];
            $districts = $information['city'] ? $information['city']->districts()->get('id') : [];

            return view('customer.profile')->with([
                'information' => $information,
                'countries' => $countries,
                'cities' => $cities,
                'districts' => $districts,
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Customer Profile Page', ['e' => $e]);

            return redirect()->route('panel')->with('error', 'Error occured');
        }
    }

    public function profileUpdate(Request $request)
    {
        try {
            $user = auth()->user();

            $name = $request->input('name');
            $surname = $request->input('surname');

            $isCompany = $request->input('company') == 'Y';

            if (!$isCompany) {
                if (!$request->input('birth_day') || !$request->input('birth_month') || !$request->input('birth_year')) {
                    return back()->with('error', __('app.birth_date_error'));
                }
            }

            $phone = str_replace(" ", "", $request->input('full_phone'));

            $updatedPhone = auth()->user()->phone;

            if ($phone != $user->phone) {
                $pin = $request->input('pin_code');

                $verification = VerificationCode::where([
                    ['phone', $phone],
                    ['code', $pin]
                ])->exists();

                VerificationCode::removePinsForPhone($phone);

                if (!$verification) {
                    return back()->with('error', __('app.pin-validation-error'));
                }

                $updatedPhone = $phone;
            }

            $birthDay = $request->input('birth_day');
            $birthMonth = $request->input('birth_month');
            $birthYear = $request->input('birth_year');

            $birthdate = $birthYear . '-' . $birthMonth . '-' . $birthDay;

            auth()->user()->update([
                'site_user_name' => $name,
                'site_user_surname' => $surname,
                'fullname' => $name . ' ' . $surname,
                'address' => $request->input('address'),
                'district_id' => $request->input('district'),
                'postal_code' => $request->input('postal_code'),
                'company' => $request->input('company'),
                'company_name' => $request->input('company_name'),
                'national_id' => $request->input('national_id'),
                'tax_id' => $request->input('tax_id'),
                'tax_office' => $request->input('tax_office'),
                'date_of_birth' => $isCompany ? auth()->user()->date_of_birth : $birthdate,
                'phone' => $updatedPhone
            ]);

            return back()->with('success', __('app.profile-updated'));
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Customer Profile Update', ['e' => $e]);

            return back()->with('error', __('app.error-occured'));
        }
    }

    public function paymentPlan(Request $request)
    {
        try {
            $user = auth()->user();

            if (empty($user->national_id)) {
                return redirect()->route('customer.profile')->with('error', __('web.national-id-required'));
            }

            $ebonds = $user->ebonds;

            if (count($ebonds) > 0) {
                return view('customer.payment-plan')->with([
                    'bonds' => $ebonds,
                    'ebonds' => true,
                ]);
            }

            $bondList = $this->cacheService->get('customer.old.bonds.' . $user->id, function () use ($user) {
                $bonds = (new SoapServicesLibController())->checkBondsOfCustomer($user->national_id);
                $list = null;

                if ($bonds) {
                    $list = SoapUtils::parseRow($bonds, function ($item) {
                        return [
                            'fullname' => $item['BORCLUADI'],
                            'bondNo' => $item['SENETNUMARASI'],
                            'dueDate' => $item['SENETODEMEVADESI'],
                            'bondAmount' => $item['SENETTUTARI'],
                            'bankName' => $item['BANKAADI'],
                            'bankBranchName' => $item['SUBE'],
                            'paid' => $item['SENETDURUMU'] === 'Ödendi' ? 'Y' : 'N',
                        ];
                    });
                }

                return $list;
            }, CacheService::ONE_HOUR);

            return view('customer.payment-plan')->with([
                'bonds' => $bondList,
                'ebonds' => false
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Customer Payment Plan', ['e' => $e]);

            return redirect()->route('panel')->with('error', 'Error occured');
        }
    }
}
