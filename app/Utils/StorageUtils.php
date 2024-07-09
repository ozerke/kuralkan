<?php

namespace App\Utils;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class StorageUtils
{
    static function getPictureUrl(string $folder, ?string $photo): ?string
    {
        if (!$photo) return null;

        if (filter_var($photo, FILTER_VALIDATE_URL)) return $photo;

        return config('app.url') . "/storage/$folder/$photo";
    }

    static function deletePhoto(string $dir, string $photo, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->delete("$dir/$photo");
    }

    static function getExternalFileUrl(string $file_path = null): ?string
    {
        if ($file_path) {
            return url('/') . '/' . $file_path;
        }

        return null;
    }

    static function generateFileName($image, $prefix = '')
    {
        return $prefix . rand(1000, 9999) . Carbon::now()->timestamp . '.' . $image->extension();
    }

    static function getStorageByEnv()
    {
        return app()->environment() == 'local' ? 'local' : 'sftp';
    }
}
