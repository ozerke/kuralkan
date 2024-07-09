@extends('adminlte::page')

@section('content_header')
<h1>{{__('app.technical-specifications')}}</h1>
@stop

@php
$heads = [__('app.display-order'), __('app.technical-specification-tr'), __('app.technical-specification-en'), __('app.values-tr'), __('app.values-en')];

$config = [
'paging' => false,
'searching' => true,
'columns' => [null, null, null, null, null],
];
@endphp

@section('content')
@include('layouts.messages')
<h4>{{__('app.stock_code')}}: <b>{{$product->stock_code}}</b></h4>
<x-adminlte-datatable id="specifications-table" :heads="$heads" :config="$config" beautify striped hoverable head-theme="dark">
    @foreach ($specifications as $displayOrder => $specification)
    <tr style="white-space:nowrap;width:100%;">
        <td>{{ $displayOrder }}</td>
        <td>{{ $specification['tr']->specification }}</td>
        <td style="min-width: 250px">
            <p style="display: none">{{$specification['en']->specification}}</p>
            <x-adminlte-input name="en-specification" class="specification-update" data-specification-id="{{$specification['en']->id}}" placeholder="..." disable-feedback value="{{$specification['en']->specification}}" fgroup-class="mb-0" />
        </td>
        <td>{{ $specification['tr']->value }}</td>
        <td style="min-width: 250px">
            <p style="display: none">{{$specification['en']->value}}</p>
            <x-adminlte-input name="en-value" class="value-update" data-specification-id="{{$specification['en']->id}}" placeholder="..." disable-feedback value="{{$specification['en']->value}}" fgroup-class="mb-0" />
        </td>
    </tr>
    @endforeach
</x-adminlte-datatable>
@stop

@section('js')
<script>
    let timer;

    function debounce(fn, delay) {
        return (() => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(), delay);
        })();
    };

    $(".specification-update").on('input', function() {
        const value = $(this).val();
        const specificationId = $(this).attr('data-specification-id');

        debounce(() => {
            fetch(`/panel/admin/products/specifications/${specificationId}/update-specification`, {
                method: 'POST'
                , credentials: 'same-origin'
                , headers: {
                    "Content-Type": "application/json"
                , }
                , body: JSON.stringify({
                    value
                })

            }).then(data => {
                if (data.status == 200) {
                    return data.json();
                } else {
                    throw new Error('Error occured')
                }
            }).catch(e => alert(e.message))
        }, 500);
    });

    $(".value-update").on('input', function() {
        const value = $(this).val();
        const specificationId = $(this).attr('data-specification-id');

        debounce(() => {
            fetch(`/panel/admin/products/specifications/${specificationId}/update-value`, {
                method: 'POST'
                , credentials: 'same-origin'
                , headers: {
                    "Content-Type": "application/json"
                , }
                , body: JSON.stringify({
                    value
                })

            }).then(data => {
                if (data.status == 200) {
                    return data.json();
                } else {
                    throw new Error('Error occured')
                }
            }).catch(e => alert(e.message))
        }, 500);
    });

</script>
@endsection
