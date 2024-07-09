<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class IYSService
{
    public static function addIYS($email, $phone)
    {
        if (!empty($phone)) {
            date_default_timezone_set('Europe/Istanbul');
            $emailData = array(
                'refId' => date('Ymd-') . $email,
                'type' => 'EPOSTA',
                'source' => 'HS_WEB',
                'recipient' => $email,
                'status' => 'ONAY',
                'consentDate' => date('Y-m-d H:i:s'),
                'recipientType' => 'BIREYSEL',
                'encoding' => 'tr',
            );

            $phoneBase = [
                'refId' => date('Ymd') . $phone,
                'source' => 'HS_WEB',
                'recipient' => $phone,
                'status' => 'ONAY',
                'consentDate' => date('Y-m-d H:i:s'),
                'recipientType' => 'BIREYSEL',
                'encoding' => 'tr',
            ];

            $phoneData = array_merge($phoneBase, ['type' => 'MESAJ']);

            $phoneCallData = array_merge($phoneBase, ['type' => 'ARAMA']);

            $iys = new IYSOperations();

            $response = $iys->addIYS($emailData, $phoneData, $phoneCallData);

            if (config('app.log_iys')) {
                LoggerService::logInfo(LogChannelsEnum::MessagesSms, 'addIYS: Response', ['response' => $response]);
            }

            return $response;
        }
    }
}
