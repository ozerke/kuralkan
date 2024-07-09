@extends('adminlte::page')

@php
    $heads = [
        __('web.bond-no'),
        __('web.date'),
        __('web.payment-amount'),
        __('web.remaining-amount'),
        __('app.description'),
        __('web.paid'),
    ];

    $config = [
        'paging' => false,
        'searching' => true,
        'ordering' => false,
        'columns' => [null, ['orderable' => false], null, null, null, null],
    ];
@endphp

@section('content')
    @include('layouts.messages')
    <div class="row pt-4">
        <div class="col-12 col-lg-6 bg-white p-3 mb-4">
            <h3 class="mb-4">{{ __('app.bond-payments-list') }}</h3>
            <div class="text-muted">{{ __('web.order-number') }}</div>
            <div class="font-weight-bold">{{ $order->order_no }}</div>
            <div class="text-muted">{{ __('app.erp-id-findeks-id') }}</div>
            <div class="font-weight-bold">{{ $order->erp_prefix }} {{ $order->erp_order_id ?? '-' }} /
                {{ $order->salesAgreement->findeks_request_id ?? '-' }}</div>
        </div>
        <div class="col-12 col-lg-6 bg-white p-3 mb-4">
            <div class="border-bottom border-dark mb-2">
                <span class="h4 font-weight-bold">{{ __('web.payment-information') }}</span>
            </div>
            <p>{{ __('web.order-amount') }}: {{ number_format($order->total_amount, 2, ',', '.') }} TL</p>
            <p>{{ __('web.payment-type') }}: {{ $order->getOrderPaymentType(true) }}</p>

            <p>{{ __('web.down-payment') }}: {{ $order->salesAgreement->down_payment_amount ?? '-' }}
                TL</p>
            <p>{{ __('web.installment-amount') }}: {{ $order->salesAgreement->monthly_payment ?? '-' }}
                TL/{{ __('web.month') }}</p>
            <p>{{ __('web.installments') }}: {{ $order->salesAgreement->number_of_installments ?? '-' }}</p>
            @if (!empty($order->salesAgreement->agreement_document_link))
                <a href="{{ $order->salesAgreement->agreement_document_link }}" target="_blank"
                    class="btn btn-success rounded-md py-2 px-4 w-auto font-weight-bold mb-4"
                    style="color:#fff !important;">
                    {{ __('web.sales-agreement') }}
                </a>
            @else
                <a class="btn btn-danger rounded-md py-2 px-4 disabled w-auto font-weight-bold mb-4" aria-disabled="true"
                    style="color:#fff !important;">
                    {{ __('web.sales-agreement') }}
                </a>
            @endif

        </div>
    </div>
    <x-adminlte-datatable id="bond-payments-table" :heads="$heads" :config="$config" beautify striped hoverable
        head-theme="dark">
        @foreach ($paymentsList as $bondPayment)
            <tr style="white-space:nowrap;width:100%;">
                <td class="text-center">{{ $bondPayment->e_bond_no }}</td>
                <td class="text-center">{{ $bondPayment->due_date->format('d-m-Y') }}</td>
                <td class="text-center">{{ number_format($bondPayment->bond_amount, 2, ',', '.') }} TL</td>
                <td class="text-center">{{ number_format($bondPayment->remaining_amount, 2, ',', '.') }} TL</td>
                <td class="text-center">{{ $bondPayment->bond_description }}</td>
                <td class="text-center">
                    <span
                        class="font-weight-bold {{ !$bondPayment->isPaid() ? 'text-red' : 'text-green' }}">{{ !$bondPayment->isPaid() ? __('app.no') : __('app.yes') }}</span>
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>

@stop
