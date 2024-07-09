<?php

namespace App\Services\Templates;

class SMSTemplateService
{
    private const SMS_TEMPLATES = [
        'bankPayment' => [
            'title' => 'Havale/EFT Bildirimi', // Bank Payment
            'parameters' => [
                '$fullname', '$orderNo', '$productNameColor', '$bankName', '$paymentAmount'
            ],
            'file' => 'templates.sms.bankPayment'
        ],
        'bankPaymentDetails' => [
            'title' => 'Havale/EFT Bildirimi Detaylı', // Bank Payment Details
            'parameters' => [
                '$orderNo', '$selectedBank', '$selectedBranch', '$selectedBranchNo', '$selectedAccountNo', '$selectedIban'
            ],
            'file' => 'templates.sms.bankPaymentDetails'
        ],
        'bankPaymentReceived' => [
            'title' => 'Havale/EFT Alındı', // Bank Payment Received
            'parameters' => [
                '$fullname', '$orderNo', '$productNameColor', '$bankName', '$paymentAmount'
            ],
            'file' => 'templates.sms.bankPaymentReceived'
        ],
        'cardPartial' => [
            'title' => 'Kredi Kartı Parçalı Ödeme', // Card Partial Payment
            'parameters' => [
                '$fullname', '$orderNo', '$productNameColor', '$paymentAmount'
            ],
            'file' => 'templates.sms.cardPartial'
        ],
        'cardFullPayment' => [
            'title' => 'Kredi Kartı Tam Ödeme', // Card Full Payment
            'parameters' => [
                '$fullname', '$orderNo', '$productNameColor', '$paymentAmount'
            ],
            'file' => 'templates.sms.cardFullPayment'
        ],
        'orderConfirmed' => [
            'title' => 'Siparişiniz Onaylandı', // Order Confirmed
            'parameters' => [
                '$fullname', '$orderNo'
            ],
            'file' => 'templates.sms.orderConfirmed'
        ],
        'orderSupplying' => [
            'title' => 'Siparişiniz Tedarik Ediliyor', // Order Supplying
            'parameters' => [
                '$fullname', '$orderNo', '$deliveryDate'
            ],
            'file' => 'templates.sms.orderSupplying'
        ],
        'orderShipped' => [
            'title' => 'Siparişiniz Sevk Edildi', // Order Shipped
            'parameters' => [
                '$fullname', '$orderNo'
            ],
            'file' => 'templates.sms.orderShipped'
        ],
        'orderInvoiceReady' => [
            'title' => 'Siparişinizin Faturası Hazır', // Order Invoice Ready
            'parameters' => [
                '$fullname', '$orderNo'
            ],
            'file' => 'templates.sms.orderInvoiceReady'
        ],
        'orderDelivered' => [
            'title' => 'Siparişiniz Teslim Edildi', // Order Delivered
            'parameters' => [
                '$fullname', '$orderNo', '$deliveryPoint'
            ],
            'file' => 'templates.sms.orderDelivered'
        ],
        'orderProcessed' => [
            'title' => 'Siparişiniz Oluşturuldu', // Order Processed
            'parameters' => [
                '$fullname', '$orderNo'
            ],
            'file' => 'templates.sms.orderProcessed'
        ],
        'quickRegister' => [
            'title' => 'Üye Hızlı Kayıt', // Quick Register
            'parameters' => [
                '$fullname', '$verificationCode'
            ],
            'file' => 'templates.sms.quickRegister'
        ],
        'userRegisterByShop' => [
            'title' => 'Ekuralkan\'a bayimiz aracılığı ile kayıt oldunuz', // User Register By Shop
            'parameters' => [
                '$fullname', '$shopName', '$verificationCode'
            ],
            'file' => 'templates.sms.userRegisterByShop'
        ],
        'existingUserVerification' => [
            'title' => 'Kayıtlı Üye OTP', // Existing User Verification
            'parameters' => [
                '$fullname', '$shopName', '$verificationCode'
            ],
            'file' => 'templates.sms.existingUserVerification'
        ],
        'userLogins' => [
            'title' => 'Ekuralkan\'dan Üye Girişi', // User Logins
            'parameters' => [
                '$userEmail', '$userPassword'
            ],
            'file' => 'templates.sms.userLogins'
        ],
        'defaultVerificationCode' => [
            'title' => 'Üye OTP Kodu', // Default Verification Code
            'parameters' => [
                '$fullname', '$verificationCode'
            ],
            'file' => 'templates.sms.defaultVerificationCode'
        ],
        //Ebonds
        'ebondsCreated' => [
            'title' => 'E-senetleriniz Oluşturuldu',
            'parameters' => [
                '$fullName'
            ],
            'file' => 'templates.sms.ebonds.created'
        ],
        'beforeDueDate' => [
            'title' => 'E-senetinizin Ödemesi Yarın',
            'parameters' => [
                '$fullName', '$eBondNo', '$eBondDueDate', '$eBondAmount'
            ],
            'file' => 'templates.sms.ebonds.beforeDueDate'
        ],
        'onDueDate' => [
            'title' => 'E-senetinizin Ödemesi Bugün',
            'parameters' => [
                '$fullName', '$eBondNo', '$eBondDueDate', '$eBondAmount'
            ],
            'file' => 'templates.sms.ebonds.onDueDate'
        ],
        'afterDueDate' => [
            'title' => 'E-senetinizin Ödemesi "1 Gün Gecikti',
            'parameters' => [
                '$fullName', '$eBondNo', '$eBondDueDate', '$eBondAmount'
            ],
            'file' => 'templates.sms.ebonds.afterDueDate'
        ],
        'ebondPenalty' => [
            'title' => 'E-senetiniz Protesto Edildi',
            'parameters' => [
                '$fullName', '$eBondNo', '$eBondDueDate', '$eBondAmount'
            ],
            'file' => 'templates.sms.ebonds.ebondPenalty'
        ],
    ];

    public function getTemplate(string $key = null)
    {
        if (!$key || strlen($key) < 1) {
            return self::SMS_TEMPLATES;
        }

        return self::SMS_TEMPLATES[$key];
    }
}
