<?php

namespace App\Models;

use App\Utils\StorageUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicFile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const UPLOAD_DIRECTORY = "public_files";

    public function getFileUrlAttribute(): ?string
    {
        if ($this->file) {
            return StorageUtils::getPictureUrl($this::UPLOAD_DIRECTORY, $this->file);
        }

        return null;
    }

    public function deleteFile(): void
    {
        if ($this->file) {
            StorageUtils::deletePhoto($this::UPLOAD_DIRECTORY, $this->file);
        }
    }
}
