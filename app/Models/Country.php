<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\App;

class Country extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $with = ['currentTranslation'];

    public function translations(): HasMany
    {
        return $this->hasMany(CountryTranslation::class, 'country_id', 'id');
    }

    public function currentTranslation(): HasOne
    {
        $locale = App::getLocale();
        $langId = Language::AVAILABLE[$locale];

        return $this->hasOne(CountryTranslation::class)
            ->where(function ($query) use ($langId) {
                $query->where('lang_id', $langId)
                    ->orWhereNotNull('lang_id');
            })
            ->orderByRaw("CASE WHEN lang_id = ? THEN 1 ELSE 2 END", [$langId]);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'country_id', 'id');
    }
}
