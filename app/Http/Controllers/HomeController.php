<?php

namespace App\Http\Controllers;

use App\Contracts\CacheServiceInterface;
use App\Models\Category;
use App\Models\City;
use App\Models\Configuration;
use App\Models\HeroSlide;
use App\Models\Product;
use App\Models\User;
use App\Services\CacheService;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    protected $cacheService;

    public function __construct(CacheServiceInterface $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locale = App::getLocale();

        $data = $this->cacheService->get("home.data.{$locale}", function () use ($locale) {
            $slides = HeroSlide::getSlidesByLanguage();

            $newProducts = Product::with('firstDisplayableVariation.firstMedia')->displayable()->newProducts()->get();

            $homeCategories = Category::getCategoriesBySlugs(['motosiklet', 'scooter'], true);

            $motorcyclesSlugs = ['motorcycle', 'motosiklet'];
            $scooterSlugs = ['scooter'];

            $motorcycles = $homeCategories->whereIn('currentTranslation.slug', $motorcyclesSlugs)->first();
            $motorcycles = $motorcycles ? $motorcycles->displayableProducts : [];

            $scooters = $homeCategories->whereIn('currentTranslation.slug', $scooterSlugs)->first();
            $scooters = $scooters ? $scooters->displayableProducts : [];

            $categoriesData = [
                'motorcycles' => $motorcycles,
                'scooters' => $scooters
            ];

            $categories = Category::whereHas('displayableProducts')->orderBy('display_order')->get();

            $meta = Configuration::whereIn('key', [Configuration::KEYS['home_title'], Configuration::KEYS['home_description'], Configuration::KEYS['home_keywords']])->get();

            $homeTitle = $meta->where('key', Configuration::KEYS['home_title'])->first();
            $homeDesc = $meta->where('key', Configuration::KEYS['home_description'])->first();
            $homeKeywords = $meta->where('key', Configuration::KEYS['home_keywords'])->first();

            $homeMeta = [
                'homeTitle' => $homeTitle ? $homeTitle->value : null,
                'homeDesc' => $homeDesc ? $homeDesc->value : null,
                'homeKeywords' => $homeKeywords ? $homeKeywords->value : null,
            ];

            return [
                'newProducts' => $newProducts,
                'motorcycles' => $categoriesData['motorcycles'],
                'scooters' => $categoriesData['scooters'],
                'categories' => $categories,
                'slides' => $slides,
                'homeTitle' => $homeMeta['homeTitle'],
                'homeDesc' => $homeMeta['homeDesc'],
                'homeKeywords' => $homeMeta['homeKeywords'],
            ];
        }, CacheService::ONE_HOUR);

        return view('home.index')->with([
            'newProducts' => $data['newProducts'],
            'motorcycles' => $data['motorcycles'],
            'scooters' => $data['scooters'],
            'categories' => $data['categories'],
            'slides' => $data['slides'],
            'homeTitle' => $data['homeTitle'],
            'homeDesc' => $data['homeDesc'],
            'homeKeywords' => $data['homeKeywords'],
        ]);
    }

    public function salesPoints(Request $request)
    {
        $cityRequest = $request->input('city');
        $districtRequest = $request->input('district');

        $data = $this->cacheService->get('salesPoint.page.data.' . $cityRequest . '.' . $districtRequest, function () use ($cityRequest, $districtRequest) {
            $cities = City::with('districtsWithSalesPoints')->whereHas('districtsWithSalesPoints')->get();

            if ($cityRequest) {
                $city = City::whereHas('districtsWithSalesPoints')->with('districtsWithSalesPoints')->findOrFail($cityRequest);
            } else {
                $city = $cities->first();
            }

            $districts = $city->districtsWithSalesPoints;

            if ($districtRequest) {
                $salesPoints = User::with('district.city')->active()->shops()->where('district_id', $districtRequest)->orderBy('site_user_name')->get();
            } else {
                $salesPoints = User::with('district.city')->active()->shops()->whereHas('district', function ($q) use ($city) {
                    $q->where('city_id', $city->id);
                })->orderBy('site_user_name')->get();
            }

            return [
                'cities' => $cities,
                'districts' => $districts,
                'salesPoints' => $salesPoints,
                'cityId' => $city->id,
                'districtId' => $districtRequest
            ];
        }, CacheService::ONE_DAY);

        return view('home.sales-points')->with($data);
    }

    public function servicePoints(Request $request)
    {
        $cityRequest = $request->input('city');
        $districtRequest = $request->input('district');

        $data = $this->cacheService->get('servicePoints.page.data.' . $cityRequest . '.' . $districtRequest, function () use ($cityRequest, $districtRequest) {
            $cities = City::with('districtsWithServicePoints')->whereHas('districtsWithServicePoints')->get();

            if ($cityRequest) {
                $city = City::whereHas('districtsWithServicePoints')->with('districtsWithServicePoints')->findOrFail($cityRequest);
            } else {
                $city = $cities->first();
            }

            $districts = $city->districtsWithServicePoints;

            if ($districtRequest) {
                $servicePoints = User::with('district.city')->active()->services()->where('district_id', $districtRequest)->orderBy('site_user_name')->get();
            } else {
                $servicePoints = User::with('district.city')->active()->services()->whereHas('district', function ($q) use ($city) {
                    $q->where('city_id', $city->id);
                })->orderBy('site_user_name')->get();
            }

            return [
                'cities' => $cities,
                'districts' => $districts,
                'servicePoints' => $servicePoints,
                'cityId' => $city->id,
                'districtId' => $districtRequest
            ];
        }, CacheService::ONE_DAY);

        return view('home.service-points')->with($data);
    }

    public function getDistrictsForSales(Request $request)
    {
        $cities = City::all();
        $city = City::findOrFail($request->input('city'));

        if (!$city) {
            $city = $cities->first();
        }

        $districts = $city->districts;
        $firstDistrict = $districts->first();
        $salesPoints = User::active()->shops()->where('district_id', $firstDistrict->id)->get();

        return view('home.sales-points')->with([
            'cities' => $cities,
            'districts' => $districts,
            'cityId' => $city->id,
            'salesPoints' => $salesPoints
        ]);
    }

    public function getDistrictsForServices(Request $request)
    {
        $cities = City::all();
        $city = City::findOrFail($request->input('city'));
        $districts = $city->districts;

        return view('home.service-points')->with([
            'cities' => $cities,
            'districts' => $districts,
            'cityId' => $city->id
        ]);
    }

    public function musteriIletisimFormu(Request $request)
    {
        return view('home.iframe');
    }


    public function iletisim()
    {
        return view('home.static.iletisim')->with([
            'homeTitle' => 'İletişim | Ekuralkan.com',
            'homeDesc' => 'Ekuralkan.com ile iletişime geçin. Sorularınız, önerileriniz veya destek talepleriniz için bize ulaşın. Hızlı ve etkili müşteri hizmetleri için buradayız'
        ]);
    }

    public function teslimatKosullari()
    {
        return view('home.static.teslimat-kosullari')->with([
            'homeTitle' => 'Teslimat Koşulları | Ekuralkan.com',
            'homeDesc' => 'Ekuralkan.com teslimat süreçleri, kargo seçenekleri ve ücretlendirme hakkında bilgi edinin. Siparişinizin size nasıl ulaşacağını öğrenin.'
        ]);
    }

    public function garantiVeIadeKosullari()
    {
        return view('home.static.garanti-ve-iade-kosullari')->with([
            'homeTitle' => 'Garanti ve İade Koşulları | Ekuralkan.com',
            'homeDesc' => 'Ekuralkan.com garanti ve iade politikamızı öğrenin. Ürün garantileri, iade süreçleri ve müşteri haklarınız hakkında detaylı bilgi edinin.'
        ]);
    }

    public function gizlilikVeGuvenlik()
    {
        return view('home.static.gizlilik-ve-guvenlik')->with([
            'homeTitle' => 'Gizlilik ve Ödeme Güvenliği | Ekuralkan.com',
            'homeDesc' => 'Kişisel bilgilerinizin ve ödemelerinizin güvenliği bizim önceliğimizdir. Gizlilik politikamız ve güvenli ödeme yöntemlerimiz hakkında bilgi edinin.'
        ]);
    }

    public function hakkimizda()
    {
        return view('home.static.hakkimizda');
    }

    public function uyelikSozlesmesi()
    {
        return view('home.static.uyelik-sozlesmesi')->with([
            'homeTitle' => 'Üyelik ve Kullanım Şartları | Ekuralkan.com',
            'homeDesc' => 'Ekuralkan.com üyelik koşulları ve site kullanım şartlarımızı inceleyin. Hizmetlerimizi kullanırken haklarınızı ve sorumluluklarınızı öğrenin.'
        ]);
    }

    public function sikcaSorulanSorular()
    {
        return view('home.static.sikca-sorulan-sorular')->with([
            'homeTitle' => 'Sıkça Sorulan Sorular (SSS) | Ekuralkan.com',
            'homeDesc' => 'Ekuralkan.com hakkında en çok sorulan soruların yanıtlarını bulun. Hizmetler, ürünler ve kullanım şartlarıyla ilgili bilgiler için SSS sayfamızı ziyaret edin.'
        ]);
    }

    public function newsletterSubmit(Request $request)
    {
        try {
            return back()->with('success', __('app.newsletter-submitted'));
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Newsletter submit', ['e' => $e]);

            return back()->with('error', __('app.error-occured'));
        }
    }
}
