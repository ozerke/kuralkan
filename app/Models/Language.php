<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    const AVAILABLE = [
        'tr' => 1,
        'en' => 2
    ];

    const KEYS = [
        1 => 'tr',
        2 => 'en'
    ];
}
