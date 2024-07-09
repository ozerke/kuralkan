<?php

namespace App\Models;

use App\Contracts\CacheServiceInterface;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $with = ['currentTranslation:product_id,lang_id,product_name,slug', 'firstDisplayableVariation'];

    protected $appends = ['category_ids'];

    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class, 'product_id', 'id');
    }

    public function currentTranslation(): HasOne
    {
        $locale = App::getLocale();
        $langId = Language::AVAILABLE[$locale];

        return $this->hasOne(ProductTranslation::class)
            ->where(function ($query) use ($langId) {
                $query->where('lang_id', $langId)
                    ->orWhereNotNull('lang_id');
            })
            ->orderByRaw("CASE WHEN lang_id = ? THEN 1 ELSE 2 END", [$langId]);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'product_id', 'id');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'product_id', 'id')->orderBy('display_order');
    }

    public function displayableVariations(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'product_id', 'id')->with(['media', 'color'])->orderBy('display_order')->where('display', 't');
    }

    public function firstDisplayableVariation(): HasOne
    {
        return $this->hasOne(ProductVariation::class, 'product_id', 'id')->orderBy('display_order')->where('display', 't');
    }

    public function currency(): HasOne
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    public function country(): HasOne
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function breadcrumbCategory(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'bread_crumb_category_id');
    }

    public function specifications(): HasMany
    {
        return $this->hasMany(ProductTechnicalSpecification::class, 'product_id', 'id');
    }

    public function downPayments(): HasMany
    {
        return $this->hasMany(DownPayment::class, 'product_id', 'id');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'product_id', 'id');
    }

    public function orderedSpecifications(): HasMany
    {
        $locale = App::getLocale();
        $langId = Language::AVAILABLE[$locale];

        return $this->hasMany(ProductTechnicalSpecification::class, 'product_id', 'id')->where('lang_id', $langId)->orderBy('display_order');
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class, 'product_id', 'id');
    }

    public function getTranslation(string $locale): ?ProductTranslation
    {
        $langId = Language::AVAILABLE[$locale];

        return $this->translations()->where('lang_id', $langId)->first();
    }

    public function getCategoryIdsAttribute()
    {
        $cacheService = app(CacheServiceInterface::class);

        return $cacheService->get("category_ids.{$this->id}", function () {
            return $this->categories()->pluck('category_id');
        }, CacheService::ONE_HOUR);
    }

    public function getTotalStock()
    {
        return $this->variations()->sum('total_stock');
    }

    public function getFirstMediaAttribute(): ?ProductMedia
    {
        $cacheService = app(CacheServiceInterface::class);

        return $cacheService->get("firstMedia.{$this->id}", function () {
            return $this->media()->orderBy('display_order')->first();
        }, CacheService::ONE_HOUR);
    }

    public function getInputMediaProps()
    {
        if ($this->media->count() < 1) return [];

        $media = $this->media()->orderBy('display_order')->get();

        return [
            'initialPreview' => $media->map(fn ($item) => $item->photo_url)->toArray(),
            'initialPreviewAsData' => true,
            'initialPreviewConfig' => $media->map(function ($item, $key) {
                if ($item->video != '') {
                    return ['caption' => $item->media, 'downloadUrl' => $item->photo_url, 'key' => $key + 1, 'type' => "video", 'filetype' => $item->video];
                }

                return ['caption' => $item->media, 'downloadUrl' => $item->photo_url, 'key' => $key + 1, 'type' => "image"];
            })->toArray(),
        ];
    }

    public function scopeNewProducts($query)
    {
        return $query->where('new_product', 'Y');
    }

    public function scopeDisplayable($query)
    {
        return $query->where('display', 't');
    }

    public function detailsUrl()
    {
        $cacheService = app(CacheServiceInterface::class);

        return $cacheService->get("details_url.{$this->id}", function () {
            if (!$this->currentTranslation->slug) {
                return "#";
            }

            return url(route('item-by-slug', $this->currentTranslation->slug));
        }, CacheService::ONE_HOUR);
    }

    public function getImageUrl()
    {
        $cacheService = app(CacheServiceInterface::class);

        return $cacheService->get("image_url.{$this->id}", function () {
            if ($this->firstDisplayableVariation->firstMedia)
                return $this->firstDisplayableVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png');

            return URL::asset('build/images/kuralkanlogo-white.png');
        }, CacheService::ONE_HOUR);
    }

    public function getVariationsSaleData($withDetails = false)
    {
        $cacheService = app(CacheServiceInterface::class);

        return $cacheService->get("variation_sale_data.{$this->id}.{$withDetails}", function () use ($withDetails) {
            if ($this->displayableVariations) {
                if ($withDetails) {
                    return $this->displayableVariations->map(function ($variation) {
                        return [
                            'variation' => $variation->id,
                            'urls' => $variation->getMediaUrls(),
                            'price' => "₺" . number_format($variation->vat_price, 2, ',', '.'),
                            'delivery_date' => $variation->in_stock ? $variation->getEstimatedDeliveryDate() : __('web.out-of-stock'),
                            'in_stock' => $variation->in_stock,
                            'color' => $variation->color->currentTranslation->color_name,
                            'color_image' => $variation->color->color_image_url
                        ];
                    });
                }

                return $this->displayableVariations->map(function ($variation) {
                    return [
                        'variation' => $variation->id,
                        'urls' => $variation->getMediaUrls(),
                        'price' => "₺" . number_format($variation->vat_price, 2, ',', '.'),
                        'delivery_date' => $variation->in_stock ? $variation->getEstimatedDeliveryDate() : __('web.out-of-stock'),
                        'in_stock' => $variation->in_stock
                    ];
                });
            }

            return [];
        }, CacheService::TEN_MINUTES);
    }

    public function getVariationsBasicData()
    {
        $cacheService = app(CacheServiceInterface::class);

        return $cacheService->get("variation_basic_data.{$this->id}", function () {
            if ($this->displayableVariations) {
                return $this->displayableVariations->map(function ($variation) {
                    return [
                        'key' => $variation->variant_key,
                        'price' => "₺" . number_format($variation->vat_price, 2, ',', '.'),
                        'delivery_date' => $variation->in_stock ? $variation->getEstimatedDeliveryDate() : __('web.out-of-stock'),
                        'in_stock' => $variation->in_stock,
                        'color' => $variation->color->currentTranslation->color_name,
                        'color' => $variation->color->color_code,
                    ];
                });
            }

            return [];
        }, CacheService::TEN_MINUTES);
    }

    public function updateVisibilityByVariations()
    {
        $hasDisplayable = $this->variations()->where('display', 't')->exists();

        if (!$hasDisplayable) {
            $this->update([
                'display' => 'f'
            ]);
        }
    }

    public static function createNonExistingProduct(array $data)
    {
        $title = explode(' ', $data['title']);

        $product = Product::create([
            'stock_code' => $data['stock_code'],
            'currency_id' => 1,
            'display' => 'f',
            'country_id' => 1,
            'display_order' => 0,
            'seo_no_index' => 'noindex',
            'seo_no_follow' => '',
            'new_product' => 'N',
            'featured_product' => 'N',
            'brand_name' => $title[0]
        ]);

        $product->translations()->create([
            'lang_id' => Language::AVAILABLE['tr'],
            'product_name' => $data['title'],
            'description' => '',
            'short_description' => '',
            'seo_title' => '',
            'seo_desc' => '',
            'seo_keywords' => '',
            'delivery_info' => '',
            'faq' => '',
            'slug' => '',
            'documents' => ''
        ]);

        return $product;
    }

    public function hasNewVariations()
    {
        return $this->variations()->where('is_notifiable', true)->exists();
    }

    public function getLowestInstallmentOption()
    {
        if (count($this->downPayments) < 1) return null;

        $maxDownPayment = $this->downPayments()->min('amount');

        $downPayment = $this->downPayments()->where('amount', $maxDownPayment)->first();

        $maxInstallments = $downPayment->installmentOptions()->max('installments');

        $installment = $downPayment->installmentOptions()->where('installments', $maxInstallments)->first();

        return $installment;
    }
}
