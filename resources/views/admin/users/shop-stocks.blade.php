@extends('adminlte::page')

@section('content_header')
    <h1>
        {{ __('app.shop-stocks') }}: <b>{{ $shop->getErpName() }}</b></h1>
@stop

@php
    $heads = [
        __('app.product'),
        __('app.thumbnail'),
        __('app.color'),
        __('app.color_code'),
        __('app.sales-point'),
        __('app.stock'),
    ];

    $config = [
        'paging' => true,
        'searching' => true,
        'columns' => [null, ['orderable' => false], null, null, null, null],
    ];
@endphp

@section('content')
    @include('layouts.messages')
    <div class="d-flex justify-content-between align-items-center my-2">
        <h4>{{ __('app.total') }}: <span class="badge bg-primary">{{ $totalStock }}</span></h4>
    </div>
    <x-adminlte-datatable id="shop-stocks-table" :heads="$heads" :config="$config" beautify striped hoverable
        head-theme="dark">
        @foreach ($shopStocks as $shopStock)
            <tr style="white-space:nowrap;width:100%;">

                <td>{{ $shopStock->product->currentTranslation->product_name }}</td>
                <td>
                    @if ($shopStock->variation->firstMedia)
                        <img src="{{ $shopStock->variation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}"
                            class="rounded-sm elevation-1" alt="Media item" height="40" width="40">
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if ($shopStock->variation->color->color_image_url)
                        <img src="{{ $shopStock->variation->color->color_image_url }}" class="rounded-full elevation-1"
                            alt="Color media item" height="40" width="40">
                    @else
                        -
                    @endif
                </td>
                <td>{{ $shopStock->variation->color->color_code }}</td>
                <td>
                    @if ($shopStock->shop->getShopDetails())
                        {{ substr($shopStock->shop->getShopDetails(), 0, 25) }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $shopStock->stock }}</td>
            </tr>
        @endforeach
    </x-adminlte-datatable>
@stop
