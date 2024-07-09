@extends('adminlte::page')

@section('plugins.DateRangePicker', true)

@section('content_header')
    <h1>
        {{ __('app.orders') }}</h1>
@stop

@php
    $heads = [
        __('app.order-date'),
        __('app.order-no'),
        __('app.product'),
        __('app.color-image'),
        __('app.ordering-user'),
        __('app.invoice-user'),
        __('app.order-amount'),
        __('app.payment-amount'),
        __('app.payment-type'),
        __('app.status'),
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
        ],
    ];

    $range = request()->get('date-range') ? explode(' - ', request()->get('date-range')) : null;

    $dateConfig = [
        'timePicker' => false,
        'startDate' => $range ? Carbon\Carbon::parse($range[0])->format('d-m-Y') : 'js:moment()',
        'endDate' => $range ? Carbon\Carbon::parse($range[1])->format('d-m-Y') : 'js:moment()',
        'locale' => ['format' => 'DD-MM-YYYY'],
    ];
@endphp

@section('content')
    @include('layouts.messages')
    <form action="{{ route('orders.index') }}" method="get" id="filter-form" class="row">
        <div class="col-12 col-lg-6">
            <x-adminlte-date-range :config="$dateConfig" name="date-range" id="date-range" label="{{ __('app.date-range') }}" />
        </div>

        <x-adminlte-select id='status-input' name="status" label-class="text-dark" igroup-size="md"
            fgroup-class="col-12 col-lg-6" label="{{ __('app.filter-by-status') }}">
            <option value="" @if (request()->status == null) selected @endif>{{ __('app.all-statuses') }}</option>
            @foreach ($orderStatuses as $status)
                <option value="{{ $status->id }}" @if (request()->status == $status->id) selected @endif>
                    {{ $status->currentTranslation->status }}</option>
            @endforeach
        </x-adminlte-select>
        <div class="col-12">
            @include('layouts.searchbar')
        </div>

        <div class="col-12 d-flex justify-start align-items-center mb-4">
            {{ __('app.sort-by-score') }}:
            <input name="sort_by_weight" id="sort_by_weight" style="width:20px;height:20px;margin-left: 10px;"
                type="checkbox" class="toggle-new-product" @if (request()->sort_by_weight === 'on') checked @endif />
        </div>
    </form>

    <div class="row d-flex justify-content-between mb-4 mx-0">
        <div class="card-col" data-status="1">
            <div class="w-full bg-white d-flex flex-column rounded px-3 py-1 shadow"
                style="border-bottom: 4px solid gray;gap:10px;min-height:120px;">
                <div class="text-bold text-lg mt-2">
                    {{ number_format($awaiting) }}
                </div>
                <div class="text-md text-muted text-uppercase mb-2">
                    {{ $translations['awaiting'] }}
                </div>
            </div>
        </div>
        <div class="card-col" data-status="2">
            <div class="w-full bg-white d-flex flex-column rounded px-3 py-1 shadow"
                style="border-bottom: 4px solid #3762EA;gap:10px;min-height:120px;">
                <div class="text-bold text-lg mt-2">
                    {{ number_format($confirmed) }}
                </div>
                <div class="text-md text-muted text-uppercase mb-2">
                    {{ $translations['confirmed'] }}
                </div>
            </div>
        </div>
        <div class="card-col" data-status="3">
            <div class="w-full bg-white d-flex flex-column rounded px-3 py-1 shadow"
                style="border-bottom: 4px solid #F5B749;gap:10px;min-height:120px;">
                <div class="text-bold text-lg mt-2">
                    {{ number_format($supplying) }}
                </div>
                <div class="text-md text-muted text-uppercase mb-2">
                    {{ $translations['supplying'] }}
                </div>
            </div>
        </div>
        <div class="card-col" data-status="4">
            <div class="w-full bg-white d-flex flex-column rounded px-3 py-1 shadow"
                style="border-bottom: 4px solid #2CCB73;gap:10px;min-height:120px;">
                <div class="text-bold text-lg mt-2">
                    {{ number_format($servicePoint) }}
                </div>
                <div class="text-md text-muted text-uppercase mb-2">
                    {{ $translations['servicePoint'] }}
                </div>
            </div>
        </div>
        <div class="card-col" data-status="5">
            <div class="w-full bg-white d-flex flex-column rounded px-3 py-1 shadow"
                style="border-bottom: 4px solid #FE6C6C;gap:10px;min-height:120px;">
                <div class="text-bold text-lg mt-2">
                    {{ number_format($delivered) }}
                </div>
                <div class="text-md text-muted text-uppercase mb-2">
                    {{ $translations['delivered'] }}
                </div>
            </div>
        </div>
    </div>

    <x-adminlte-datatable id="orders-table" :heads="$heads" :config="$config" beautify striped hoverable
        head-theme="dark">
        @foreach ($orders as $order)
            <tr style="white-space:nowrap;width:100%;" @class(['bg-accent' => $order->erp_prefix === 'SK'])>
                <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                <td>{{ $order->order_no }}</td>
                <td>{{ $order->productVariation->product->currentTranslation->product_name }}</td>
                <td>
                    @if ($order->productVariation->color->color_image_url)
                        <img src="{{ $order->productVariation->color->color_image_url }}" class="rounded-full elevation-1"
                            alt="Color media item" height="30" width="30">
                    @else
                        -
                    @endif
                </td>

                <td>{{ substr($order->user->full_name, 0, 25) }}<br />({{ $order->user->erp_user_id }})</td>
                <td>{{ $order->invoiceUser->full_name }}<br />({{ $order->invoiceUser->erp_user_id }})</td>
                <td>{{ number_format($order->total_amount, 2, ',', '.') }}</td>
                <td>{{ number_format($order->getOrderPaymentsState()['paid_amount'], 2, ',', '.') }}</td>
                <td>{{ $order->getOrderPaymentType(true) }}</td>
                @if ($order->latestStatusHistory && $order->latestStatusHistory->orderStatus->currentTranslation->status)
                    <td>{{ substr($order->latestStatusHistory->orderStatus->currentTranslation->status, 0, 25) }}</td>
                @else
                    <td>{{ __('web.pending') }}</td>
                @endif
                <td style="text-align: left">
                    <a class="btn btn-xs btn-default text-primary mx-1" title="View"
                        href="{{ route('orders.details', ['orderId' => $order->id]) }}">
                        <i class="fa fa-lg fa-fw fa-eye"></i>
                    </a>
                    <a class="btn btn-xs btn-default text-primary mx-1" title="History"
                        href="{{ route('orders.status-history', ['orderId' => $order->id]) }}">
                        <i class="fa fa-lg fa-fw fa-history"></i>
                    </a>
                    <a class="btn btn-xs btn-default text-primary mx-1" title="Support tickets" href="#">
                        <i class="fa fa-lg fa-fw fa-headset"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>

    {{ $orders->appends(request()->query())->onEachSide(0)->links() }}

@stop

@section('js')
    <script>
        $('#date-range').on('apply.daterangepicker', function(ev, picker) {
            $("#filter-form").submit();
        });

        $(".card-col").on('click', function() {
            $("#status-input").val($(this).attr('data-status'));
            $("#filter-form").submit();
        });

        $("#status-input").on('change', function() {
            $("#filter-form").submit();
        });
    </script>
@endsection
