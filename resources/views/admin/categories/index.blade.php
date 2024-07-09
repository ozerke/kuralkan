@extends('adminlte::page')

@section('content_header')
<h1>{{__('app.categories')}}</h1>
@stop

@php
$heads = [__('app.display-order'), __('app.category-tr'), __('app.category-en'), __('app.slug-tr'), __('app.slug-en'), __('app.number-of-products'), __('app.action')];

$config = [
'paging' => false,
'searching' => true,
'columns' => [null, null, null, null, null, null, ['orderable' => false]],
];
@endphp

@section('content')
@include('layouts.messages')
<div class="d-flex justify-content-between align-items-center my-2">
    <h4>{{__('app.total')}}: <span class="badge bg-primary">{{$count}}</span></h4>

    <a class="btn btn-md btn-primary" href="{{ route('categories.create') }}">
        <i class="fa fa-lg fa-fw fa-plus text-sm"></i> {{__('app.create')}}
    </a>

</div>

<x-adminlte-datatable id="categories-table" :heads="$heads" :config="$config" beautify striped hoverable head-theme="dark">
    @foreach ($categories as $category)
    <tr style="white-space:nowrap;width:100%;">
        <td>
            <p style="display: none">{{$category->display_order}}</p>
            <x-adminlte-input name="display_order" class="display-order-update" data-id="{{$category->id}}" placeholder="..." disable-feedback value="{{$category->display_order}}" fgroup-class="mb-0" />
        </td>
        <td style="min-width: 250px">
            <p style="display: none">{{$category->tr->category_name}}</p>
            <x-adminlte-input name="tr-category-name" class="category-update" data-id="{{$category->tr->id}}" placeholder="..." disable-feedback value="{{$category->tr->category_name}}" fgroup-class="mb-0" />
        </td>
        <td style="min-width: 250px">
            <p style="display: none">{{$category->en->category_name}}</p>
            <x-adminlte-input name="en-category-name" class="category-update" data-id="{{$category->en->id}}" placeholder="..." disable-feedback value="{{$category->en->category_name}}" fgroup-class="mb-0" />
        </td>
        <td style="min-width: 250px">
            <p style="display: none">{{$category->tr->slug}}</p>
            <x-adminlte-input name="tr-slug" class="slug-update" data-id="{{$category->tr->id}}" placeholder="..." disable-feedback value="{{$category->tr->slug}}" fgroup-class="mb-0" />
        </td>
        <td style="min-width: 250px">
            <p style="display: none">{{$category->en->slug}}</p>
            <x-adminlte-input name="en-slug" class="slug-update" data-id="{{$category->en->id}}" placeholder="..." disable-feedback value="{{$category->en->slug}}" fgroup-class="mb-0" />
        </td>
        <td>{{ $category->getNumberOfProducts() }}</td>
        <td>
            <a class="btn btn-xs btn-default text-primary mx-1" title="Edit Category" href="{{ route('categories.edit', $category->id) }}">
                <i class="fa fa-lg fa-fw fa-pen"></i>
            </a>
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

    $(".category-update").on('input', function() {
        const value = $(this).val();
        const translationId = $(this).attr('data-id');

        debounce(() => {
            fetch(`/panel/admin/categories/update-category/${translationId}`, {
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

    $(".slug-update").on('input', function() {
        const value = $(this).val();
        const translationId = $(this).attr('data-id');

        debounce(() => {
            fetch(`/panel/admin/categories/update-slug/${translationId}`, {
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

    $(".display-order-update").on('input', function() {
        const value = $(this).val();
        const categoryId = $(this).attr('data-id');

        debounce(() => {
            fetch(`/panel/admin/categories/${categoryId}/update-display-order`, {
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
