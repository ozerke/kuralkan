@extends('adminlte::page')

@section('content_header')
    <h1>
        {{ __('app.hero-slides') }}</h1>
@stop

@php
    $heads = [
        __('app.title'),
        __('app.thumbnail'),
        __('app.display-order'),
        __('app.language'),
        __('app.url'),
        ['label' => __('app.action'), 'no-export' => true, 'width' => 10],
    ];

    $config = [
        'paging' => false,
        'searching' => true,
        'columns' => [null, ['orderable' => false], null, null, null, ['orderable' => false]],
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


            <form method="POST" action="{{ route('hero-slides.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <x-adminlte-input name="title" label="{{ __('app.title') }}" placeholder="..."
                        fgroup-class="col-12 col-md-6" disable-feedback required />
                    <x-adminlte-input-file required name="media" igroup-size="md" fgroup-class="col-12 col-md-6"
                        placeholder="..." label="{{ __('app.upload-media') }}" accept="image/*">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-blue">
                                <i class="fas fa-file"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input-file>
                    <x-adminlte-input name="display_order" label="{{ __('app.display-order') }}" placeholder="1/2/3/4/5..."
                        fgroup-class="col-12 col-md-6" disable-feedback required type="number" />

                    <x-adminlte-select label="{{ __('app.language') }}" name="lang" fgroup-class="col-12 col-md-6"
                        required>
                        <option value="tr">TR</option>
                        <option value="en">EN</option>
                    </x-adminlte-select>
                    <x-adminlte-input name="url" label="{{ __('app.url') }}" placeholder="..." fgroup-class="col-12"
                        disable-feedback />
                </div>

                <button class="btn btn-lg btn-success my-2">
                    {{ __('app.create') }}
                </button>
            </form>


        </div>
    </div>
    <x-adminlte-datatable id="hero-slides-table" :heads="$heads" :config="$config" beautify striped hoverable
        head-theme="dark">
        @foreach ($slides as $slide)
            <tr style="white-space:nowrap;width:100%;">

                <td>{{ $slide->title }}</td>
                <td><img src="{{ $slide->photo_url }}" class="rounded-sm elevation-1" alt="Media item" height="40"
                        width="40"></td>
                <td>{{ $slide->display_order }}</td>
                <td>{{ $slide->getLanguageName() }}</td>
                <td>
                    @if ($slide->url)
                        <a href="{{ $slide->url }}">{{ $slide->url }}</a>
                    @else
                        -
                    @endif
                </td>
                <td>
                    <form method="POST" action="{{ route('hero-slides.destroy', $slide->id) }}">
                        @csrf
                        @method('DELETE')
                        <a class="btn btn-xs btn-default text-primary mx-1" title="Edit section"
                            href="{{ route('hero-slides.edit', $slide->id) }}">
                            <i class="fa fa-lg fa-fw fa-pen"></i>
                        </a>
                        <button type="submit" class="btn btn-xs btn-danger text-white mx-1" title="Delete">
                            <i class="fa fa-lg fa-fw fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>

@stop
