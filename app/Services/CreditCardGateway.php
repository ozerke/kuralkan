<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class CreditCardGateway
{
    const SEND_PAYMENT_URL = "https://portal.buluttahsilat.com/Api/Vpos/PaymentCreateDirect";
    const SEND_INSTALLMENT_URL = "https://portal.buluttahsilat.com/Api/VPos/GetUsableInstallmentList";
    const PAYMENT_LIST_URL = "https://portal.buluttahsilat.com/Api/VPos/PaymentList";

    private function getVendorCredentials()
    {
        return [
            'api_code' => config('app.bt_company_api_code'),
            'username' => config('app.bt_username'),
            'password' => config('app.bt_password'),
            'secret_key_code' => config('app.bt_secret_key_code'),
            'payment_exp_code' => config('app.bt_payment_exp_code')
        ];
    }

    public function sendPayment($senderId, $cardHolderName, $cardNumber, $cvc, $cardExpiresMonth, $cardExpiresYear, $installments, $orderNo, $orderDate, $totalPrice, $amountWithoutRates, $userId, $clientIp, $campaignCode = null)
    {
        $credentials = $this->getVendorCredentials();

        $currency = "TRY";

        $ref = $orderNo . '-' . $senderId;

        $hash_str = $credentials['secret_key_code'] . $credentials['username'] . $credentials['api_code'] . $ref . $orderDate . $totalPrice . $currency;
        $hash = md5($hash_str);

        $returnUrl = config('app.url') . "/handle-payment/" . $orderNo . '?app_lang=' . app()->getLocale();

        $data = [
            "FirmAPICode" => $credentials['api_code'],
            "UserName" => $credentials['username'],
            "Password" => $credentials['password'],
            "OrderRefNo" => $ref,
            "TotalPriceWithVAT" => $totalPrice,
            "ReservedField" => $amountWithoutRates,
            "Description" => $userId,
            "OrderDate" => $orderDate,
            "Currency" => $currency,
            "CreditCardNumber" => $cardNumber,
            "CardHolderFullName" => $cardHolderName,
            "Cvv" => $cvc,
            "CardExpiresMonth" => $cardExpiresMonth,
            "CardExpiresYear" => $cardExpiresYear,
            "Installment" => $installments,
            "ReturnUrl" => $returnUrl,
            "ClientIP" => $clientIp,
            "IsReturnUrlRedirect" => true,
            "Hash" => $hash
        ];

        if ($campaignCode) {
            $data['PaymentExpCode'] = $campaignCode;
        }

        $response = Http::post(self::SEND_PAYMENT_URL, $data);

        return $response;

        /*
        The response contains OrderRefNo, OrderGuidCode, StatusCode, StatusMessage, Hash, Form3DContent
        A successful StatusCode can be 322 or 601 any other response will not have the Form3DContent for posting.
        In case of success the form should be sending the browser to the relative bank's 3D secure confirmation form.
        */
    }

    public function sendFeePayment($senderId, $cardHolderName, $cardNumber, $cvc, $cardExpiresMonth, $cardExpiresYear, $orderNo, $orderDate, $feeAmount, $userId, $clientIp)
    {
        $credentials = $this->getVendorCredentials();

        $currency = "TRY";

        $ref = $orderNo . '-' . $senderId . '-' . 'fee';

        $hash_str = $credentials['secret_key_code'] . $credentials['username'] . $credentials['api_code'] . $ref . $orderDate . $feeAmount . $currency;
        $hash = md5($hash_str);

        $returnUrl = config('app.url') . "/handle-fee-payment/" . $orderNo . '?app_lang=' . app()->getLocale();

        $data = [
            "FirmAPICode" => $credentials['api_code'],
            "UserName" => $credentials['username'],
            "Password" => $credentials['password'],
            "OrderRefNo" => $ref,
            "TotalPriceWithVAT" => $feeAmount,
            "ReservedField" => $feeAmount,
            "Description" => $userId,
            "OrderDate" => $orderDate,
            "Currency" => $currency,
            "CreditCardNumber" => $cardNumber,
            "CardHolderFullName" => $cardHolderName,
            "Cvv" => $cvc,
            "CardExpiresMonth" => $cardExpiresMonth,
            "CardExpiresYear" => $cardExpiresYear,
            "Installment" => 1,
            "ReturnUrl" => $returnUrl,
            "ClientIP" => $clientIp,
            "IsReturnUrlRedirect" => true,
            "Hash" => $hash
        ];

        $response = Http::post(self::SEND_PAYMENT_URL, $data);

        return $response;

        /*
        The response contains OrderRefNo, OrderGuidCode, StatusCode, StatusMessage, Hash, Form3DContent
        A successful StatusCode can be 322 or 601 any other response will not have the Form3DContent for posting.
        In case of success the form should be sending the browser to the relative bank's 3D secure confirmation form.
        */
    }

    public function sendBondPayment($senderId, $cardHolderName, $cardNumber, $cvc, $cardExpiresMonth, $cardExpiresYear, $orderNo, $orderDate, $totalPrice, $userId, $clientIp, $bondNo)
    {
        $credentials = $this->getVendorCredentials();

        $currency = "TRY";

        $ref = "$orderNo-$senderId-esenet-$bondNo";

        $hash_str = $credentials['secret_key_code'] . $credentials['username'] . $credentials['api_code'] . $ref . $orderDate . $totalPrice . $currency;
        $hash = md5($hash_str);

        $returnUrl = config('app.url') . "/handle-bond-payment/" . $orderNo . '?bond_no=' . $bondNo . '&app_lang=' . app()->getLocale();

        $data = [
            "FirmAPICode" => $credentials['api_code'],
            "UserName" => $credentials['username'],
            "Password" => $credentials['password'],
            "OrderRefNo" => $ref,
            "TotalPriceWithVAT" => $totalPrice,
            "ReservedField" => $totalPrice,
            "Description" => $userId,
            "OrderDate" => $orderDate,
            "Currency" => $currency,
            "CreditCardNumber" => $cardNumber,
            "CardHolderFullName" => $cardHolderName,
            "Cvv" => $cvc,
            "CardExpiresMonth" => $cardExpiresMonth,
            "CardExpiresYear" => $cardExpiresYear,
            "Installment" => 1,
            "ReturnUrl" => $returnUrl,
            "ClientIP" => $clientIp,
            "IsReturnUrlRedirect" => true,
            "Hash" => $hash
        ];

        $response = Http::post(self::SEND_PAYMENT_URL, $data);

        return $response;

        /*
        The response contains OrderRefNo, OrderGuidCode, StatusCode, StatusMessage, Hash, Form3DContent
        A successful StatusCode can be 322 or 601 any other response will not have the Form3DContent for posting.
        In case of success the form should be sending the browser to the relative bank's 3D secure confirmation form.
        */
    }

    public static function translateStatusToError($status, $lang)
    {
        $translations = [
            331 => ['tr' => 'ODEME ALINAMADI', 'en' => 'PAYMENT NOT RECEIVED'],
            332 => ['tr' => 'ODEME ALINDI', 'en' => 'PAYMENT RECEIVED'],
            333 => ['tr' => 'IPTAL EDILDI', 'en' => 'PAYMENT CANCELLED'],
            601 => ['tr' => 'ISLEM BASARILI', 'en' => 'TRANSACTION SUCCESSFUL'],
            602 => ['tr' => 'ISLEM SIRASINDA HATA OLUSTU', 'en' => 'AN ERROR OCCURRED DURING THE TRANSACTION'],
            603 => ['tr' => 'SAYFAYA GELIS VERILERI HATALI', 'en' => 'RECEIVED DATA IS INCORRECT'],
            604 => ['tr' => 'KULLANICI BILGILERI HATALI', 'en' => 'USER INFORMATION IS INCORRECT'],
            605 => ['tr' => 'IP ERISIM YETKINIZ YOK', 'en' => 'YOU DO NOT HAVE IP ACCESS AUTHORITY'],
            606 => ['tr' => 'KULLANIM SURESI DOLMUSTUR', 'en' => 'PERIOD OF USE HAS EXPIRED'],
            608 => ['tr' => 'BU SIPARIS ILE ISLEM YAPILMISTIR', 'en' => 'TRANSACTION HAS BEEN MADE WITH THIS ORDER'],
            609 => ['tr' => 'URUN BILGISI EKSIK', 'en' => 'PRODUCT INFORMATION IS MISSING'],
            610 => ['tr' => 'URUN BILGILERI TUTARLI DEGIL', 'en' => 'PRODUCT INFORMATION IS NOT CONSISTENT'],
            611 => ['tr' => 'GIRIS BILGILERINI KONTROL EDIN', 'en' => 'CHECK LOGIN INFORMATION'],
            612 => ['tr' => 'KART VE ODEME BILGILERINI KONTROL EDIN', 'en' => 'CHECK CARD AND PAYMENT INFORMATION'],
            613 => ['tr' => 'ODEME ALINAMADI', 'en' => 'PAYMENT NOT RECEIVED'],
            614 => ['tr' => 'BANKA KOMISYON ORANLARI TANIMLI OLMADIGINDAN ISLEM YAPILAMIYOR', 'en' => 'TRANSACTIONS CANNOT BE MADE AS BANK COMMISSION RATES ARE NOT DEFINED'],
            615 => ['tr' => 'TEK CEKIM ICIN BANKA TANIMLI DEGIL', 'en' => 'BANK IS NOT DEFINED FOR SINGLE WITHDRAWAL'],
            616 => ['tr' => 'BANKA KULLANILMIYOR YA DA TAKSIT SECENEGI YOK', 'en' => 'THE BANK IS NOT USED OR THERE IS NO INSTALLMENT OPTION'],
            617 => ['tr' => 'BU TAKSIT SECENEGI BANKANIZ TARAFINDAN KULLANILMIYOR', 'en' => 'THIS INSTALLMENT OPTION IS NOT USED BY YOUR BANK'],
            618 => ['tr' => 'GONDERILEN TUTAR HATALI', 'en' => 'THE AMOUNT SENT IS INCORRECT'],
            619 => ['tr' => 'BANKA KULLANIMDA DEGIL YA DA KOMISYON ORANLARI TANIMLI DEGIL', 'en' => 'THE BANK IS NOT IN USE OR COMMISSION RATES ARE NOT DEFINED'],
            620 => ['tr' => '3D SIFRE EKRANI ACILMIYOR', 'en' => '3D PASSWORD SCREEN DOES NOT OPEN'],
            621 => ['tr' => 'TUTAR BILGILERINIZI KONTROL EDINIZ', 'en' => 'CHECK YOUR AMOUNT INFORMATION'],
            622 => ['tr' => '3D SIFRESI HATALI GIRILDI', 'en' => '3D PASSWORD WAS ENTERED INCORRECTLY'],
            623 => ['tr' => 'FIYAT ALANLARI VIRGULDEN SONRA 2 KARAKTER GIRILMELI', 'en' => 'PRICE FIELDS MUST HAVE 2 DIGITS AFTER THE COMMA'],
            624 => ['tr' => 'KEYVALUELIST DOLU OLMALI', 'en' => 'KEYVALUELIST MUST BE FULL'],
            625 => ['tr' => 'BU KULLANICININ GIRIS YETKISI YOK', 'en' => 'THIS USER DOES NOT HAVE LOGIN AUTHORITY'],
            626 => ['tr' => 'FAZLA SAYIDA HATALI GIRIS', 'en' => 'TOO MANY FAULTY ENTRIES'],
            627 => ['tr' => 'ODENMEMIS TAKSIT TUTARI', 'en' => 'UNPAID INSTALLMENT AMOUNT'],
            628 => ['tr' => 'ODEME ISLEMINDEN VAZGECILDI', 'en' => 'PAYMENT WAS ABANDONED'],
            629 => ['tr' => 'KAYIT BULUNAMADI', 'en' => 'NO RECORDS FOUND'],
            630 => ['tr' => 'ISTEK ZAMAN ASIMI', 'en' => 'REQUEST TIMEOUT'],
            633 => ['tr' => 'BASARILI BIR ISLEM KAYDI BULUNAMADI', 'en' => 'NO SUCCESSFUL TRANSACTION RECORD FOUND'],
            634 => ['tr' => 'ODEME TUTARI SECILI TAKSIT ICIN TANIMLANAN ALT LIMITIN ALTINDA', 'en' => 'PAYMENT AMOUNT IS BELOW THE LOWER LIMIT DEFINED FOR THE SELECTED INSTALLMENT'],
            635 => ['tr' => 'ODEME TUTARI SECILI TAKSIT ICIN TANIMLANAN UST LIMITIN USTUNDE', 'en' => 'PAYMENT AMOUNT IS ABOVE THE UPPER LIMIT DEFINED FOR THE SELECTED INSTALLMENT']
        ];

        if (!$translations[$status]) {
            return $translations[333][$lang];
        }

        return $translations[$status][$lang];
    }

    // 20240131 - OE - Get installment rates and bank info from Bulut Tahsilat
    public function bulutInstallments($amountToBePaid, $cardNumber, $campaignCode = null)
    {
        $credentials = $this->getVendorCredentials();

        $ccSixDigits = strlen($cardNumber) > 6 ? substr($cardNumber, 0, 6) : $cardNumber;
        $currency = "TRY"; # The default currency is left hardcoded as TRY for now
        $data = array(
            "FirmAPICode" => $credentials['api_code'],
            "UserName" => $credentials['username'],
            "Password" => $credentials['password'],
            "PaymentExpCode" => $campaignCode ?: $credentials['payment_exp_code'],
            "CardBinNumber" => $ccSixDigits,
            "TotalAmount" => $amountToBePaid,
            "Currency" => $currency,
        );

        $response = Http::post(self::SEND_INSTALLMENT_URL, $data);

        $response = $response->body();
        $response = json_decode($response);

        if ($response->StatusCode == 322 || $response->StatusCode == 601) {
            return collect($response->IntallmentItem)->toArray();
        }

        return null;
    }

    public function findInstallments($installments = [], $count = 12)
    {
        $list = collect();

        foreach ($installments as $installment) {
            if ($count) {
                if ($count >= $installment->InstallmentNumber) {
                    $list->push([
                        'months' => $installment->InstallmentNumber,
                        'total' => number_format($this->badNumToReal($installment->TotalAmount), 2, ',', '.'),
                        'perOne' => number_format($this->badNumToReal($installment->MonthlyAmount), 2, ',', '.'),
                    ]);
                }
            } else {
                $list->push([
                    'months' => $installment->InstallmentNumber,
                    'total' => number_format($this->badNumToReal($installment->TotalAmount), 2, ',', '.'),
                    'perOne' => number_format($this->badNumToReal($installment->MonthlyAmount), 2, ',', '.'),
                ]);
            }
        }

        if ($list->isEmpty()) {
            return null;
        }

        return $list->sortBy('months')->toArray();
    }

    private function badNumToReal($numstr = '')
    {
        if (empty($numstr)) return '';
        $noDots = str_replace('.', '', strval($numstr));
        $lastTwo = substr($noDots, -2);
        $numRest = substr($noDots, 0, strlen($noDots) - 2);
        $floatNumber = $numRest . '.' . $lastTwo;
        return (float)$floatNumber;
    }

    public function validateIncomingHash($paymentResponse, Order $order, $provideOutput = false)
    {
        if (!App::environment('production')) {
            return true;
        }

        $orderDate = $order->created_at->setTimezone('UTC')->format('Y-m-d H:i:s');
        $carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $orderDate, 'UTC');
        $convertedDate = $carbonDate->setTimezone('Europe/Istanbul');

        $date = $convertedDate->toDateTimeString();

        if ($provideOutput) {
            $hashData = $this->getExpectedHash(
                $paymentResponse->OrderRefNo,
                $date,
                $paymentResponse->TotalPriceWithVat,
                $paymentResponse->Currency,
                $paymentResponse->StatusCode,
                $paymentResponse->StatusMessage,
                true
            );

            return [
                'Found' => $paymentResponse->Hash,
                'Expected' => $hashData[0],
                'hashedString' => $hashData[1]
            ];
        }

        return $paymentResponse->Hash === $this->getExpectedHash(
            $paymentResponse->OrderRefNo,
            $date,
            $paymentResponse->TotalPriceWithVat,
            $paymentResponse->Currency,
            $paymentResponse->StatusCode,
            $paymentResponse->StatusMessage
        );
    }

    private function getExpectedHash($orderRefNo, $orderDate, $totalPriceWithVat, $currency, $statusCode, $statusMessage, $returnHashedString = false)
    {
        $credentials = $this->getVendorCredentials();

        $hashValue = $credentials['secret_key_code'] . $credentials['username'] . $credentials['api_code'] .
            $orderRefNo .
            $orderDate .
            $totalPriceWithVat .
            $currency .
            $statusCode .
            $statusMessage;

        if ($returnHashedString) {
            return [
                md5($hashValue),
                $hashValue
            ];
        }

        return md5($hashValue);
    }

    public function getPaymentList($startDate = '', $endDate = '', $transactionStatusId = 1)
    {
        $credentials = $this->getVendorCredentials();

        $data = array(
            "FirmApiCode" => $credentials['api_code'],
            "UserName" => $credentials['username'],
            "Password" => $credentials['password'],
            "StartDate" => $startDate,
            "EndDate" => $endDate,
        );

        $response = Http::post(self::PAYMENT_LIST_URL, $data);

        if (!$response->ok()) {
            throw new Exception("HTTP Request failed with status: " . $response->status());
        }

        $responseBody = $response->body();

        $responseDecoded = json_decode($responseBody);

        dd($responseDecoded);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Failed to decode JSON response: " . json_last_error_msg());
        }

        return $responseDecoded;
    }
}
