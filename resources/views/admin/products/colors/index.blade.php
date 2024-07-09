@extends('adminlte::page')

@section('content_header')
<h1>{{__('app.colors_list')}}</h1>
@stop

@php
$heads = [__('app.color_code'), __('app.color_name_tr'), __('app.color_name_en'), __('app.erp_color_name'), __('app.color_image'), ['label' => __('app.action'), 'no-export' => true, 'width' => 10]];

$config = [
'paging' => true,
'searching' => true,
'columns' => [null, null, null, null, null,['orderable' => false]],
];
@endphp

@section('content')
@include('layouts.messages')
<x-adminlte-datatable id="colors-table" :heads="$heads" :config="$config" beautify striped hoverable head-theme="dark">
    @foreach ($colors as $color)

    <tr style="white-space:nowrap;width:100%;">
        <td>{{ $color->color_code }}</td>
        <td>{{ optional($color->getTranslationByKey('tr'))->color_name ?? '-' }}</td>
        <td>{{ optional($color->getTranslationByKey('en'))->color_name ?? '-' }}</td>
        <td>{{ $color->erp_color_name }}</td>
        <td>@if($color->color_image_url) <img src="{{$color->color_image_url}}" class="rounded-full elevation-1" alt="Color image item" height="40" width="40"> @else - @endif</td>
        <td>
            <a class="btn btn-xs btn-default text-primary mx-1" title="Edit color" href="{{ route('colors.edit', $color->id) }}">
                <i class="fa fa-lg fa-fw fa-pen"></i>
            </a>
        </td>
    </tr>
    @endforeach
</x-adminlte-datatable>

@stop
