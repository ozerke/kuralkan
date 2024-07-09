@extends('adminlte::page')

@section('content_header')
    <h1>{{ __('app.sms-templates') }}</h1>
@stop

@php
    $heads = [__('app.title'), __('app.key'), ['label' => __('app.action'), 'no-export' => true, 'width' => 10]];

    $config = [
        'paging' => false,
        'searching' => true,
        'columns' => [null, null, ['orderable' => false]],
    ];
@endphp

@section('content')
    @include('layouts.messages')
    <x-adminlte-datatable id="sms-table" :heads="$heads" :config="$config" beautify striped hoverable head-theme="dark">
        @foreach ($templates as $key => $template)
            <tr style="white-space:wrap;width:100%;">

                <td>{{ $template['title'] ?? '' }}</td>
                <td>{{ $key }}</td>
                <td class="d-flex">
                    <a class="btn btn-xs btn-default text-primary mx-1" title="Edit section"
                        href="{{ route('templates.sms.edit', $key) }}">
                        <i class="fa fa-lg fa-fw fa-pen"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>

@stop
