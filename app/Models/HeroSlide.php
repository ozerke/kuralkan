<?php

namespace App\Models;

use App\Traits\HasPhoto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class HeroSlide extends Model
{
    use HasFactory, HasPhoto;

    protected $guarded = ['id'];

    const PHOTO_UPLOAD_DIRECTORY = 'hero-slides';

    public function getLanguageName()
    {
        return config('app.locales')[\App\Models\Language::KEYS[$this->lang_id]];
    }

    public static function getSlidesByLanguage()
    {
        $locale = App::getLocale();
        $langId = Language::AVAILABLE[$locale];

        return self::where('lang_id', $langId)->orderBy('display_order')->get();
    }
}
