@extends('adminlte::page')

@php
    $paymentInfo = $order->getOrderPaymentsState(true);
    $notaryStatus = $order->salesAgreement ? $order->salesAgreement->getNotaryDocumentStatus() : null;
    $orderCampaign = $order->orderCampaign;
@endphp

@section('content')
    @include('layouts.messages')

    <div class="row">
        <div class="col-lg-12 py-5 px-5 gap-5">
            <div class="row justify-content-between align-items-center bg-white p-3">
                <div class="col-lg-6 col-md-12">
                    <div class="text-muted">{{ __('web.order-number') }}</div>
                    <div class="font-weight-bold">{{ $order->order_no }}</div>
                    @if ($order->salesAgreement)
                        <div class="text-muted">{{ __('app.erp-id-findeks-id') }}</div>
                        <div class="font-weight-bold">{{ $order->erp_prefix }} {{ $order->erp_order_id ?? '-' }} /
                            {{ $order->salesAgreement->findeks_request_id ?? '-' }}</div>
                    @else
                        <div class="text-muted">{{ __('app.erp-id') }}</div>
                        <div class="font-weight-bold">{{ $order->erp_prefix }} {{ $order->erp_order_id ?? '-' }}</div>
                    @endif
                </div>
                <div class="col-lg-6 col-md-12 d-flex justify-content-end align-items-center">
                    <div class="font-weight-bold mr-4">{{ __('app.order-date') }}:
                        {{ $order->created_at->format('d-m-Y H:i') }}</div>
                    <div class="bg-primary p-2 rounded text-white text-center">
                        @if ($order->erp_order_id)
                            <span
                                class="font-weight-semibold">{{ optional($order->latestStatusHistory)->orderStatus->currentTranslation->status ?? '-' }}</span>
                        @else
                            <span class="font-weight-semibold">{{ __('web.pending') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-4 bg-white p-3">
                    <div class="border-bottom border-dark">
                        <span class="h4 font-weight-bold">{{ __('web.product-information') }}</span>
                    </div>
                    <div class="w-100 py-3">
                        <img src="{{ $order->productVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}"
                            class="img-fluid" alt="Product Image">
                    </div>
                    <div class="font-weight-bold">
                        {{ $order->productVariation->product->currentTranslation->product_name }} -
                        {{ $order->productVariation->color->currentTranslation->color_name }}
                    </div>
                    <div class="mb-2">
                        {{ __('web.stock-code') }}: {{ $order->productVariation->product->stock_code }}
                    </div>
                    @if ($order->motor_no || $order->chasis_no)
                        <div class="bg-primary rounded mb-2 text-center">
                            @if ($order->motor_no)
                                <p class="font-weight-bold text-white py-2">{{ __('web.engine-no') }}:
                                    {{ $order->motor_no }}</p>
                            @endif
                            @if ($order->chasis_no)
                                <p class="font-weight-bold text-white py-2">{{ __('web.chassis-no') }}:
                                    {{ $order->chasis_no }}</p>
                            @endif
                        </div>
                    @endif

                    @if ($order->temprorary_licence_doc_link)
                        <a href="{{ $order->temprorary_licence_doc_link }}" target="_blank"
                            class="btn btn-success rounded py-2 px-4 w-100 mb-2 font-weight-bold"
                            style="color:#fff !important;">
                            {{ __('web.temporary-plate') }}
                        </a>
                    @else
                        <a class="btn btn-danger rounded py-2 px-4 disabled w-100 mb-2 font-weight-bold"
                            aria-disabled="true" style="color:#fff !important;">
                            {{ __('web.temporary-plate') }}
                        </a>
                    @endif

                    @if ($order->plate_printing_doc_link)
                        <a href="{{ $order->plate_printing_doc_link }}" target="_blank"
                            class="btn btn-success rounded py-2 px-4 text-white w-100 font-weight-bold"
                            style="color:#fff !important;">
                            {{ __('web.temporary-license') }}
                        </a>
                    @else
                        <a class="btn btn-danger rounded py-2 px-4 disabled text-white w-100 font-weight-bold"
                            aria-disabled="true" style="color:#fff !important;">
                            {{ __('web.temporary-license') }}
                        </a>
                    @endif
                </div>
                <div class="col-lg-4 bg-white p-3">
                    @if ($order->isOrderedByShop())
                        <div class="border-bottom border-dark mb-2">
                            <span class="h4 font-weight-bold">{{ __('web.sales-point-information') }}</span>
                        </div>
                        <p>{{ __('web.shop-name') }}: [{{ $order->user->erp_user_id }}]
                            {{ $order->user->site_user_name }}</p>
                        <p>{{ __('web.district-city') }}:
                            {{ $order->user->district->currentTranslation->district_name }}
                            @if ($order->user->getCity())
                                / {{ $order->user->getCity()->currentTranslation->city_name }}
                            @endif
                        </p>
                        <p>{{ __('web.phone') }}: {{ $order->user->phone }}</p>
                    @endif
                    <div class="border-bottom border-dark mb-2">
                        <span class="h4 font-weight-bold">{{ __('web.invoice-information') }}</span>
                    </div>
                    <p>
                        {{ __('web.name-company') }}: {{ $order->invoiceUser->getInvoiceName() }}
                    </p>
                    <p>
                        {{ __('web.address') }}: {{ $order->invoiceUser->address }}
                    </p>
                    <p>
                        {{ __('web.district-city') }}:
                        {{ $order->invoiceUser->district->currentTranslation->district_name }}
                        @if ($order->invoiceUser->getCity())
                            / {{ $order->invoiceUser->getCity()->currentTranslation->city_name }}
                        @endif
                    </p>
                    <p>
                        {{ __('web.email') }}: {{ $order->invoiceUser->email }}
                    </p>
                    <p>
                        {{ __('web.phone') }}: {{ $order->invoiceUser->phone }}
                    </p>
                    <p>
                        {{ __('web.tc-no-tax-no') }}: {{ $order->invoiceUser->getTaxOrNationalId() }}
                    </p>
                    @if ($order->invoiceUser->isCompany())
                        <p>
                            {{ __('web.tax-info') }}: {{ $order->invoiceUser->tax_office }}
                        </p>
                    @endif

                    @if ($order->invoice_link)
                        <a href="{{ $order->invoice_link }}" target="_blank"
                            class="btn btn-success rounded py-2 px-4 w-100 text-white mb-2 font-weight-bold"
                            style="color:#fff !important;">
                            {{ __('web.invoice') }}
                        </a>
                    @else
                        <a class="btn btn-danger rounded py-2 px-4 disabled w-100 text-white mb-2 font-weight-bold"
                            style="color:#fff !important;" aria-disabled="true">
                            {{ __('web.invoice') }}
                        </a>
                    @endif

                    <div class="border-bottom border-dark mb-2">
                        <span class="h4 font-weight-bold">{{ __('web.delivery-information') }}</span>
                    </div>
                    <p>{{ __('web.service-name') }}: {{ $order->deliveryUser->site_user_name }}</p>
                    <p>{{ __('web.address') }}: {{ $order->deliveryUser->address }}</p>
                    <p>{{ __('web.district-city') }}:
                        {{ $order->deliveryUser->district->currentTranslation->district_name }}
                        @if ($order->deliveryUser->getCity())
                            / {{ $order->deliveryUser->getCity()->currentTranslation->city_name }}
                        @endif
                    </p>
                    <p>{{ __('web.phone') }}: {{ $order->deliveryUser->phone }}</p>
                    <p>{{ __('web.email') }}: {{ $order->deliveryUser->erp_email }}</p>
                    @if ($order->delivery_date)
                        <p class="font-weight-bold bg-primary text-white rounded py-2 px-4">{{ __('web.delivery-date') }}:
                            {{ $order->getDeliveryDate() }}
                        </p>
                    @endif
                </div>
                <div class="col-lg-4 bg-white p-3">
                    <div class="border-bottom border-dark mb-2">
                        <span class="h4 font-weight-bold">{{ __('web.payment-information') }}</span>
                    </div>
                    @if ($order->isSalesAgreementOrder())
                        <p>
                            <span>{{ __('web.application-status') }}:</span>
                            <span>{{ $order->sa_status['text'] }} @if ($order->salesAgreement && $order->salesAgreement->is_sms_pending)
                                    ({{ __('web.sms-pending') }})
                                @endif
                                @if (empty($order->salesAgreement->application_fee_payment_id))
                                    ({{ __('web.application-fee-not-paid') }})
                                @endif
                            </span>
                        </p>
                    @endif

                    @if ($order->isCampaignOrder())
                        <div class="my-4 border px-2 py-2 border-dark">
                            <p class="h5 font-weight-bold">{{ __('app.campaigns') }}</p>
                            <p>{{ __('app.down-payment') }}:
                                {{ number_format($orderCampaign->down_payment, 2, ',', '.') }}
                                TL ({{ $orderCampaign->getPercentageForOrderAmount() }}%)</p>
                            <p>{{ __('app.installments') }}: {{ $orderCampaign->installments }}</p>
                            <p>{{ __('app.rate') }}: {{ $orderCampaign->rate }}</p>
                            <p>{{ __('app.bt-payment-exp-code') }}: {{ $orderCampaign->bt_payment_exp_code }}</p>
                        </div>
                    @endif

                    <p>{{ __('web.order-amount') }}: {{ number_format($order->total_amount, 2, ',', '.') }} TL</p>
                    <p>{{ __('web.payment-type') }}: {{ $order->getOrderPaymentType(true) }}</p>
                    @if ($order->payment_type === 'S')
                        <p>{{ __('web.down-payment') }}: {{ $order->salesAgreement->down_payment_amount ?? '-' }}
                            TL</p>
                        <p>{{ __('web.payment-left') }}:
                            {{ number_format($paymentInfo['remaining_amount'], 2, ',', '.') }} TL</p>
                        <p>{{ __('web.installment-amount') }}: {{ $order->salesAgreement->monthly_payment ?? '-' }}
                            TL/{{ __('web.month') }}</p>
                        <p>{{ __('web.installments') }}: {{ $order->salesAgreement->number_of_installments ?? '-' }}</p>
                        @if (!empty($order->salesAgreement->agreement_document_link))
                            <a href="{{ $order->salesAgreement->agreement_document_link }}" target="_blank"
                                class="btn btn-success rounded-md py-2 px-4 w-100 font-weight-bold mb-4"
                                style="color:#fff !important;">
                                {{ __('web.sales-agreement') }}
                            </a>
                        @else
                            <a class="btn btn-danger rounded-md py-2 px-4 disabled w-100 font-weight-bold mb-4"
                                aria-disabled="true" style="color:#fff !important;">
                                {{ __('web.sales-agreement') }}
                            </a>
                        @endif
                        @if ($order->salesAgreement && $order->salesAgreement->is_new_agreement)
                            <a href="{{ route('orders.bond-payments-list', ['orderId' => $order->id]) }}" target="_blank"
                                class="btn btn-primary rounded-md w-100 text-white font-weight-bold mb-4"
                                style="color:#fff !important;">
                                {{ __('app.bond-payments-list') }}
                            </a>
                        @endif
                    @else
                        <p>{{ __('web.payment-amount') }}: {{ number_format($paymentInfo['paid_amount'], 2, ',', '.') }}
                            TL</p>
                        <p class="mb-4">{{ __('web.payment-left') }}:
                            {{ number_format($paymentInfo['remaining_amount'], 2, ',', '.') }} TL</p>
                    @endif
                    @if ($order->erp_order_id)
                        <a target="_blank" class="font-weight-bold" style="color: rgb(37, 105, 232) !important;"
                            href="{{ route('remote-sales-pdf', ['orderNo' => $order->order_no]) }}">{{ __('web.distance-sales-contract') }}</a>
                    @endif
                </div>
            </div>

            @if ($order->showLegalRegistrationFormForAdmin())
                <div class="row">
                    <div class="col bg-white p-3">
                        <div class="border-bottom border-dark w-100 mb-4">
                            <span class="h4 font-weight-bold">{{ __('web.artes-title') }}</span>
                        </div>
                        <div class="row w-100">
                            @if ($order->legalRegistration)
                                @if ($order->legalRegistration->approved_by_erp != 'approved')
                                    <div class="col-12">
                                        {{ __('web.parameters') }}:
                                        <p>
                                            <code style="word-break: break-all;">
                                                {{ empty(!$order->legalRegistration->params) ? $order->legalRegistration->params : '-' }}
                                            </code>
                                        </p>
                                        <a class="btn btn-primary mb-4" data-toggle="collapse" href="#paramCollapse"
                                            role="button" aria-expanded="false" aria-controls="paramCollapse">
                                            {{ __('app.show-params-table') }}
                                        </a>
                                        <div class="col-12 row collapse" id="paramCollapse">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">{{ __('app.no') }}</th>
                                                        <th scope="col">{{ __('app.param-value') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($order->legalRegistration->parseParams() as $param)
                                                        <tr>
                                                            <th scope="row text-center">{{ $loop->index }}</th>
                                                            <td style="word-break: break-all;">
                                                                {{ !empty($param) ? $param : '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        {{ __('web.id-card-front') }}: <a target="_blank"
                                            href="{{ $order->legalRegistration->getLink($order->legalRegistration->id_card_front) }}">
                                            {{ $order->legalRegistration->id_card_front }}</a>
                                    </div>
                                    <div class="col-12">
                                        {{ __('web.id-card-back') }}:
                                        <a target="_blank"
                                            href="{{ $order->legalRegistration->getLink($order->legalRegistration->id_card_back) }}">{{ $order->legalRegistration->id_card_back }}</a>
                                    </div>
                                    <div class="col-12">
                                        {{ __('web.signature-circular') }}:
                                        <a target="_blank"
                                            href="{{ $order->legalRegistration->getLink($order->legalRegistration->signature_circular) }}">{{ $order->legalRegistration->signature_circular }}</a>
                                    </div>
                                    <div class="col-12">
                                        {{ __('web.operating-certificate') }}:
                                        <a target="_blank"
                                            href="{{ $order->legalRegistration->getLink($order->legalRegistration->operating_certificate) }}">{{ $order->legalRegistration->operating_certificate }}</a>
                                    </div>
                                    <div class="col-12">
                                        {{ __('web.registry-gazzete') }}:
                                        <a target="_blank"
                                            href="{{ $order->legalRegistration->getLink($order->legalRegistration->registry_gazzete) }}">{{ $order->legalRegistration->registry_gazzete }}</a>
                                    </div>
                                    <div class="col-12">
                                        {{ __('web.circular-indentity-front') }}:
                                        <a target="_blank"
                                            href="{{ $order->legalRegistration->getLink($order->legalRegistration->circular_indentity_front) }}">{{ $order->legalRegistration->circular_indentity_front }}
                                        </a>
                                    </div>
                                    <div class="col-12">
                                        {{ __('web.circular-indentity-back') }}:
                                        <a target="_blank"
                                            href="{{ $order->legalRegistration->getLink($order->legalRegistration->circular_indentity_back) }}">{{ $order->legalRegistration->circular_indentity_back }}</a>
                                    </div>
                                    <div class="col-12">
                                        {{ __('web.power-of-attorney') }}:
                                        <a target="_blank"
                                            href="{{ $order->legalRegistration->getLink($order->legalRegistration->power_of_attorney) }}">{{ $order->legalRegistration->power_of_attorney }}</a>
                                    </div>
                                @endif
                                <div class="col-12 my-3">
                                    <h5 class="font-weight-bold">{{ __('web.approved') }}:
                                        @switch($order->legalRegistration->approved_by_erp)
                                            @case('approved')
                                                <span class="badge badge-success">{{ __('app.yes') }}</span>
                                            @break

                                            @case('declined')
                                                <span class="badge badge-danger">{{ __('app.no') }}</span>
                                            @break

                                            @default
                                                <span class="badge badge-secondary">{{ __('web.pending') }}</span>
                                        @endswitch
                                    </h5>
                                </div>
                            @endif
                            @if (
                                !$order->approvedLegalForm() &&
                                    !$order->pendingLegalForm() &&
                                    !empty($artesResponse) &&
                                    !empty($artesResponse['is_registered']) &&
                                    $artesResponse['is_registered'] == '40')
                                <div class="alert alert-info" role="alert">
                                    {{ __('web.artes-message') }}
                                </div>
                            @endif
                            @if (!$order->approvedLegalForm())
                                @if (empty($order->temprorary_licence_doc_link))
                                    @if ($order->showLegalRegistrationForm())
                                        <div class="col-12">
                                            <a target="_blank"
                                                href="{{ route('legal-registration-form', ['orderNo' => $order->order_no]) }}"
                                                class="btn btn-success rounded py-2 px-4 w-100 text-white mb-2 font-weight-bold">
                                                <i class="fa fa-lg fa-fw fa-external-link-square-alt"></i>
                                                {{ __('web.legal-registration-form') }}
                                            </a>
                                        </div>
                                    @else
                                        @if ($order->pendingLegalForm())
                                            <div class="col-12">
                                                <a
                                                    class="btn btn-secondary rounded py-2 px-4 disabled w-100 text-white mb-2 font-weight-bold">
                                                    <i class="fa fa-lg fa-fw fa-external-link-square-alt"></i>
                                                    {{ __('web.legal-registration-form') }}
                                                </a>
                                            </div>
                                        @else
                                            <div class="col-12">
                                                <a
                                                    class="btn btn-danger rounded py-2 px-4 disabled w-100 text-white mb-2 font-weight-bold">
                                                    <i class="fa fa-lg fa-fw fa-external-link-square-alt"></i>
                                                    {{ __('web.legal-registration-form') }}
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if ($order->isSalesAgreementOrder())
                <div class="row">
                    <div class="col bg-white p-3">
                        <div class="border-bottom border-dark w-100 mb-4">
                            <span class="h4 font-weight-bold">{{ __('app.notary-document') }}</span>
                        </div>
                        <div class="w-100">
                            @if ($notaryStatus)
                                @if (!$notaryStatus['has_uploaded_any'])
                                    <x-adminlte-alert theme="info"
                                        title="{{ __('app.notary-document-was-not-uploaded') }}">
                                        {{ __('app.customer-has-not-uploaded-notary-document') }}
                                    </x-adminlte-alert>
                                @else
                                    <div class="col-12 font-weight-bold mb-4 text-lg">
                                        @if ($notaryStatus['notary_front']['rejected'])
                                            <span class="badge badge-danger">{{ __('app.rejected') }}</span>
                                        @endif
                                        {{ __('app.uploaded-notary-document-front') }}:
                                        @if ($notaryStatus['notary_front']['document'])
                                            <a target="_blank"
                                                href="{{ $notaryStatus['notary_front']['document'] }}">{{ $notaryStatus['notary_front']['document_name'] }}</a>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </div>
                                    <div class="col-12 font-weight-bold mb-4 text-lg">
                                        @if ($notaryStatus['notary_back']['rejected'])
                                            <span class="badge badge-danger">{{ __('app.rejected') }}</span>
                                        @endif
                                        {{ __('app.uploaded-notary-document-back') }}:
                                        @if ($notaryStatus['notary_back']['document'])
                                            <a target="_blank"
                                                href="{{ $notaryStatus['notary_back']['document'] }}">{{ $notaryStatus['notary_back']['document_name'] }}</a>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </div>
                                    <div class="col-12 font-weight-bold mb-4 text-lg">
                                        @if ($notaryStatus['front_side_id']['rejected'])
                                            <span class="badge badge-danger">{{ __('app.rejected') }}</span>
                                        @endif
                                        {{ __('app.uploaded-front-side-id') }}:
                                        @if ($notaryStatus['front_side_id']['document'])
                                            <a target="_blank"
                                                href="{{ $notaryStatus['front_side_id']['document'] }}">{{ $notaryStatus['front_side_id']['document_name'] }}</a>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </div>
                                    @if ($notaryStatus['rejection_reason'])
                                        <x-adminlte-alert theme="danger"
                                            title="{{ __('app.notary-document-was-rejected') }}">
                                            {{ __('app.rejected-document-explanation', ['reason' => $notaryStatus['rejection_reason']]) }}
                                        </x-adminlte-alert>
                                    @else
                                        <form method="POST" class="col-12"
                                            action="{{ route('orders.reject-notary-document', ['orderId' => $order->id]) }}">
                                            @csrf
                                            <h4 class="font-weight-bold">{{ __('app.rejected-documents') }}</h4>
                                            <x-adminlte-select2 name="rejected_documents[]" id="rejected_documents"
                                                multiple required>
                                                <option value="notary_front">{{ __('app.notary-document-front') }}
                                                </option>
                                                <option value="notary_back">{{ __('app.notary-document-back') }}</option>
                                                <option value="front_side_id">{{ __('app.front-side-id') }}</option>
                                            </x-adminlte-select2>
                                            <x-adminlte-textarea name="rejection_reason"
                                                placeholder="{{ __('app.reject-reason') }}" />
                                            <button type="submit" class="btn btn-danger">
                                                {{ __('app.reject-notary-document') }}
                                            </button>
                                        </form>
                                    @endif

                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col bg-white p-3">
                    <div class="border-bottom border-dark w-100 mb-4">
                        <span class="h4 font-weight-bold">{{ __('web.payments') }}</span>
                    </div>
                    <div class="row row-cols-1 row-cols-lg-3 gap-4 w-100">
                        @foreach ($order->orderPaymentsWithDeleted as $payment)
                            @php
                                $ccInfo = $payment->getCreditCardPaymentInfo();
                            @endphp
                            <div class="col">
                                <div class="card border border-light h-100 rounded-0">
                                    <div class="card-body">
                                        @if ($payment->bankAccount)
                                            <div class="d-flex align-items-center justify-content-center mb-4">
                                                <img style="height: 40px;width:auto; max-width: 200px;object-fit:contain;"
                                                    src="{{ $payment->getBankLogo() }}" />
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <p class="text-left">
                                                <span class="font-weight-bold">{{ __('web.payment-type') }}:</span>
                                                {{ $payment->getPaymentTypeTranslation() }}
                                            </p>
                                            @if ($payment->isCreditCardPayment())
                                                <p class="text-left">
                                                    {{ $ccInfo['params'] }}
                                                </p>
                                            @endif
                                            <p class="text-left">
                                                <span class="font-weight-bold">{{ __('web.date') }}:</span>
                                                {{ $payment->created_at->format('d-m-Y H:i') }}
                                            </p>
                                            <p class="text-left">
                                                <span class="font-weight-bold">{{ __('web.payment-amount') }}:</span>
                                                {{ number_format($payment->payment_amount, 2, ',', '.') }}
                                                TL
                                            </p>
                                            <p class="text-left">
                                                <span class="font-weight-bold">{{ __('web.installments') }}:</span>
                                                {{ $payment->number_of_installments }}
                                                @if ($ccInfo['collectedAmount'])
                                                    ({{ $ccInfo['collectedAmount'] }} TL)
                                                @endif
                                            </p>
                                            <p class="text-left">
                                                <span class="font-weight-bold">{{ __('web.approved') }}:</span>
                                                <span
                                                    class="{{ $payment->approved_by_erp === 'N' ? 'text-danger' : 'text-success' }}">{{ $payment->approved_by_erp === 'Y' ? __('app.yes') : __('app.no') }}</span>
                                            </p>
                                            @if ($payment->failed)
                                                <p class="text-left">
                                                    <span class="font-weight-bold">{{ __('app.status') }}:</span> <span
                                                        class="font-weight-bold text-danger">{{ __('app.failed-payment') }}</span>
                                                </p>
                                            @endif
                                            @if ($payment->is_fee_payment)
                                                <p class="text-left">
                                                    <span
                                                        class="font-weight-bold">{{ __('app.application-fee') }}:</span>
                                                    <span
                                                        class="font-weight-bold text-success">{{ __('app.yes') }}</span>
                                                </p>
                                            @endif
                                            @if ($payment->e_bond_no)
                                                <p class="text-left">
                                                    <span class="font-weight-bold">{{ __('app.bond-no') }}:</span>
                                                    <span
                                                        class="font-weight-bold bg-primary rounded p-2">{{ $payment->e_bond_no }}</span>
                                                </p>
                                            @endif
                                            @if ($payment->deleted_at)
                                                <p class="text-left">
                                                    <span class="font-weight-bold">{{ __('web.deleted-at') }}:</span>
                                                    <span
                                                        class="font-weight-bold text-danger">{{ $payment->deleted_at->format('d-m-Y H:i') }}</span>
                                                </p>
                                            @endif
                                        </div>
                                        @if ($payment->approved_by_erp === 'N' && !$payment->failed && !$payment->deleted_at)
                                            <form method="POST"
                                                action="{{ route('orders.cancel-payment', ['paymentId' => $payment->id]) }}">
                                                @method('post')
                                                @csrf
                                                <button type="submit" class="btn btn-danger">
                                                    {{ __('web.cancel-payment') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
