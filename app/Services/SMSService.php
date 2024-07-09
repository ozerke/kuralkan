<?php

namespace App\Services;

class SMSService
{
    public static function sendSMS($phone, $message)
    {
        if (!empty($phone) && !empty($message)) {
            $data = array(
                'message' => $message,
                'no' => [$phone],
                'header' => 'EKURALKAN',
                'filter' => 0,
                'encoding' => 'tr',
                'startdate' => time(),
                'stopdate' => time(),
                'bayikodu' => '8503092157',
                'appkey' => 'kMOFQz9DLQ1KkriQ9mUwjsNrijgXu7wFrkULktOBG9c='
            );

            $sms = new SMSSender();

            $response = $sms->smsGonder($data);

            if (config('app.log_sms')) {
                LoggerService::logInfo(LogChannelsEnum::MessagesSms, 'sendSMS: Response', ['response' => $response]);
            }

            return $response;
        }
    }
}
