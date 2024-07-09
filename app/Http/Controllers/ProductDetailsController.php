<?php

namespace App\Http\Controllers;

use App\Contracts\CacheServiceInterface;
use App\Models\Bank;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Configuration;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\ProductVariation;
use App\Models\User;
use App\Services\CacheService;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Http\Request;
use Jackiedo\Cart\Facades\Cart;

class ProductDetailsController extends Controller
{
    protected $cacheService;

    public function __construct(CacheServiceInterface $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function resolvePageItemBySlug(Request $request, $slug)
    {
        $product = $this->resolveProductBySlug($slug);

        if ($product) {
            return $this->productDetails($product);
        }

        $category = $this->resolveCategoryBySlug($slug);

        if ($category) {
            return view('home.category.index')->with([
                'category' => $category
            ]);
        }

        return redirect()->route('home')->with('error', __('web.not-found'));
    }

    private function productDetails(Product $product)
    {
        if (!count($product->displayableVariations)) {
            return back()->with('error', 'Product does not have any variations.');
        }

        if ($product->display === 'f') {
            return redirect()->route('home');
        }

        $data = $this->cacheService->get("product_details_data.{$product->id}", function () use ($product) {
            ['banks' => $banks, 'lowestInstallment' => $lowestInstallment] = Bank::getInstallmentRatesForVariation($product->displayableVariations[0]);

            $locale = app()->getLocale();

            $eSalesAgreement = Configuration::getESalesAgreement($locale);

            $campaigns = $product->campaigns()->with('product')->orderBy('down_payment')->get();

            return [
                'product' => $product,
                'countries' => Country::with('translations')->get('id'),
                'banks' => $banks,
                'lowestInstallment' => $lowestInstallment,
                'eSalesAgreement' => $eSalesAgreement ? $eSalesAgreement->value : null,
                'campaigns' => $campaigns
            ];
        }, CacheService::FIVE_MINUTES);

        return view('home.product.details')->with($data);
    }

    private function resolveProductBySlug($slug)
    {
        return $this->cacheService->get("resolveProductBySlug.{$slug}", function () use ($slug) {
            $product = Product::with([
                'firstDisplayableVariation.media',
                'currentTranslation' => function ($query) {
                    $query->select("*");
                },
            ])->whereHas('translations', function ($q) use ($slug) {
                $q->where('slug', $slug);
            })->first();

            return $product;
        }, CacheService::FIVE_MINUTES);
    }

    private function resolveCategoryBySlug($slug)
    {
        return $this->cacheService->get("resolveCategoryBySlug.{$slug}", function () use ($slug) {
            $category = Category::with([
                'products.firstDisplayableVariation.firstMedia',
                'currentTranslation' => function ($query) {
                    $query->select("*");
                },
            ])->whereHas('translations', function ($q) use ($slug) {
                $q->where('slug', $slug);
            })->first();

            return $category;
        }, CacheService::FIVE_MINUTES);
    }

    public function quickBuy(Request $request)
    {
        try {
            $variationId = $request->input('variation');
            $variation = ProductVariation::findOrFail((int)$variationId);

            if (!$variation->in_stock) {
                return back()->with('error', __('web.product-is-out-of-stock'));
            }

            if ($variation->display === 'f' || $variation->product->display === 'f') {
                $cart = Cart::name('current');
                $cart->clearItems();

                return redirect()->route('home');
            }

            $cart = Cart::name('current');

            $cart->clearItems();

            $cart->addItem([
                'model' => $variation,
                'quantity' => 1,
            ]);

            return redirect()->route('cart');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Quick buy', ['e' => $e]);

            return back()->with('error', 'Error occured. Contact the administration.');
        }
    }

    public function getStocksDataForProduct(Request $request)
    {
        try {
            $productId = $request->input('productId');
            $colorId = $request->input('colorId');
            $countryId = $request->input('countryId');
            $cityId = $request->input('cityId');
            $districtId = $request->input('districtId');

            $product = Product::findOrFail($productId);
            $variation = $product->variations()->where('color_id', $colorId)->first();

            $shops = [];

            if ($variation) {
                $shops = User::active()->with(['shopStocks' => function ($q) use ($variation) {
                    $q->where('product_variation_id', $variation->id);
                }, 'district.city'])->whereHas('shopStocks', function ($q) use ($variation) {
                    $q->where('product_variation_id', $variation->id);
                })->get();
            }

            $cities = collect();
            $districts = collect();
            $results = collect();

            if ($countryId) {
                foreach ($shops as $shop) {
                    if (!is_null($shop->district) && $shop->district->city->country_id == $countryId) {
                        $cities->push([
                            'id' => $shop->district->city->id,
                            'title' => $shop->district->city->currentTranslation->city_name
                        ]);

                        if ($cityId && $shop->district->city_id == $cityId) {
                            $districts->push([
                                'id' => $shop->district->id,
                                'title' => $shop->district->currentTranslation->district_name
                            ]);

                            if (!$districtId) {
                                $results->push([
                                    'title' => $shop->site_user_name,
                                    'address' => $shop->address,
                                    'stock' => $shop->shopStocks->first()->stock,
                                    'phone' => $shop->phone,
                                    'map' => $shop->getMapsUrl()
                                ]);
                            } else {
                                if ($shop->district_id == $districtId) {
                                    $results->push([
                                        'title' => $shop->site_user_name,
                                        'address' => $shop->address,
                                        'stock' => $shop->shopStocks->first()->stock,
                                        'phone' => $shop->phone,
                                        'map' => $shop->getMapsUrl()
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            return [
                'cities' => $cities->unique()->sortBy('title')->values()->all(),
                'districts' => $districts->unique()->sortBy('title')->values()->all(),
                'results' => $results->unique()->sortBy('title')->values()->all()
            ];
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Get stocks data for product', ['e' => $e]);
            return [
                'cities' => [],
                'districts' => [],
                'results' => []
            ];
        }
    }

    public function searchPage(Request $request)
    {
        try {
            $searchQuery = $request->input('q');
            $sort = $request->input('sort');

            if (empty($searchQuery)) {
                return view('home.search')->with([
                    'results' => []
                ]);
            }

            $results = ProductTranslation::with('product')->get();

            $results = $results
                ->unique('product_id')
                ->map(fn ($translation) => [
                    'product_id' => $translation->product_id,
                    'title' => $translation->getSearchableTitle(),
                    'fullTitle' => $translation->product->currentTranslation->product_name,
                    'display' => $translation->product->display == 't',
                    'keywords' => $translation->product->currentTranslation->getSearchableKeywords(),
                ])
                ->filter(fn ($translation) => (
                    str_contains($translation['title'], strtolower($searchQuery)) ||
                    str_contains(strtolower($translation['fullTitle']), strtolower($searchQuery)) ||
                    str_contains(strtolower($translation['keywords']), strtolower($searchQuery)))
                    && $translation['display'])
                ->pluck('product_id')
                ->toArray();

            $results = Product::displayable()->find($results)->sortBy('firstDisplayableVariation.price', SORT_REGULAR, $sort == 'desc');

            return view('home.search')->with([
                'results' => $results
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Search page', ['e' => $e]);

            return back()->with('error', 'Error occured. Contact the administration.');
        }
    }
}
