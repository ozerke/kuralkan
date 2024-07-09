@extends('adminlte::page')

@section('content_header')
    <h1>{{ __('app.edit-menu-section') }}</h1>
@stop

@php
    $config = [
        'placeholder' => __('app.select-multiple'),
    ];
@endphp

@section('content')
    @include('layouts.messages')
    <form method="POST" action="{{ route('menu-sections.update', $section->id) }}">
        @method('patch')
        @csrf
        <div class="row bg-white p-2">

            <x-adminlte-select fgroup-class="col-12 col-md-4" label="{{ __('app.product-brand') }}" name="product_brand">
                <option value="Bajaj" @if ($section->product_brand == 'Bajaj') selected @endif>Bajaj</option>
                <option value="Kanuni" @if ($section->product_brand == 'Kanuni') selected @endif>Kanuni</option>
            </x-adminlte-select>
            <x-adminlte-input name="title_tr" label="{{ __('app.title-tr') }}" placeholder="..."
                fgroup-class="col-12 col-md-4" disable-feedback required value="{{ $section->title_tr }}" />
            <x-adminlte-input name="title_en" label="{{ __('app.title-en') }}" placeholder="..."
                fgroup-class="col-12 col-md-4" disable-feedback required value="{{ $section->title_en }}" />

            <x-adminlte-input name="display_order" label="{{ __('app.display-order') }}" placeholder="1/2/3/4/5..."
                fgroup-class="col-12 col-md-6" disable-feedback required type="number"
                value="{{ $section->display_order }}" />

            <x-adminlte-select fgroup-class="col-12 col-md-6" label="{{ __('app.category') }}" name="category_id">
                <option value="">-</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @if ($section->category_id && $section->category_id == $category->id) selected @endif>
                        {{ $category->currentTranslation->category_name }}</option>
                @endforeach
            </x-adminlte-select>


            <x-adminlte-select2 id="menu_items" name="menu_items[]" label="{{ __('app.menu-items') }}" igroup-size="md"
                :config="$config" multiple fgroup-class="col-12">
                <x-slot name="prependSlot">
                    <div class="input-group-text bg-primary">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                </x-slot>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @if ($section->product_ids->contains($product->id)) selected @endif>
                        {{ $product->currentTranslation->product_name }}</option>
                @endforeach
            </x-adminlte-select2>

            <div class="col-12 row w-full">
                <h4 class="col-12">{{ __('app.item-order') }}</h3>
                    <ul class="list-group" id="sortable">
                        @foreach ($section->menuSectionItems()->orderBy('display_order')->get() as $item)
                            <li class="list-group-item" style="cursor: pointer;">
                                {{ $item->product->currentTranslation->product_name }} ({{ $item->product->id }})
                                <input hidden name="item_order[]" value="{{ $item->product->id }}" />
                            </li>
                        @endforeach
                    </ul>
            </div>
        </div>

        <div class="w-full d-flex justify-content-end">
            <button class="btn btn-md btn-success my-4">
                {{ __('app.save') }}
            </button>
        </div>
    </form>


@stop

@section('js')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>
        $(function() {
            $("#sortable").sortable();
        });
    </script>
@endsection
