<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function orderStatus(): HasOne
    {
        return $this->hasOne(OrderStatus::class, 'id', 'order_status_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}
