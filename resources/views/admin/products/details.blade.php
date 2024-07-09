@extends('adminlte::page')

@section('content_header')
    <h1><a href="{{ route('products.index') }}">{{ __('app.products_list') }}</a> &gt; {{ __('app.edit_product') }} &gt;
        <b>{{ $product->currentTranslation->product_name }}</b>
    </h1>
@stop

@php
    $config = [
        'placeholder' => __('app.select-multiple'),
    ];

    $editorConfig = [
        'codeviewFilter' => false,
        'codeviewIframeFilter' => true,
        'height' => '100',
        'toolbar' => [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'photo']],
            ['view', ['fullscreen', 'codeview', 'help']],
        ],
    ];
@endphp

@section('content')
    @include('layouts.messages')
    <form method="POST" action="{{ route('products.update', $product->id) }}">
        @method('patch')
        @csrf
        <div class="row bg-white p-2">
            <div class="col-12 col-md-4">
                <h4 class="font-weight-bold">{{ __('app.product-information') }}</h4>
                <p>{{ __('app.stock_code') }}: <b>{{ $product->stock_code }}</b></p>
                <p>{{ __('app.gtip_code') }}: <b>{{ $product->gtip_code ?? '-' }}</b></p>
                <p>{{ __('app.currency') }}: <b>{{ optional($product->currency)->currency_code ?? '-' }}</b></p>
                <p>{{ __('app.country') }}: <b>{{ $product->country->currentTranslation->country_name }}</b></p>
                <p>{{ __('app.created-at') }}: <b>{{ $product->created_at }}</b></p>
                <p>{{ __('app.updated-at') }}: <b>{{ $product->updated_at }}</b></p>
            </div>
            <div class="col-12 col-md-8">
                <x-adminlte-input-switch name="new_product" data-on-text="{{ __('app.yes') }}"
                    data-off-text="{{ __('app.no') }}" label="{{ __('app.new_product') }}" :checked="$product->new_product === 'Y'" />
                <x-adminlte-input-switch name="display" data-on-text="{{ __('app.yes') }}"
                    data-off-text="{{ __('app.no') }}" label="{{ __('app.display') }}" :checked="$product->display === 't'" />

                <x-adminlte-select label="{{ __('app.breadcrumb-category') }}" name="breadcrumb-category">
                    <option value="">-</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @if ($product->bread_crumb_category_id && $product->bread_crumb_category_id == $category->id) selected @endif>
                            {{ $category->currentTranslation->category_name }}</option>
                    @endforeach
                </x-adminlte-select>

                <x-adminlte-select2 id="categories" name="categories[]" label="{{ __('app.categories') }}" igroup-size="md"
                    :config="$config" multiple>
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-primary">
                            <i class="fas fa-tag"></i>
                        </div>
                    </x-slot>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @if ($product->category_ids->contains($category->id)) selected @endif>
                            {{ $category->currentTranslation->category_name }}</option>
                    @endforeach
                </x-adminlte-select2>

                <x-adminlte-input name="display-order" label="{{ __('app.display-order') }}" placeholder="1/2/3/4/5/6..."
                    disable-feedback value="{{ $product->display_order }}" required />

                <x-adminlte-input-switch name="seo-no-index" data-on-text="{{ __('app.yes') }}"
                    data-off-text="{{ __('app.no') }}" checked label="{{ __('app.seo-no-index') }}" :checked="$product->seo_no_index === 'noindex'" />
                <x-adminlte-input-switch name="seo-no-follow" data-on-text="{{ __('app.yes') }}"
                    data-off-text="{{ __('app.no') }}" checked label="{{ __('app.seo-no-follow') }}" :checked="$product->seo_no_follow === 'nofollow'" />


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
                        <x-adminlte-input name="tr-slug" label="{{ __('app.slug') }}" placeholder="..." disable-feedback
                            value="{{ optional($product->getTranslation('tr'))->slug }}" required />
                        <x-adminlte-input name="tr-product-name" label="{{ __('app.product-name') }}" placeholder="..."
                            disable-feedback value="{{ optional($product->getTranslation('tr'))->product_name }}"
                            required />
                        <x-adminlte-textarea name="tr-product-short-description"
                            label="{{ __('app.product-short-description') }}"
                            placeholder="...">{{ optional($product->getTranslation('tr'))->short_description }}</x-adminlte-textarea>
                        <x-adminlte-text-editor required name="tr-product-description"
                            label="{{ __('app.product-description') }}" igroup-size="md" placeholder="..."
                            :config="$editorConfig">{{ optional($product->getTranslation('tr'))->description }}</x-adminlte-text-editor>
                        <x-adminlte-text-editor required name="tr-product-delivery-info"
                            label="{{ __('app.product-delivery-info') }}" igroup-size="md" placeholder="..."
                            :config="$editorConfig">{{ optional($product->getTranslation('tr'))->delivery_info }}</x-adminlte-text-editor>
                        <x-adminlte-text-editor required name="tr-product-faq" label="{{ __('app.product-faq') }}"
                            igroup-size="md" placeholder="..."
                            :config="$editorConfig">{{ optional($product->getTranslation('tr'))->faq }}</x-adminlte-text-editor>
                        <x-adminlte-text-editor name="tr-product-documents" label="{{ __('app.documents') }}"
                            igroup-size="md" placeholder="..."
                            :config="$editorConfig">{{ optional($product->getTranslation('tr'))->documents }}</x-adminlte-text-editor>
                        <x-adminlte-input name="tr-seo-title" label="{{ __('app.seo-title') }}" placeholder="..."
                            disable-feedback value="{{ optional($product->getTranslation('tr'))->seo_title }}" required />
                        <x-adminlte-textarea name="tr-seo-description" label="{{ __('app.seo-description') }}"
                            placeholder="..."
                            required>{{ optional($product->getTranslation('tr'))->seo_desc }}</x-adminlte-textarea>
                        <x-adminlte-input name="tr-seo-keywords" label="{{ __('app.seo-keywords') }}" placeholder="..."
                            disable-feedback value="{{ optional($product->getTranslation('tr'))->seo_keywords }}"
                            required />
                    </div>
                    <div class="tab-pane fade py-2" id="nav-en" role="tabpanel" aria-labelledby="nav-en-tab">
                        <x-adminlte-input name="en-slug" label="{{ __('app.slug') }}" placeholder="..." disable-feedback
                            value="{{ optional($product->getTranslation('en'))->slug }}" required />
                        <x-adminlte-input name="en-product-name" label="{{ __('app.product-name') }}" placeholder="..."
                            disable-feedback value="{{ optional($product->getTranslation('en'))->product_name }}"
                            required />
                        <x-adminlte-textarea name="en-product-short-description"
                            label="{{ __('app.product-short-description') }}"
                            placeholder="...">{{ optional($product->getTranslation('en'))->short_description }}</x-adminlte-textarea>
                        <x-adminlte-text-editor required name="en-product-description"
                            label="{{ __('app.product-description') }}" igroup-size="md" placeholder="..."
                            :config="$editorConfig">{{ optional($product->getTranslation('en'))->description }}</x-adminlte-text-editor>
                        <x-adminlte-text-editor required name="en-product-delivery-info"
                            label="{{ __('app.product-delivery-info') }}" igroup-size="md" placeholder="..."
                            :config="$editorConfig">{{ optional($product->getTranslation('en'))->delivery_info }}</x-adminlte-text-editor>
                        <x-adminlte-text-editor required name="en-product-faq" label="{{ __('app.product-faq') }}"
                            igroup-size="md" placeholder="..."
                            :config="$editorConfig">{{ optional($product->getTranslation('en'))->faq }}</x-adminlte-text-editor>
                        <x-adminlte-text-editor name="en-product-documents" label="{{ __('app.documents') }}"
                            igroup-size="md" placeholder="..."
                            :config="$editorConfig">{{ optional($product->getTranslation('en'))->documents }}</x-adminlte-text-editor>
                        <x-adminlte-input name="en-seo-title" label="{{ __('app.seo-title') }}" placeholder="..."
                            disable-feedback value="{{ optional($product->getTranslation('en'))->seo_title }}" required />
                        <x-adminlte-textarea name="en-seo-description" label="{{ __('app.seo-description') }}"
                            placeholder="..."
                            required>{{ optional($product->getTranslation('en'))->seo_desc }}</x-adminlte-textarea>
                        <x-adminlte-input name="en-seo-keywords" label="{{ __('app.seo-keywords') }}" placeholder="..."
                            disable-feedback value="{{ optional($product->getTranslation('en'))->seo_keywords }}"
                            required />
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
