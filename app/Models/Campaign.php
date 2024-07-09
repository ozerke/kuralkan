<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function getDownPaymentAmount($order = null, $format = true)
    {
        $price = $order ? $order->total_amount : $this->product->displayableVariations[0]->vat_price;

        $amount = $price * ($this->down_payment / 100);

        if ($format) {
            $amount = number_format($amount, 0, ',', '.');
        }

        return $amount;
    }
}
