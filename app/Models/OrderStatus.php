<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\App;

class OrderStatus extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $with = ['currentTranslation'];

    const ASSIGNABLE_STATUSES = [
        'order-received' => 1,
        'cancelled' => 6
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(OrderStatusTranslation::class, 'order_status_id', 'id');
    }

    public function currentTranslation(): HasOne
    {
        $locale = App::getLocale();
        $langId = Language::AVAILABLE[$locale];

        return $this->hasOne(OrderStatusTranslation::class)
            ->where(function ($query) use ($langId) {
                $query->where('lang_id', $langId)
                    ->orWhereNotNull('lang_id');
            })
            ->orderByRaw("CASE WHEN lang_id = ? THEN 1 ELSE 2 END", [$langId]);
    }

    public function orderStatusHistory(): HasMany
    {
        return $this->hasMany(Order::class, 'order_status_id', 'id');
    }
}
