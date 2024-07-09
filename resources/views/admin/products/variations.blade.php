@extends('adminlte::page')

@section('content_header')
    <h1>
        {{ __('app.product-variations') }}</h1>
@stop

@php
    $heads = [
        __('app.product'),
        __('app.thumbnail'),
        __('app.color'),
        __('app.color_code'),
        __('app.stock'),
        __('app.price'),
        __('app.delivery_date'),
        __('app.updated-at'),
        __('app.display'),
        ['label' => __('app.action'), 'no-export' => true, 'width' => 10],
    ];

    $config = [
        'paging' => true,
        'searching' => true,
        'columns' => [null, ['orderable' => false], null, null, null, null, null, null, null, ['orderable' => false]],
    ];
@endphp

@section('content')
    @include('layouts.messages')
    <x-adminlte-datatable id="variations-table" :heads="$heads" :config="$config" beautify striped hoverable
        head-theme="dark">
        @foreach ($variations as $variation)
            <tr style="white-space:nowrap;width:100%;">

                <td>{{ $product->currentTranslation->product_name }}</td>
                <td class="cell-center">
                    @if ($variation->firstMedia)
                        <img src="{{ $variation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}"
                            class="rounded-sm elevation-1" alt="Media item" height="40" width="40">
                    @else
                        -
                    @endif
                </td>
                <td class="cell-center">
                    @if ($variation->color->color_image_url)
                        <img src="{{ $variation->color->color_image_url }}" class="rounded-full elevation-1"
                            alt="Color media item" height="40" width="40">
                    @else
                        {{ $variation->color->currentTranslation->color_name }}
                    @endif
                </td>
                <td>{{ $variation->color->color_code }}</td>
                <td class="cell-right">{{ $variation->total_stock }}</td>
                <td class="cell-right">{{ number_format($variation->price, 2, ',', '.') }}</td>
                <td class="cell-right">{{ $variation->estimated_delivery_date }}</td>
                <td class="cell-right">{{ $variation->updated_at }}</td>

                <td>
                    <input id="{{ $variation->id }}-display" style="width:20px;height:20px" type="checkbox"
                        class="toggle-display" @if ($variation->display === 't') checked @endif />
                </td>
                <td>
                    <a class="btn btn-xs btn-default text-primary mx-1" title="Edit Details"
                        href="{{ route('variations.edit', $variation->id) }}">
                        <i class="fa fa-lg fa-fw fa-pen"></i>
                    </a>
                    <a class="btn btn-xs btn-default text-primary mx-1" title="Shop Stocks"
                        href="{{ route('variations.shop-stocks', $variation->id) }}"">
                        <i class=" fa fa-lg fa-fw fa-store"></i>
                    </a>
                    <a class="btn btn-xs btn-default text-primary mx-1" title="Orders"
                        href="{{ route('variations.orders', $variation->id) }}">
                        <i class="fa fa-lg fa-fw fa-shopping-cart"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>
@stop

@section('js')
    <script>
        $(".toggle-display").click(function() {
            const variationId = $(this).attr('id').split('-display');

            fetch(`/panel/admin/variations/toggle-display/${variationId[0]}`, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    "Content-Type": "application/json",
                }

            })

        });
    </script>
@endsection
