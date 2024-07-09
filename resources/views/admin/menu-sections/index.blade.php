@extends('adminlte::page')

@section('content_header')
    <h1>{{ __('app.menu-sections') }}</h1>
@stop

@php
    $heads = [
        __('app.product-brand'),
        __('app.title-tr'),
        __('app.title-en'),
        __('app.display-order'),
        ['label' => __('app.action'), 'no-export' => true, 'width' => 10],
    ];

    $config = [
        'paging' => false,
        'searching' => true,
        'columns' => [null, null, null, null, ['orderable' => false]],
    ];
@endphp

@section('content')
    @include('layouts.messages')
    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#createFormCollapse" aria-expanded="false"
        aria-controls="createFormCollapse">
        {{ __('app.create') }}
    </button>
    <div class="collapse mt-2" id="createFormCollapse">
        <div class="card card-body">
            <form method="POST" action="{{ route('menu-sections.store') }}">
                @csrf
                <div class="row">
                    <x-adminlte-select label="{{ __('app.product-brand') }}" name="product_brand"
                        fgroup-class="col-12 col-md-4" required>
                        <option value="Bajaj">Bajaj</option>
                        <option value="Kanuni">Kanuni</option>
                    </x-adminlte-select>
                    <x-adminlte-input name="title_tr" label="{{ __('app.title-tr') }}" placeholder="..."
                        fgroup-class="col-12 col-md-4" disable-feedback required />
                    <x-adminlte-input name="title_en" label="{{ __('app.title-en') }}" placeholder="..."
                        fgroup-class="col-12 col-md-4" disable-feedback required />
                    <x-adminlte-select fgroup-class="col-12 col-md-6" label="{{ __('app.category') }}" name="category_id">
                        <option value="">-</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->currentTranslation->category_name }}</option>
                        @endforeach
                    </x-adminlte-select>
                    <x-adminlte-input name="display_order" label="{{ __('app.display-order') }}" placeholder="1/2/3/4/5..."
                        fgroup-class="col-12 col-md-6" disable-feedback required type="number" />
                </div>

                <button class="btn btn-lg btn-success my-2">
                    {{ __('app.create') }}
                </button>
            </form>


        </div>
    </div>
    <x-adminlte-datatable id="sections-table" :heads="$heads" :config="$config" beautify striped hoverable
        head-theme="dark">
        @foreach ($sections as $section)
            <tr style="white-space:nowrap;width:100%;">

                <td class="text-center">{{ $section->product_brand }}</td>
                <td class="text-center">{{ $section->title_tr }}</td>
                <td class="text-center">{{ $section->title_en }}</td>
                <td class="text-center">{{ $section->display_order }}</td>
                <td class="d-flex">
                    <a class="btn btn-xs btn-default text-primary mx-1" title="Edit section"
                        href="{{ route('menu-sections.edit', $section->id) }}">
                        <i class="fa fa-lg fa-fw fa-pen"></i>
                    </a>
                    <form method="POST" action="{{ route('menu-sections.destroy', $section->id) }}">
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
