<?php

namespace App\Models;

use App\Contracts\CacheServiceInterface;
use App\Services\CacheService;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\App;

class Category extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // protected $appends = ['tr', 'en'];

    protected $with = ['currentTranslation:category_id,lang_id,category_name,slug'];

    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class, 'category_id', 'id');
    }

    public function currentTranslation(): HasOne
    {
        $locale = App::getLocale();
        $langId = Language::AVAILABLE[$locale];

        return $this->hasOne(CategoryTranslation::class)
            ->where(function ($query) use ($langId) {
                $query->where('lang_id', $langId)
                    ->orWhereNotNull('lang_id');
            })
            ->orderByRaw("CASE WHEN lang_id = ? THEN 1 ELSE 2 END", [$langId]);
    }

    public function productCategories(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'category_id', 'id');
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, ProductCategory::class, 'category_id', 'id', 'id', 'product_id');
    }

    public function displayableProducts()
    {
        return $this->hasManyThrough(Product::class, ProductCategory::class, 'category_id', 'id', 'id', 'product_id')->where('display', 't');
    }

    public function firstProduct()
    {
        return $this->hasManyThrough(Product::class, ProductCategory::class, 'category_id', 'id', 'id', 'product_id')->limit(1);
    }

    public function getNumberOfProducts()
    {
        return $this->productCategories()->count();
    }

    public function getTrAttribute(): ?CategoryTranslation
    {
        $langId = Language::AVAILABLE['tr'];

        $cacheService = app(CacheServiceInterface::class);

        return $cacheService->get("category_tr.{$langId}.{$this->id}", function () use ($langId) {
            return $this->translations()->where('lang_id', $langId)->first();
        }, CacheService::ONE_HOUR);
    }

    public function getEnAttribute(): ?CategoryTranslation
    {
        $langId = Language::AVAILABLE['en'];

        $cacheService = app(CacheServiceInterface::class);

        return $cacheService->get("category_en.{$langId}.{$this->id}", function () use ($langId) {
            return $this->translations()->where('lang_id', $langId)->first();
        }, CacheService::ONE_HOUR);
    }

    public static function getCategoriesBySlugs(array $slugs, $withProducts = false)
    {
        if (empty($slugs)) {
            throw new Exception('No slugs were provided');
        }

        $cacheService = app(CacheServiceInterface::class);

        return $cacheService->get('categories.' . implode(',', $slugs), function () use ($slugs) {
            return self::with('displayableProducts.firstDisplayableVariation.firstMedia')->whereHas('translations', function ($query) use ($slugs) {
                $query->whereIn('slug', $slugs);
            })->get();
        }, CacheService::ONE_HOUR);
    }

    public function getProducts($priceSort = null)
    {
        $cacheService = app(CacheServiceInterface::class);

        return $cacheService->get("category.products.{$priceSort}.{$this->id}", function () use ($priceSort) {
            $products = $this->products;

            if ($priceSort) {
                $products->sortBy(function ($product) {
                    return $product->firstDisplayableVariation->price;
                }, SORT_REGULAR, $priceSort == 'desc');
            }

            return $products;
        }, CacheService::ONE_HOUR);
    }

    public function getFirstProductAttribute()
    {
        $cacheService = app(CacheServiceInterface::class);

        return $cacheService->get("category.first_product.{$this->id}", function () {
            // $firstProductCategory = $this->firstProduct()->first();

            return null;
        }, CacheService::ONE_HOUR);
    }

    public function innerUrl()
    {
        return route('item-by-slug', $this->currentTranslation->slug);
    }
}
