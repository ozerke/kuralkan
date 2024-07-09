<?php

namespace App\Services\Templates;

class MailTemplateService
{
    private const MAIL_TEMPLATES = [
        'orderConfirmed' => [
            'title' => 'Siparişiniz Onaylandı', // Order Confirmed
            'parameters' => [
                '$fullname', '$orderNo', '$remoteSalesAgreementLink'
            ],
            'file' => 'templates.mail.orders.order-confirmed'
        ],
        'orderSupplying' => [
            'title' => 'Siparişiniz Tedarik Ediliyor', 
            'parameters' => [
                '$fullname', '$orderNo', '$deliveryDate'
            ],
            'file' => 'templates.mail.orders.order-supplying'
        ],
        'orderInvoiceReady' => [
            'title' => 'Siparişinizin Faturası Hazır',
            'parameters' => [
                '$fullname', '$orderNo'
            ],
            'file' => 'templates.mail.orders.order-invoice-ready'
        ],
        'orderShipped' => [
            'title' => 'Siparişiniz Sevk Edildi',
            'parameters' => [
                '$fullname', '$orderNo'
            ],
            'file' => 'templates.mail.orders.order-shipped'
        ],
        'orderDelivered' => [
            'title' => 'Siparişiniz Teslim Edildi',
            'parameters' => [
                '$fullname', '$orderNo', '$deliveryPoint'
            ],
            'file' => 'templates.mail.orders.order-delivered'
        ],
        'orderCreationWithInvoiceInformation' => [
            'title' => 'Siparişiniz Oluşturuldu', // Order Creation with Invoice Information
            'parameters' => [
                '$fullname', '$orderNo', '$productPhotoUrl', '$productName', '$productColorName', '$productStockCode',
                '$orderTotalPrice', '$company', '$invoiceCompany', '$invoiceCustomerAddress',
                '$invoiceCustomerDistrictName', '$invoiceCustomerCityName', '$invoiceCompanyTaxOffice',
                '$invoiceCompanyTaxId', '$invoiceCustomerNationalId', '$orderedBySalesPoint',
                '$salesPointSiteUserName', '$salesPointDistrictName', '$salesPointCityName',
                '$serviceSiteUserName', '$serviceAddress', '$serviceDistrictName', '$serviceCityName'
            ],
            'file' => 'templates.mail.orders.order-creation-with-invoice-information'
        ],
        'orderCreationWithSalesAgreement' => [
            'title' => 'Senetli Satış Sözleşmesi ile Siparişiniz Oluşturuldu', // Order Creation with Sales Agreement
            'parameters' => [
                '$fullname', '$orderNo', '$productPhotoUrl', '$productName', '$productColorName', '$productStockCode',
                '$orderTotalPrice', '$company', '$invoiceCompany', '$invoiceCustomerAddress',
                '$invoiceCustomerDistrictName', '$invoiceCustomerCityName', '$invoiceCompanyTaxOffice',
                '$invoiceCompanyTaxId', '$invoiceCustomerNationalId', '$orderedBySalesPoint',
                '$salesPointSiteUserName', '$salesPointDistrictName', '$salesPointCityName',
                '$serviceSiteUserName', '$serviceAddress', '$serviceDistrictName', '$serviceCityName'
            ],
            'file' => 'templates.mail.orders.order-creation-with-sales-agreement'
        ],
        'quickRegistration' => [
            'title' => 'Ekuralkan Hızlı Üyelik', // Quick Registration
            'parameters' => [
                '$fullname'
            ],
            'file' => 'templates.mail.users.quick-registration'
        ],
        'resetPassword' => [
            'title' => 'Ekuralkan Şifre Sıfırlama', // Reset Password
            'parameters' => [
                '$fullname', '$resetUrl'
            ],
            'file' => 'templates.mail.users.reset-password'
        ],
        'salesPointUserRegistration' => [
            'title' => 'Ekuralkan\'a bayimiz aracılığı ile kayıt oldunuz', // Sales Point User Registration
            'parameters' => [
                '$fullname', '$salesPoint', '$email', '$randomPassword'
            ],
            'file' => 'templates.mail.users.sales-point-user-registration'
        ],
        //Ebonds
        'ebondsCreated' => [
            'title' => 'E-senetleriniz Oluşturuldu',
            'parameters' => [
                '$fullName'
            ],
            'file' => 'templates.mail.ebonds.created'
        ],
        'beforeDueDate' => [
            'title' => 'E-senetinizin Ödemesi Yarın',
            'parameters' => [
                '$fullName', '$eBondNo', '$eBondDueDate', '$eBondAmount'
            ],
            'file' => 'templates.mail.ebonds.beforeDueDate'
        ],
        'onDueDate' => [
            'title' => 'E-senetinizin Ödemesi Bugün',
            'parameters' => [
                '$fullName', '$eBondNo', '$eBondDueDate', '$eBondAmount'
            ],
            'file' => 'templates.mail.ebonds.onDueDate'
        ],
        'afterDueDate' => [
            'title' => 'E-senetinizin Ödemesi 1 Gün Gecikti',
            'parameters' => [
                '$fullName', '$eBondNo', '$eBondDueDate', '$eBondAmount'
            ],
            'file' => 'templates.mail.ebonds.afterDueDate'
        ],
        'ebondPenalty' => [
            'title' => 'E-senetiniz Protesto Edildi',
            'parameters' => [
                '$fullName', '$eBondNo', '$eBondDueDate', '$eBondAmount'
            ],
            'file' => 'templates.mail.ebonds.ebondPenalty'
        ],
    ];

    public function getTemplate(string $key = null)
    {
        if (!$key || strlen($key) < 1) {
            return self::MAIL_TEMPLATES;
        }

        return self::MAIL_TEMPLATES[$key];
    }
}
