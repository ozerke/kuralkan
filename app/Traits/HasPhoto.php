<?php

namespace App\Traits;

use App\Utils\StorageUtils;

/**
 * Photo trait for manipulating photo field of the parent model
 *
 * The dedicated model, which will use this trait should have defined a class constant PHOTO_UPLOAD_DIRECTORY, as well as a photo column in DB
 *
 */

trait HasPhoto
{
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->media) {
            return StorageUtils::getPictureUrl($this::PHOTO_UPLOAD_DIRECTORY, $this->media);
        }

        return null;
    }

    public function deletePhoto(): void
    {
        if ($this->media) {
            StorageUtils::deletePhoto($this::PHOTO_UPLOAD_DIRECTORY, $this->media);
        }
    }
}
