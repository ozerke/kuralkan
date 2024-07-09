<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationCode extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'phone', 'phone');
    }

    public static function generateUniquePin()
    {
        $pin = random_int(1000, 9999);

        $exists = self::where('code', $pin)->exists();

        if ($exists) {
            return self::generateUniquePin();
        }

        return $pin;
    }

    public static function removePinsForPhone($phone)
    {
        self::where('phone', $phone)->delete();
    }
}
