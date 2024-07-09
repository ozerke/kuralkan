@extends('adminlte::page')

@section('content_header')
    <h1>{{ __('app.edit-category') }}: <b>{{ $category->tr->category_name }}</b></h1>
@stop

@php
    $editorConfig = [
        'codeviewFilter' => false,
        'codeviewIframeFilter' => true,
        'height' => '100',
        'toolbar' => [['style', ['bold', 'italic', 'underline', 'clear']], ['font', ['strikethrough', 'superscript', 'subscript']], ['fontsize', ['fontsize']], ['color', ['color']], ['para', ['ul', 'ol', 'paragraph']], ['height', ['height']], ['table', ['table']], ['insert', ['link', 'photo']], ['view', ['fullscreen', 'codeview', 'help']]],
    ];
@endphp

@section('content')
    @include('layouts.messages')
    <form method="POST" action="{{ route('categories.update', $category->id) }}">
        @method('patch')
        @csrf
        <div class="row bg-white p-2">
            <div class="col-12 col-md-8">
                <x-adminlte-input name="display-order" label="{{ __('app.display-order') }}" placeholder="1/2/3/4/5/6..."
                    disable-feedback value="{{ $category->display_order }}" required />
            </div>
        </div>

        <div class="row bg-white p-2 mt-2">
            <div class="col-12">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-tr-tab" data-toggle="tab" href="#nav-tr" role="tab"
                            aria-controls="nav-tr" aria-selected="true">{{ config('app.locales')['tr'] }}</a>
                        <a class="nav-item nav-link" id="nav-en-tab" data-toggle="tab" href="#nav-en" role="tab"
                            aria-controls="nav-en" aria-selected="false">{{ config('app.locales')['en'] }}</a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active py-2" id="nav-tr" role="tabpanel" aria-labelledby="nav-tr-tab">
                        <x-adminlte-input name="tr-category-name" label="{{ __('app.category') }}" placeholder="..."
                            disable-feedback value="{{ optional($category->tr)->category_name }}" required />
                        <x-adminlte-input name="tr-slug" label="{{ __('app.slug') }}" placeholder="..." disable-feedback
                            value="{{ optional($category->tr)->slug }}" required />
                        <x-adminlte-input name="tr-seo-title" label="{{ __('app.seo-title') }}" placeholder="..."
                            disable-feedback value="{{ optional($category->tr)->seo_title }}" required />
                        <x-adminlte-textarea name="tr-seo-description" label="{{ __('app.seo-description') }}"
                            placeholder="..." required>{{ optional($category->tr)->seo_description }}</x-adminlte-textarea>
                        <x-adminlte-input name="tr-seo-keywords" label="{{ __('app.seo-keywords') }}" placeholder="..."
                            disable-feedback value="{{ optional($category->tr)->seo_keywords }}" required />
                        <x-adminlte-text-editor required name="tr-description" label="{{ __('app.description') }}"
                            igroup-size="md" placeholder="..."
                            :config="$editorConfig">{{ optional($category->tr)->description }}</x-adminlte-text-editor>
                    </div>
                    <div class="tab-pane fade py-2" id="nav-en" role="tabpanel" aria-labelledby="nav-en-tab">
                        <x-adminlte-input name="en-category-name" label="{{ __('app.category') }}" placeholder="..."
                            disable-feedback value="{{ optional($category->en)->category_name }}" required />
                        <x-adminlte-input name="en-slug" label="{{ __('app.slug') }}" placeholder="..." disable-feedback
                            value="{{ optional($category->en)->slug }}" required />
                        <x-adminlte-input name="en-seo-title" label="{{ __('app.seo-title') }}" placeholder="..."
                            disable-feedback value="{{ optional($category->en)->seo_title }}" required />
                        <x-adminlte-textarea name="en-seo-description" label="{{ __('app.seo-description') }}"
                            placeholder="..." required>{{ optional($category->en)->seo_description }}</x-adminlte-textarea>
                        <x-adminlte-input name="en-seo-keywords" label="{{ __('app.seo-keywords') }}" placeholder="..."
                            disable-feedback value="{{ optional($category->en)->seo_keywords }}" required />
                        <x-adminlte-text-editor required name="en-description" label="{{ __('app.description') }}"
                            igroup-size="md" placeholder="..."
                            :config="$editorConfig">{{ optional($category->en)->description }}</x-adminlte-text-editor>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full d-flex justify-content-end">
            <button class="btn btn-md btn-success my-4">
                {{ __('app.save') }}
            </button>
        </div>
    </form>


@stop
