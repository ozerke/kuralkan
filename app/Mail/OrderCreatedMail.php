<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private $order;
    private $isSalesAgreement;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->isSalesAgreement = $order->isSalesAgreementOrder();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Siparişinizle ilgili bilgiler',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $data = [
            'fullname' => $this->order->invoiceUser->full_name,
            'orderNo' => $this->order->order_no,
            'productPhotoUrl' => $this->order->productVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png'),
            'productName' => $this->order->productVariation->product->currentTranslation->product_name,
            'productColorName' => $this->order->productVariation->color->currentTranslation->color_name,
            'productStockCode' => $this->order->productVariation->product->stock_code,
            'orderTotalPrice' => number_format($this->order->total_amount, 2, ',', '.') . 'TL',
            'company' => $this->order->invoiceUser->company,
            'invoiceCompany' => $this->order->invoiceUser->company_name,
            'invoiceCustomerAddress' =>  $this->order->invoiceUser->address,
            'invoiceCustomerDistrictName' => $this->order->invoiceUser->district->currentTranslation->district_name,
            'invoiceCustomerCityName' => $this->order->invoiceUser->getCity()->currentTranslation->city_name ?? '-',
            'invoiceCompanyTaxOffice' => $this->order->invoiceUser->tax_office,
            'invoiceCompanyTaxId' => $this->order->invoiceUser->tax_id,
            'invoiceCustomerNationalId' => $this->order->invoiceUser->national_id,
            'orderedBySalesPoint' => $this->order->isOrderedByShop() ? 'Y' : 'N',
            'salesPointSiteUserName' => $this->order->user->site_user_name,
            'salesPointDistrictName' => $this->order->user->district->currentTranslation->district_name,
            'salesPointCityName' => $this->order->user->getCity()->currentTranslation->city_name ?? '-',
            'serviceSiteUserName' => $this->order->deliveryUser->site_user_name,
            'serviceAddress' => $this->order->deliveryUser->address,
            'serviceDistrictName' => $this->order->deliveryUser->district->currentTranslation->district_name,
            'serviceCityName' => $this->order->deliveryUser->getCity()->currentTranslation->city_name ?? '-',
        ];

        return new Content(
            view: $this->isSalesAgreement ? 'templates.mail.orders.order-creation-with-sales-agreement' : 'templates.mail.orders.order-creation-with-invoice-information',
            with: $data
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
