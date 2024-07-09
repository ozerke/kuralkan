@extends('adminlte::page')

@section('content_header')
    <h1>{{ __('app.hero-slides') }}</h1>
@stop

@section('content')
    @include('layouts.messages')
    <form method="POST" action="{{ route('hero-slides.update', $slide->id) }}">
        @method('patch')
        @csrf
        <div class="row">
            <x-adminlte-input name="title" label="{{ __('app.title') }}" placeholder="..." fgroup-class="col-12 col-md-6"
                disable-feedback required value="{{ $slide->title }}" />
            <x-adminlte-input name="display_order" label="{{ __('app.display-order') }}" placeholder="1/2/3/4/5..."
                fgroup-class="col-12 col-md-6" disable-feedback required type="number"
                value="{{ $slide->display_order }}" />
            <x-adminlte-select label="{{ __('app.language') }}" name="lang" fgroup-class="col-12 col-md-6" required>
                <option value="tr" @if ($slide->lang_id == 1) selected @endif>TR</option>
                <option value="en" @if ($slide->lang_id == 2) selected @endif>EN</option>
            </x-adminlte-select>
            <x-adminlte-input name="url" label="{{ __('app.url') }}" placeholder="..." fgroup-class="col-12 col-md-6"
                disable-feedback value="{{ $slide->url }}" />
        </div>

        <div class="w-full d-flex justify-content-end">
            <button class="btn btn-md btn-success my-4">
                {{ __('app.save') }}
            </button>
        </div>
    </form>


@stop
