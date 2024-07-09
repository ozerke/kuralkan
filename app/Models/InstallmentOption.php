<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstallmentOption extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function downPayment(): BelongsTo
    {
        return $this->belongsTo(DownPayment::class, 'down_payment_id', 'id');
    }
}
