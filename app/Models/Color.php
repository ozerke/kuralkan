<?php

namespace App\Models;

use App\Contracts\CacheServiceInterface;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;
use App\Utils\StorageUtils;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Color extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $with = ['currentTranslation'];

    const IMAGE_UPLOAD_DIRECTORY = 'colors';

    public function translations(): HasMany
    {
        return $this->hasMany(ColorTranslation::class, 'color_id', 'id');
    }

    public function currentTranslation(): HasOne
    {
        $locale = App::getLocale();
        $langId = Language::AVAILABLE[$locale];

        return $this->hasOne(ColorTranslation::class)
            ->where(function ($query) use ($langId) {
                $query->where('lang_id', $langId)
                    ->orWhereNotNull('lang_id');
            })
            ->orderByRaw("CASE WHEN lang_id = ? THEN 1 ELSE 2 END", [$langId]);
    }

    public function getTranslationByKey($key = 'tr')
    {
        $cacheService = app(CacheServiceInterface::class);

        return $cacheService->get("color.translation.{$key}.{$this->id}", function () use ($key) {
            return $this->translations()->where('lang_id', Language::AVAILABLE[$key])->first();
        }, CacheService::ONE_HOUR);
    }

    public function getColorImageUrlAttribute(): ?string
    {
        if ($this->color_image) {
            return StorageUtils::getPictureUrl(self::IMAGE_UPLOAD_DIRECTORY, $this->color_image);
        }

        return null;
    }

    public function deleteColorImage(): void
    {
        if ($this->color_image) {
            StorageUtils::deletePhoto(self::IMAGE_UPLOAD_DIRECTORY, $this->color_image);
        }
    }

    public static function createNonExistingColor(array $data)
    {
        $color = Color::create([
            'color_code' => $data['color_code'],
            'erp_color_name' => $data['color'],
            'color_image' => ''
        ]);

        $color->translations()->create([
            'lang_id' => Language::AVAILABLE['tr'],
            'color_name' => $data['color'],
        ]);

        return $color;
    }
}
