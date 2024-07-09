<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTechnicalSpecification extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['language_key'];

    public function getLanguageKeyAttribute(): string
    {
        return Language::KEYS[$this->lang_id];
    }
}
