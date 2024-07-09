<?php

namespace App\Models;

use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Utils\StorageUtils;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class SalesAgreement extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const DOCUMENT_UPLOAD_DIRECTORY = 'esenet';

    const STAGES = [
        'application_fee' => 'application_fee',
        'initiate_findeks' => 'initiate_findeks',
        'findeks_request_status' => 'findeks_request_status',
        'retry_later' => 'retry_later',
        'sms_pin_pending' => 'sms_pin_pending',
        'verifying_pin' => 'verifying_pin',
        'findeks_request_result' => 'findeks_request_result',
        'findeks_merge_order' => 'findeks_merge_order',
        'declined' => 'declined',
        'collect_down_payment' => 'collect_down_payment',
        'finished' => 'finished',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function ebonds(): HasMany
    {
        return $this->hasMany(Ebond::class, 'sales_agreement_id', 'id')->orderBy('due_date');
    }

    public function isNotApproved()
    {
        return $this->approval_status === 'not_approved';
    }

    public function isApproved()
    {
        return $this->approval_status === 'approved';
    }

    public function isDeclined()
    {
        return $this->approval_status === 'declined';
    }

    public function hasNotaryDocument($type, $returnLink = false)
    {
        if (!$type) {
            return false;
        }

        switch ($type) {
            case 'notary_front':
                return $returnLink ? $this->notary_document : !empty($this->notary_document);
            case 'notary_back':
                return $returnLink ? $this->notary_document_back : !empty($this->notary_document_back);
            case 'front_side_id':
                return $returnLink ? $this->front_side_id : !empty($this->front_side_id);
            default:
                return false;
        }

        return false;
    }

    public function hasUploadedAnyDocument()
    {
        return !empty($this->notary_document) || !empty($this->notary_document_back) || !empty($this->front_side_id);
    }

    public function getNotaryDocumentStatus()
    {
        return [
            'notary_front' => [
                'rejected' => $this->notary_document_rejected,
                'is_uploaded' => !empty($this->notary_document),
                'document' => $this->getNotaryDocumentLink('notary_front'),
                'document_name' => $this->notary_document,
            ],
            'notary_back' => [
                'rejected' => $this->notary_document_back_rejected,
                'is_uploaded' => !empty($this->notary_document_back),
                'document' => $this->getNotaryDocumentLink('notary_back'),
                'document_name' => $this->notary_document_back,
            ],
            'front_side_id' => [
                'rejected' => $this->front_side_id_rejected,
                'is_uploaded' => !empty($this->front_side_id),
                'document' => $this->getNotaryDocumentLink('front_side_id'),
                'document_name' => $this->front_side_id,
            ],
            'rejection_reason' => $this->notary_document_rejection_reason,
            'has_uploaded_any' => $this->hasUploadedAnyDocument()
        ];
    }

    public function getNotaryDocumentLink($type)
    {
        if (!$type) {
            return null;
        }

        $url = $this->hasNotaryDocument($type, true);

        if (!$url) {
            return null;
        }

        return config('app.url') . '/api/notary-docs/' . $url;
    }

    public function uploadNotaryDocument($documentFile, $type)
    {
        $findeksId = $this->findeks_request_id;

        $erpOrderId = $this->order->erp_order_id;
        $erpPrefix = $this->order->erp_prefix;
        $erpRef = $erpPrefix . $erpOrderId;

        $documentName = '';

        switch ($type) {
            case 'notary_back':
                $documentName = $findeksId . '_' . $erpRef . '_2.' . $documentFile->extension();
                break;
            case 'front_side_id':
                $documentName = $findeksId . '_' . $erpRef . '_kimlik.' . $documentFile->extension();
                break;
            default:
                $documentName = $findeksId . '_' . $erpRef . '.' . $documentFile->extension();
                break;
        }

        $isLocalEnv = app()->environment() == 'local';

        $path = Storage::disk($isLocalEnv ? 'local' : 'sftp')->putFileAs(self::DOCUMENT_UPLOAD_DIRECTORY, $documentFile, $documentName);

        if (!$path) {
            return false;
        }

        return $documentName;
    }

    public static function returnDocument($document)
    {
        $disk = Storage::disk(StorageUtils::getStorageByEnv());
        $path = self::DOCUMENT_UPLOAD_DIRECTORY . '/' . $document;

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
            LoggerService::logError(LogChannelsEnum::ErpSalesAgreements, 'returnDocument', ['e' => $e]);
            abort(404);
        }
    }


    public function deleteDocument($type): void
    {
        if ($type === 'notary_front' && $this->notary_document) {
            Storage::disk(StorageUtils::getStorageByEnv())->delete(self::DOCUMENT_UPLOAD_DIRECTORY . '/' . $this->notary_document);
            return;
        }

        if ($type === 'notary_back' && $this->notary_document_back) {
            Storage::disk(StorageUtils::getStorageByEnv())->delete(self::DOCUMENT_UPLOAD_DIRECTORY . '/' . $this->notary_document_back);
            return;
        }

        if ($type === 'front_side_id' && $this->front_side_id) {
            Storage::disk(StorageUtils::getStorageByEnv())->delete(self::DOCUMENT_UPLOAD_DIRECTORY . '/' . $this->front_side_id);
            return;
        }
    }
}
