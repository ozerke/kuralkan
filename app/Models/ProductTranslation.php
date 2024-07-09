<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTranslation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getNameWithoutBrand($brand)
    {
        if (!$brand) return $this->product_name;

        if (!str_contains($this->product_name, $brand)) {
            return $this->product_name;
        }

        $name = explode($brand, $this->product_name);

        return $name[1];
    }

    public function getSearchableTitle()
    {
        $title = preg_replace("/[^a-zA-Z0-9]/i", "", $this->product_name);

        return strtolower($title);
    }

    public function getSearchableKeywords()
    {
        $keywords = preg_replace("/[^a-zA-Z0-9]/i", "", $this->seo_keywords);

        return strtolower($keywords);
    }
}
