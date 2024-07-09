@extends('adminlte::page')

@section('content_header')
    <h1 class="mb-2">{{ __('app.order-status-history') }}: {{ $order->order_no }}</h1>
    <span class="text-lg bg-primary rounded p-2">{{ __('app.erp-id') }}: {{ $order->erp_order_id }}</span>
@stop

@php
    $heads = [__('app.date'), __('app.status'), __('app.created-by')];

    $config = [
        'paging' => false,
        'searching' => true,
        'columns' => [null, null, null],
    ];
@endphp

@section('content')
    @include('layouts.messages')

    <x-adminlte-datatable id="status-table" :heads="$heads" :config="$config" beautify striped hoverable head-theme="dark">
        @foreach ($orderHistory as $history)
            <tr style="white-space:nowrap;width:100%;">
                <td class="text-center">{{ $history->created_at }}</td>
                <td class="text-center">{{ $history->orderStatus->currentTranslation->status }}</td>
                <td class="text-center"><a
                        href="{{ route('users.edit', ['user' => $history->user]) }}">{{ $history->user->full_name }}</a></td>
            </tr>
        @endforeach
    </x-adminlte-datatable>

@stop
