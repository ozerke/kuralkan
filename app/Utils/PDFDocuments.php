<?php

namespace App\Utils;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class PDFDocuments
{
    public const UPLOAD_DIRECTORY = 'app/uploads/contracts';

    public static function getPath(Order $order)
    {
        $documentName = 'contract_' . $order->order_no . '.pdf';

        $path = storage_path() . '/' . self::UPLOAD_DIRECTORY . '/' . $documentName;

        if (!file_exists($path)) {
            self::generateContract($order);
        }

        return $path;
    }

    public static function returnContract(Order $order)
    {
        $documentName = 'contract_' . $order->order_no . '.pdf';

        $path = storage_path() . '/' . self::UPLOAD_DIRECTORY . '/' . $documentName;

        if (!file_exists($path)) {
            self::generateContract($order);
        }

        try {
            $file = File::get($path);
            $type = File::mimeType($path);
            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);
            return $response;
        } catch (Exception $exception) {
            abort(404);
        }
    }

    public static function generateContract(Order $order)
    {
        $documentName = 'contract_' . $order->order_no . '.pdf';

        if (!file_exists(storage_path() . '/' . self::UPLOAD_DIRECTORY)) {
            mkdir(storage_path() . '/' . self::UPLOAD_DIRECTORY, 0777, true);
        }

        $data = [
            'dateTime' => $order->created_at->format('d-m-Y H:i:s'),
            'tosAddress' => $order->invoiceUser->address,
            'tosDeliveryAddress' => $order->deliveryUser->address,
            'tosEmail' => $order->invoiceUser->email,
            'tosFullname' => $order->invoiceUser->full_name,
            'tosPhone' => $order->invoiceUser->phone,
            'tosProductName' => $order->productVariation->getDocumentTitle(),
            'tosPrice' => '₺' . number_format($order->total_amount, 2, ',', '.'),
        ];

        $pdf = Pdf::loadView('utility.remote-agreement', $data);

        return $pdf->save(storage_path() . '/' . self::UPLOAD_DIRECTORY . '/' . $documentName);
    }

    public static function deleteContract(Order $order)
    {
        $documentName = 'contract_' . $order->order_no . '.pdf';

        $path = storage_path() . '/' . self::UPLOAD_DIRECTORY . '/' . $documentName;

        if (file_exists($path)) {
            return unlink($path);
        }

        return false;
    }
}
