<?php

namespace App\Models;

use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Utils\StorageUtils;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class LegalRegistration extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public const UPLOAD_DIRECTORY = 'webartes';

    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function getLink($docName)
    {
        if (!$docName) return '#';

        return config('app.url') . '/api/legal-docs/' . $docName;
    }

    public static function getPhotoLink($docName)
    {
        if (!$docName) return '';

        return config('app.url') . '/api/legal-docs/' . $docName;
    }

    public static function deleteByName($documentName): void
    {
        if ($documentName) {
            Storage::disk(StorageUtils::getStorageByEnv())->delete(self::UPLOAD_DIRECTORY . '/' . $documentName);
        }
    }

    public function deleteAllDocuments(): void
    {
        $fields = [
            'id_card_front',
            'id_card_back',
            'signature_circular',
            'operating_certificate',
            'registry_gazzete',
            'circular_indentity_front',
            'circular_indentity_back',
            'power_of_attorney',
        ];

        foreach ($fields as $field) {
            if ($this->$field) {
                Storage::disk(StorageUtils::getStorageByEnv())->delete(self::UPLOAD_DIRECTORY . '/' . $this->$field);
            }
        }
    }

    public static function returnDocument($document)
    {
        $disk = Storage::disk(StorageUtils::getStorageByEnv());
        $path = self::UPLOAD_DIRECTORY . '/' . $document;

        if (!$disk->exists($path)) {
            abort(404, 'Document not found.');
        }

        try {
            $fileContents = $disk->get($path);
            $mimeType = $disk->mimeType($path);

            $response = Response::make($fileContents, 200);
            $response->header('Content-Type', $mimeType);

            return $response;
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'returnDocument', ['e' => $e]);
            abort(404);
        }
    }

    public static function uploadDocument($documentFile, $chasisNo, $prefix)
    {
        $documentName = $chasisNo . '_' . $prefix . '.' . $documentFile->extension();

        $path = Storage::disk(StorageUtils::getStorageByEnv())->putFileAs(self::UPLOAD_DIRECTORY, $documentFile, $documentName);

        if (!$path) {
            return false;
        }

        return $documentName;
    }

    public function parseParams(): array
    {
        if (empty($this->params)) return [];

        return explode(',', $this->params);
    }
}
