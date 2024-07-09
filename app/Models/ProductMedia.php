<?php

namespace App\Models;

use App\Traits\HasPhoto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    use HasFactory, HasPhoto;

    protected $guarded = ['id'];

    const PHOTO_UPLOAD_DIRECTORY = 'products';
}
