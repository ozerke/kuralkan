<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Jobs\Orders\ErpOrderJob;
use App\Jobs\SendEmailJob;
use App\Jobs\SendSMSJob;
use App\Mail\RegisteredByShopMail;
use App\Models\ConsignedProduct;
use App\Models\Country;
use App\Models\Order;
use App\Models\User;
use App\Services\IYSService;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Services\SMSTemplateParser;
use App\Utils\PDFDocuments;
use Exception;
use Illuminate\Http\Request;
use Jackiedo\Cart\Facades\Cart;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart = Cart::name('current');

        if (count($cart->getItems()) < 1) {
            return view('home.orders.cart')->with([
                'item' => false
            ]);
        }

        $hash = array_keys($cart->getItems())[0];
        $item = $cart->getItem($hash)->getModel();

        $cartItem = $cart->getItem($hash);

        $extraInfo = $cartItem->getExtraInfo();

        $consignedProductId = $extraInfo['consignedProductId'] ?? null;

        if ($consignedProductId) {
            return view('home.orders.cart')->with([
                'item' => $item,
                'consignedProduct' => ConsignedProduct::findOrFail($consignedProductId)
            ]);
        }

        if ($item->display === 'f' || $item->product->display === 'f') {
            $cart = Cart::name('current');
            $cart->clearItems();

            return redirect()->route('home');
        }

        return view('home.orders.cart')->with([
            'item' => $item
        ]);
    }

    public function clearCart()
    {
        $cart = Cart::name('current');
        $cart->clearItems();

        return view('home.orders.cart')->with([
            'item' => false
        ]);
    }

    public function invoiceInformation(Request $request)
    {
        $cart = Cart::name('current');
        $information = auth()->user()->getInvoiceInformation();

        if (count($cart->getItems()) < 1) {
            return view('home.orders.cart')->with([
                'item' => false
            ]);
        }

        $hash = array_keys($cart->getItems())[0];
        $item = $cart->getItem($hash)->getModel();

        $countries = Country::all();
        $cities = $information['country'] ? $information['country']->cities()->get('id') : [];
        $districts = $information['city'] ? $information['city']->districts()->get('id') : [];

        $deliveryCountry = Country::first();
        $deliveryCities = $deliveryCountry->cities()->whereHas('districtsWithServicePoints')->get('id');
        $deliveryDistricts = [];

        $cartItem = $cart->getItem($hash);
        $extraInfo = $cartItem->getExtraInfo();
        $consignedProductId = $extraInfo['consignedProductId'] ?? null;

        $consignedProduct = null;

        if ($consignedProductId) {
            $consignedProduct = ConsignedProduct::findOrFail($consignedProductId);
        }

        return view('home.orders.invoice-information')->with([
            'item' => $item,
            'information' => $information,
            'countries' => $countries,
            'cities' => $cities,
            'districts' => $districts,
            'deliveryCities' => $deliveryCities,
            'deliveryDistricts' => $deliveryDistricts,
            'consignedProduct' => $consignedProduct
        ]);
    }

    public function submitOrderInformation(Request $request)
    {
        try {
            $cart = Cart::name('current');

            if (count($cart->getItems()) < 1) {
                return view('home.orders.cart')->with([
                    'item' => false
                ]);
            }

            $hash = array_keys($cart->getItems())[0];
            $item = $cart->getItem($hash)->getModel();

            $cartItem = $cart->getItem($hash);
            $extraInfo = $cartItem->getExtraInfo();
            $consignedProductId = $extraInfo['consignedProductId'] ?? null;

            $consignedProduct = null;

            if ($consignedProductId) {
                $consignedProduct = ConsignedProduct::findOrFail($consignedProductId);

                if (!$consignedProduct->in_stock) {
                    return back()->with('error', __('web.product-is-marked-sold'));
                }
            }

            $paymentMethod = $request->input('payment-method');

            if ($paymentMethod == 'bank') {
                $paymentMethod = 'H';
            } else {
                $paymentMethod = 'S';
            }

            $isSalesAgreementOrder = $paymentMethod === 'S';

            $deliveryPoint = User::findOrFail($request->input('delivery_point'));

            if (auth()->user()->isAdmin()) {
                return back()->with('error', __('web.admin-order-error'));
            }

            $isCompany = $request->input('company') == 'Y';

            if (!$isCompany) {
                if (!$request->input('birth_day') || !$request->input('birth_month') || !$request->input('birth_year')) {
                    return back()->with('error', __('app.birth_date_error'));
                }
            }

            if (auth()->user()->isShopOrService()) {
                $password = Str::random(8);

                $email = $request->input('email');

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return back()->with('error', __('validation.email', ['attribute' => 'email']));
                }

                $name = $request->input('name');
                $surname = $request->input('surname');
                $phone = str_replace(" ", "", $request->input('full_phone'));

                $birthDay = $request->input('birth_day');
                $birthMonth = $request->input('birth_month');
                $birthYear = $request->input('birth_year');

                $birthdate = $birthYear . '-' . $birthMonth . '-' . $birthDay;

                $createdUser = User::where('email', $email)->first();

                $nationalId = $request->input('national_id');
                $nationalIdUser = null;

                if (!empty($nationalId)) {
                    $nationalIdUser = User::where('national_id', $nationalId)->first();
                }

                if ($createdUser) {
                    if ($createdUser->isShopOrService()) {
                        return back()->with('error', __('web.errors.invoice-user-is-shop-error'));
                    }

                    if ($nationalIdUser) {
                        if ($nationalIdUser->email !== $createdUser->email) {
                            return back()->with('error', 'OCsOI001-Bir hata oluştu. Lütfen, Müşteri Hizmetleri ile iletişime geçiniz. ');
                        }
                    }

                    $createdUser->update([
                        'site_user_name' => $name,
                        'site_user_surname' => $surname,
                        'address' => $request->input('address'),
                        'district_id' => $request->input('district'),
                        'postal_code' => $request->input('postal_code'),
                        'company' => $request->input('company'),
                        'company_name' => $request->input('company_name'),
                        'national_id' => $isCompany ? $createdUser->national_id : $request->input('national_id'),
                        'tax_id' => $request->input('tax_id'),
                        'tax_office' => $request->input('tax_office'),
                        'date_of_birth' => $isCompany ? $createdUser->date_of_birth : $birthdate,
                        'updated_by' => auth()->user()->id,
                        'fullname' => $name . ' ' . $surname
                    ]);
                } else {
                    $createdUser = User::create([
                        'site_user_name' => $name,
                        'site_user_surname' => $surname,
                        'fullname' => $name . ' ' . $surname,
                        'email' => $email,
                        'phone' => $phone,
                        'password' => $password,
                        'address' => $request->input('address'),
                        'district_id' => $request->input('district'),
                        'postal_code' => $request->input('postal_code'),
                        'company' => $request->input('company'),
                        'company_name' => $request->input('company_name'),
                        'national_id' => $isCompany ? null : $request->input('national_id'),
                        'tax_id' => $request->input('tax_id'),
                        'tax_office' => $request->input('tax_office'),
                        'date_of_birth' => $isCompany ? "1900-01-01" : $birthdate,
                        'registered_by' => auth()->user()->id
                    ]);

                    $createdUser->assignRole(User::ROLES['customer']);

                    $createdUser->updateUserNo();

                    $message = SMSTemplateParser::userLogins($email, $password);

                    IYSService::addIYS($email, $phone);

                    dispatch(new SendSMSJob($phone, $message));

                    dispatch(new SendEmailJob($createdUser->email, new RegisteredByShopMail($createdUser, $password, auth()->user())));
                }

                $order = Order::create([
                    'user_id' => auth()->user()->id,
                    'invoice_user_id' => $createdUser->id,
                    'delivery_user_id' => $request->input('delivery_point'),
                    'product_variation_id' => $item->id,
                    'payment_type' => $isSalesAgreementOrder ? 'S' : null,
                    'product_name' => $item->product->currentTranslation->product_name,
                    'total_amount' => $item->vat_price,
                    'chasis_no' => $consignedProduct ? $consignedProduct->chasis_no : null,
                    'motor_no' => null,
                    'invoice_link' => null,
                    'bank_id' => null,
                    'erp_order_id' => null,
                    'from_stock' => 'N',
                    'order_no' => 'pending',
                    'erp_prefix' => $consignedProduct ? 'SK' : 'SY'
                ]);

                $cart->clearItems();

                if ($isSalesAgreementOrder) {
                    dispatch(new ErpOrderJob($order, $createdUser, $deliveryPoint, auth()->user()));

                    return redirect()->route('order-processing', ['orderNo' => $order->order_no]);
                }

                return redirect()->route('order-payment', ['orderNo' => $order->order_no]);
            } else {
                $name = $request->input('name');
                $surname = $request->input('surname');

                $birthDay = $request->input('birth_day');
                $birthMonth = $request->input('birth_month');
                $birthYear = $request->input('birth_year');

                $birthdate = $birthYear . '-' . $birthMonth . '-' . $birthDay;

                $nationalId = $request->input('national_id');
                $nationalIdUser = null;

                if (!empty($nationalId)) {
                    $nationalIdUser = User::where('national_id', $nationalId)->first();
                }

                if ($nationalIdUser) {
                    if ($nationalIdUser->email !== auth()->user()->email) {
                        return back()->with('error', 'OCsOI002-Bir hata oluştu. Lütfen, Müşteri Hizmetleri ile iletişime geçiniz. ');
                    }
                }

                auth()->user()->update([
                    'site_user_name' => $name,
                    'site_user_surname' => $surname,
                    'address' => $request->input('address'),
                    'district_id' => $request->input('district'),
                    'postal_code' => $request->input('postal_code'),
                    'company' => $request->input('company'),
                    'company_name' => $request->input('company_name'),
                    'national_id' => $isCompany ? auth()->user()->national_id : $request->input('national_id'),
                    'tax_id' => $request->input('tax_id'),
                    'tax_office' => $request->input('tax_office'),
                    'date_of_birth' => $isCompany ? auth()->user()->date_of_birth : $birthdate
                ]);

                $order = Order::create([
                    'user_id' => auth()->user()->id,
                    'invoice_user_id' => auth()->user()->id,
                    'delivery_user_id' => $request->input('delivery_point'),
                    'product_variation_id' => $item->id,
                    'payment_type' => $isSalesAgreementOrder ? 'S' : null,
                    'product_name' => $item->product->currentTranslation->product_name,
                    'total_amount' => $item->vat_price,
                    'chasis_no' => $consignedProduct ? $consignedProduct->chasis_no : null,
                    'motor_no' => null,
                    'invoice_link' => null,
                    'bank_id' => null,
                    'erp_order_id' => null,
                    'from_stock' => 'N',
                    'order_no' => 'pending',
                    'erp_prefix' => $consignedProduct ? 'SK' : 'SY'
                ]);

                $cart->clearItems();

                if ($isSalesAgreementOrder) {
                    dispatch(new ErpOrderJob($order, auth()->user(), $deliveryPoint));

                    return redirect()->route('order-processing', ['orderNo' => $order->order_no]);
                }

                return redirect()->route('order-payment', ['orderNo' => $order->order_no]);
            }
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ApplicationOrdering, 'submitOrderInformation', ['e' => $e]);
            return back()->with('error', 'OCsOI003-Bir hata oluştu. Lütfen, Müşteri Hizmetleri ile iletişime geçiniz. ');
        }
    }

    public function retrieveRemoteSalesPdf(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->orders()->with('productVariation')->where('order_no', $orderNo)->first();

            if (auth()->user()->isAdmin()) {
                $order = Order::with('productVariation')->where('order_no', $orderNo)->first();
            }

            if (!$order) {
                $order = auth()->user()->createdOrders()->with('productVariation')->where('order_no', $orderNo)->first();
                if (!$order) {
                    return redirect()->route('home');
                }
            }

            return PDFDocuments::returnContract($order);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ApplicationOrdering, 'retrieveRemoteSalesPdf', ['e' => $e]);
            return back()->with('error', 'OCrRSP001-Bir hata oluştu. Lütfen, Müşteri Hizmetleri ile iletişime geçiniz. ');
        }
    }

    public function orderProcessingPage(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->orders()->with('productVariation')->where('order_no', $orderNo)->first();

            if (!$order) {
                $order = auth()->user()->createdOrders()->with('productVariation')->where('order_no', $orderNo)->first();
                if (!$order) {
                    return redirect()->route('home');
                }
            }

            return view('home.orders.processing-order', ['order' => $order]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ApplicationOrdering, 'orderProcessingPage', ['e' => $e]);
            return back()->with('error', 'OCoPP001-Bir hata oluştu. Lütfen, Müşteri Hizmetleri ile iletişime geçiniz. ');
        }
    }

    public function checkOrderProcessing(Request $request)
    {
        try {
            $orderNo = $request->input('orderNo');

            if (empty($orderNo)) {
                return response()->json(['status' => false, 'terminated' => true]);
            }

            $order = auth()->user()->orders()->with('productVariation')->where('order_no', $orderNo)->first();

            if (!$order) {
                $order = auth()->user()->createdOrders()->with('productVariation')->where('order_no', $orderNo)->first();
                if (!$order) {
                    return response()->json(['status' => false, 'terminated' => true]);
                }
            }

            if ($order->isCancelled()) {
                return response()->json(['status' => false, 'terminated' => true]);
            }

            if ($order->erp_order_id) {
                return response()->json(['status' => true, 'terminated' => false, 'redirectTo' => route('order-payment', ['orderNo' => $order->order_no])]);
            }

            return response()->json(['status' => false, 'terminated' => false, 'redirectTo' => false]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ApplicationOrdering, 'checkOrderProcessing', ['e' => $e]);
            return response()->json(['status' => false, 'terminated' => true]);
        }
    }
}
