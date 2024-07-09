@extends('adminlte::page')

@section('content_header')
    <h1>{{ __('app.campaigns') }}</h1>
@stop

@php
    $heads = [
        __('app.product'),
        __('app.down-payment') . ', %',
        __('app.installments'),
        __('app.rate') . ', %',
        __('app.bt-payment-exp-code'),
        ['label' => __('app.action'), 'no-export' => true, 'width' => 10],
    ];

    $config = [
        'paging' => false,
        'searching' => true,
        'columns' => [null, null, null, null, null, ['orderable' => false]],
    ];
@endphp

@section('content')
    @include('layouts.messages')
    <a class="btn btn-primary" href="{{ route('campaigns.create') }}">{{ __('app.create') }}</a>
    <x-adminlte-datatable id="campaigns-table" :heads="$heads" :config="$config" beautify striped hoverable
        head-theme="dark">
        @foreach ($campaigns as $campaign)
            <tr style="white-space:nowrap;width:100%;">

                <td class="text-center">{{ $campaign->product->currentTranslation->product_name }}</td>
                <td class="text-center">{{ $campaign->down_payment }}</td>
                <td class="text-center">{{ $campaign->installments }}</td>
                <td class="text-center">{{ number_format($campaign->rate, 0) }}</td>
                <td class="text-center">{{ $campaign->bt_payment_exp_code }}</td>
                <td class="d-flex">
                    <a class="btn btn-xs btn-default text-primary mx-1" title="Edit campaign"
                        href="{{ route('campaigns.edit', $campaign->id) }}">
                        <i class="fa fa-lg fa-fw fa-pen"></i>
                    </a>
                    <form method="POST" action="{{ route('campaigns.destroy', $campaign->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-danger text-white mx-1" title="Delete">
                            <i class="fa fa-lg fa-fw fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>

@stop
