@extends('adminlte::page')

@section('plugins.DateRangePicker', true)

@section('content_header')
    <h1>
        @if ($bonds)
            {{ __('web.bond-payment') }}
        @else
            {{ __('app.user-payments') }}
        @endif
    </h1>
@stop

@php
    if ($bonds) {
        $heads = [
            __('app.order-no'),
            __('app.bond-no'),
            __('app.order-date'),
            __('app.customer'),
            __('app.payment-type'),
            __('app.payment-date'),
            __('app.payment-amount'),
            __('app.collected-payment'),
            __('app.installments'),
            __('app.bank-name'),
            __('app.description'),
            __('app.approved'),
            ['label' => __('app.action'), 'no-export' => true, 'width' => 10],
        ];

        $config = [
            'paging' => false,
            'searching' => false,
            'columns' => [
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
            ],
        ];
    } else {
        $heads = [
            __('app.order-no'),
            __('app.order-date'),
            __('app.customer'),
            __('app.payment-type'),
            __('app.payment-date'),
            __('app.payment-amount'),
            __('app.collected-payment'),
            __('app.installments'),
            __('app.bank-name'),
            __('app.description'),
            __('app.approved'),
            ['label' => __('app.action'), 'no-export' => true, 'width' => 10],
        ];

        $config = [
            'paging' => false,
            'searching' => false,
            'columns' => [
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
                ['orderable' => false],
            ],
        ];
    }

    $range = request()->get('date-range') ? explode(' - ', request()->get('date-range')) : null;

    $dateConfig = [
        'timePicker' => false,
        'startDate' => $range ? Carbon\Carbon::parse($range[0])->format('d-m-Y') : 'js:moment().subtract(1, "months")',
        'endDate' => $range ? Carbon\Carbon::parse($range[1])->format('d-m-Y') : 'js:moment()',
        'locale' => ['format' => 'DD-MM-YYYY'],
    ];
@endphp

@section('content')
    @include('layouts.messages')
    <form action="{{ $bonds ? route('users.bond-payments', $userId) : route('users.payments', $userId) }}" method="get"
        id="filter-form">
        <div class="row mb-4">
            <div class="col-12 col-lg-4">
                <x-adminlte-date-range :config="$dateConfig" name="date-range" id="date-range"
                    label="{{ __('app.date-range') }}" />
            </div>
            <div class="col-12 col-lg-4">
                <x-adminlte-select label="{{ __('app.payment-type') }}" name="payment-type" id="payment-type">
                    <option value="">-</option>
                    <option value="H" @if (request()->get('payment-type') == 'H') selected @endif>{{ __('web.bank-transfer') }}
                    </option>
                    <option value="K" @if (request()->get('payment-type') == 'K') selected @endif>{{ __('web.credit-card') }}
                    </option>
                </x-adminlte-select>
            </div>
            <div class="col-12 col-lg-4">
                <x-adminlte-select label="{{ __('app.bank-name') }}" name="bank" id="bank">
                    <option value="">-</option>
                    @foreach ($banks as $bank)
                        <option value="{{ $bank->id }}" @if (request()->get('bank') == $bank->id) selected @endif>
                            {{ $bank->erp_bank_name }}</option>
                    @endforeach
                </x-adminlte-select>
            </div>
            <div class="col-12">
                @include('layouts.searchbar')
            </div>
            <div class="col-12 d-flex justify-start align-items-center mb-4">
                {{ __('app.sort-by-score') }}:
                <input name="sort_by_weight" id="sort_by_weight" style="width:20px;height:20px;margin-left: 10px;"
                    type="checkbox" class="toggle-new-product" @if (request()->sort_by_weight === 'on') checked @endif />
            </div>
        </div>
    </form>

    <x-adminlte-datatable id="user-payments-table" :heads="$heads" :config="$config" beautify striped hoverable
        head-theme="dark">
        @foreach ($payments as $payment)
            <tr style="white-space:nowrap;width:100%;">
                <td>{{ $payment->order->order_no }}</td>
                @if ($bonds)
                    <td>{{ $payment->e_bond_no }}</td>
                @endif
                <td>{{ $payment->order->created_at->format('d-m-Y H:i') }}</td>
                <td>{{ $payment->user->full_name }}</td>
                <td>{{ $payment->getPaymentTypeTranslation() }}</td>
                <td>{{ $payment->created_at->format('d-m-Y H:i') }}</td>
                <td>{{ number_format($payment->payment_amount, 2, ',', '.') }}</td>
                <td>{{ number_format($payment->collected_payment, 2, ',', '.') }}</td>
                <td>{{ $payment->number_of_installments }}</td>
                <td>{{ optional($payment->bankAccount)->bank->erp_bank_name ?? '-' }}</td>
                <td>{{ $payment->description }}</td>
                <td class="text-bold {{ $payment->approved_by_erp === 'N' ? 'text-danger' : 'text-success' }}">
                    {{ $payment->approved_by_erp === 'Y' ? __('app.yes') : __('app.no') }}</td>
                <td style="text-align: left">
                    <a class="btn btn-xs btn-default text-primary mx-1" title="View"
                        href="{{ route('orders.details', ['orderId' => $payment->order_id]) }}">
                        <i class="fa fa-lg fa-fw fa-eye"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>

    {{ $payments->appends(request()->query())->onEachSide(0)->links() }}

@stop

@section('js')
    <script>
        $('#date-range').on('apply.daterangepicker', function(ev, picker) {
            $("#filter-form").submit();
        });

        $("#bank, #payment-type").on('change', function() {
            $("#filter-form").submit();
        });
    </script>
@endsection
