@extends('adminlte::page')

@section('content_header')
    <h1>
        {{ __('app.products_list') }}</h1>
@stop

@php
    $heads = [
        __('app.product'),
        __('app.thumbnail'),
        __('app.stock_code'),
        __('app.stock'),
        __('app.price'),
        __('app.variations'),
        __('app.updated-at'),
        __('app.new_product'),
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

    <x-adminlte-datatable id="products-table" :heads="$heads" :config="$config" beautify striped hoverable
        head-theme="dark">
        @foreach ($products as $product)
            <tr style="white-space:nowrap;width:100%;">

                <td>{{ $product->currentTranslation->product_name }} <a href="{{ $product->detailsUrl() }}" target="_blank"><i
                            class="fa fa-lg fa-fw fa-external-link-square-alt"></i></a></td>
                <td class="cell-center">
                    @if (optional($product->firstDisplayableVariation)->firstMedia)
                        <img src="{{ $product->firstDisplayableVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}"
                            class="rounded-sm elevation-1" alt="Media item" height="40" width="40">
                    @else
                        -
                    @endif
                </td>
                <td>{{ $product->stock_code }}</td>
                <td class="cell-right">{{ $product->getTotalStock() }}</td>
                <td class="cell-right">
                    {{ number_format(optional($product->firstDisplayableVariation)->price, 2, ',', '.') }}</td>
                <td class="cell-right">
                    @if ($product->hasNewVariations())
                        <span style="color:red;font-weight: bold;">(*)</span>
                    @endif {{ $product->variations()->count() }}
                </td>
                <td class="cell-right">{{ $product->updated_at }}</td>
                <td class="cell-center">
                    <input id="{{ $product->id }}-new-product" style="width:20px;height:20px" type="checkbox"
                        class="toggle-new-product" @if ($product->new_product === 'Y') checked @endif />
                </td>
                <td class="cell-center">
                    <input id="{{ $product->id }}-display" style="width:20px;height:20px" type="checkbox"
                        class="toggle-display" @if ($product->display === 't') checked @endif />
                </td>
                <td class="cell-center">
                    <a class="btn btn-xs btn-default text-primary mx-1" title="Edit Details"
                        href="{{ route('products.edit', $product->id) }}">
                        <i class="fa fa-lg fa-fw fa-pen"></i>
                    </a>
                    <a class="btn btn-xs btn-default text-primary mx-1" title="Variations"
                        href="{{ route('variations.index', $product->id) }}">
                        <i class="fa fa-lg fa-fw fa-layer-group"></i>
                    </a>
                    <a class="btn btn-xs btn-default text-primary mx-1" title="Specifications"
                        href="{{ route('specifications.index', $product->id) }}">
                        <i class="fa fa-lg fa-fw fa-list"></i>
                    </a>
                    <a class="btn btn-xs btn-default text-primary mx-1" title="Media"
                        href="{{ route('products.edit-images', $product->id) }}">
                        <i class="fa fa-lg fa-fw fa-image"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>

@stop

@section('js')
    <script>
        $(".toggle-display").click(function() {
            const productId = $(this).attr('id').split('-display');

            fetch(`/panel/admin/toggle-display/${productId[0]}`, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    "Content-Type": "application/json",
                }

            })

        });

        $(".toggle-new-product").click(function() {
            const productId = $(this).attr('id').split('-new-product');

            fetch(`/panel/admin/toggle-new-product/${productId[0]}`, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    "Content-Type": "application/json",
                }

            })

        });
    </script>
@endsection
