<?php

namespace App\Models;

use App\Contracts\CacheServiceInterface;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\App;

class MenuSection extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['title_tr', 'title_en', 'product_ids'];

    protected $with = ['currentTranslation'];

    public function translations(): HasMany
    {
        return $this->hasMany(MenuSectionTranslation::class, 'menu_section_id', 'id');
    }

    public function currentTranslation(): HasOne
    {
        $locale = App::getLocale();
        $langId = Language::AVAILABLE[$locale];

        return $this->hasOne(MenuSectionTranslation::class)
            ->where(function ($query) use ($langId) {
                $query->where('lang_id', $langId)
                    ->orWhereNotNull('lang_id');
            })
            ->orderByRaw("CASE WHEN lang_id = ? THEN 1 ELSE 2 END", [$langId]);
    }

    public function menuSectionItems(): HasMany
    {
        return $this->hasMany(MenuSectionItem::class, 'menu_section_id', 'id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function getTitleTrAttribute()
    {
        $translation = $this->translations()->where('lang_id', 1)->first();

        return $translation ? $translation->title : null;
    }

    public function getTitleEnAttribute()
    {
        $translation = $this->translations()->where('lang_id', 2)->first();

        return $translation ? $translation->title : null;
    }

    public function getProductIdsAttribute()
    {
        return $this->menuSectionItems()->pluck('product_id');
    }

    public static function getSectionsForBrand(string $brand)
    {
        if (!$brand) return [];

        $cacheService = app(CacheServiceInterface::class);

        return $cacheService->get("menu-sections.$brand", function () use ($brand) {
            $sections = self::with(['menuSectionItems.product.firstDisplayableVariation.firstMedia', 'category'])->where('product_brand', $brand)->get();

            $sections = $sections->map(function ($section) {
                $products = $section->menuSectionItems;
                $products = $products->sortBy('display_order');
                $products = $products->pluck('product');
                $products = $products->filter(fn ($item) => $item->display === 't');

                return [
                    'title' => $section->currentTranslation->title,
                    'categoryUrl' => $section->category ? $section->category->innerUrl() : null,
                    'items' => $products
                ];
            });

            return $sections->filter(fn ($section) => count($section['items']) > 0);
        }, CacheService::ONE_HOUR);
    }
}
