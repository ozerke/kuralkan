<?php

namespace App\Services;

use Exception;

class IYSOperations
{
    private $username;
    private $password;
    private $appkey;
    private $brandCode;

    public function __construct()
    {

        if (!config('app.sms_username')) {
            throw new Exception('SMS username ENV is missing');
        }

        if (!config('app.sms_password')) {
            throw new Exception('SMS password ENV is missing');
        }

        if (!config('app.sms_appkey')) {
            throw new Exception('SMS appkey ENV is missing');
        }

        if (!config('app.sms_brand_code')) {
            throw new Exception('SMS brand code ENV is missing');
        }

        $this->username = config('app.sms_username');
        $this->password = config('app.sms_password');
        $this->appkey = config('app.sms_appkey');
        $this->brandCode = config('app.sms_brand_code');
    }

    public function addIYS($emailData, $phoneData, $phoneCallData): array
    {
        $hata = array(
            30 => "Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir. Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",
            50 => "Sorguladığınız kayıt bulunamadı.",
            60 => "Arama kriterlerinize göre listelenecek kayıt olmadığını ifade eder.",
            70 => "Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder.",
            100 => "Sistem hatası, sınır aşımı.(dakikada en fazla 10 kez sorgulanabilir.)",
            101 => "Sistem hatası, sınır aşımı.(dakikada en fazla 10 kez sorgulanabilir.)",
        );

        try {
            $arr_acc = array(
                "header" => [
                    "username" => $this->username,
                    "password" => $this->password,
                    "brandCode" => $this->brandCode
                ],
                "body" => [
                    "data" => [
                        [
                            "refId" => $emailData['refId'],
                            "type" => $emailData['type'],
                            "source" => $emailData['source'],
                            "recipient" => $emailData['recipient'],
                            "status" => $emailData['status'],
                            "consentDate" => $emailData['consentDate'],
                            "recipientType" => $emailData['recipientType'],
                            "appkey" => $this->appkey
                        ],
                        [
                            "refId" => $phoneData['refId'],
                            "type" => $phoneData['type'],
                            "source" => $phoneData['source'],
                            "recipient" => $phoneData['recipient'],
                            "status" => $phoneData['status'],
                            "consentDate" => $phoneData['consentDate'],
                            "recipientType" => $phoneData['recipientType'],
                            "appkey" => $this->appkey
                        ],
                        [
                            "refId" => $phoneCallData['refId'],
                            "type" => $phoneCallData['type'],
                            "source" => $phoneCallData['source'],
                            "recipient" => $phoneCallData['recipient'],
                            "status" => $phoneCallData['status'],
                            "consentDate" => $phoneCallData['consentDate'],
                            "recipientType" => $phoneCallData['recipientType'],
                            "appkey" => $this->appkey
                        ]
                    ]
                ]
            );

            $url_acc = "https://api.netgsm.com.tr/iys/add";
            $content_acc = json_encode($arr_acc);
            $send_acc = $this->curlitjson($url_acc, $content_acc);
            $send_acc = json_decode($send_acc);
            return ['response' => $send_acc, 'status' => 'true'];
        } catch (Exception $exc) {
            return ['response' => $exc, 'status' => 'false'];
        }
    }

    private function curlitjson($url, $content)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array("Content-type: application/json")
        );
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return ($json_response);
    }
}
