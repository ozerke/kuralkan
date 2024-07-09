<?php

namespace App\Models;

use App\Contracts\CacheServiceInterface;
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Jackiedo\Cart\Contracts\UseCartable;
use Jackiedo\Cart\Traits\CanUseCart;

class ProductVariation extends Model implements UseCartable
{
    use HasFactory, CanUseCart;

    protected $guarded = ['id'];

    protected $appends = ['vat_price', 'in_stock'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProductVariationMedia::class, 'product_variation_id', 'id');
    }

    public function firstMedia(): HasOne
    {
        return $this->hasOne(ProductVariationMedia::class, 'product_variation_id', 'id')->orderBy('display_order');
    }

    public function color(): HasOne
    {
        return $this->hasOne(Color::class, 'id', 'color_id');
    }

    public function shopStocks(): HasMany
    {
        return $this->hasMany(ShopStock::class, 'product_variation_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'product_variation_id', 'id');
    }

    public function getVatPriceAttribute()
    {
        if (!$this->vat_ratio) return $this->price;

        $vatCoef = ($this->vat_ratio / 100) + 1;

        return round($this->price * $vatCoef);
    }

    public function getOtvRatio()
    {
        $otvRatio = (float) $this->otv_ratio / 100;

        return $otvRatio + 1;
    }

    public function getInStockAttribute()
    {
        return $this->total_stock > 0;
    }

    public function getMediaUrls()
    {
        $cacheService = app(CacheServiceInterface::class);

        return $cacheService->get("variation-media-urls.{$this->id}", function () {
            if ($this->media) {
                return $this->media->pluck('photo_url')->toArray();
            }

            return [];
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

    public function getEstimatedDeliveryDate($isConsignedProduct = false)
    {
        if ($isConsignedProduct) {
            $today = Carbon::today();
            return $today->addWeekdays(2)->format('d-m-Y');
        }

        $date = $this->estimated_delivery_date;

        if (!$date) {
            $date = Carbon::today();
        } else {
            $date = Carbon::parse($date);
        }

        $today = Carbon::today();

        if ($today > $date) {
            return $today->addWeekdays(5)->format('d-m-Y');
        }

        return $date->format('d-m-Y');
    }

    public static function createNonExistingVariation(array $data, $productId, $colorId)
    {
        $date = $data['estimated_delivery_date'];

        if ($date) {
            $date = explode('-', $date);
            $date = $date[2] . '-' . $date[1] . '-' . $date[0];
        }

        $variation = ProductVariation::create([
            'product_id' => $productId,
            'color_id' => $colorId,
            'display' => 'f',
            'display_order' => 0,
            'barcode' => null,
            'total_stock' => $data['total_stock'],
            'estimated_delivery_date' => $date,
            'price' => $data['price'],
            'vat_ratio' => $data['vat'],
            'otv_ratio' => $data['otv'],
            'discount' => 0,
            'discount_type' => '',
            'variant_key' => $data['variant_key'],
            'is_notifiable' => true
        ]);

        return $variation;
    }

    public function getDocumentTitle()
    {
        $titleTr = $this->product->translations()->where('lang_id', 1)->first();
        $colorTr = $this->color->translations()->where('lang_id', 1)->first();

        return $titleTr->product_name . " " . $colorTr->color_name;
    }
}
