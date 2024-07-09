@extends('adminlte::page')

@section('content_header')
    <h1>{{ __('app.configuration') }}</h1>
@stop

@php
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
    <form method="POST" action="{{ route('configuration.store') }}">
        @csrf
        <div class="row">
            <x-adminlte-input name="min_payment_percentage" label="{{ __('app.min-partial-payment-percent') }}"
                placeholder="10%" fgroup-class="col-12 col-md-6" disable-feedback required type="number"
                value="{{ $min ?? null }}" />
            <x-adminlte-input name="max_payments_count" label="{{ __('app.max-payments-count') }}" placeholder="30"
                fgroup-class="col-12 col-md-6" disable-feedback required type="number" value="{{ $max ?? null }}" />
        </div>

        <h3>{{ __('app.sales-agreements') }}</h3>

        <div class="row">
            <x-adminlte-input name="sa_application_fee" label="{{ __('app.application-fee') }}, TL" placeholder="50"
                fgroup-class="col-12 col-md-6" disable-feedback required type="number" value="{{ $fee ?? null }}" />
            <x-adminlte-text-editor name="e_sales_agreement_en" label="{{ __('app.e-sales-agreement-en') }}"
                igroup-size="md" placeholder="..." fgroup-class="col-12"
                :config="$editorConfig">{{ $eSalesAgreementEn }}</x-adminlte-text-editor>
            <x-adminlte-text-editor name="e_sales_agreement_tr" label="{{ __('app.e-sales-agreement-tr') }}"
                igroup-size="md" placeholder="..." fgroup-class="col-12"
                :config="$editorConfig">{{ $eSalesAgreementTr }}</x-adminlte-text-editor>
            <x-adminlte-text-editor name="sales_agreement_explanation_en"
                label="{{ __('app.sales-agreement-explanation-en') }}" igroup-size="md" placeholder="..."
                fgroup-class="col-12" :config="$editorConfig">{{ $salesAgreementExplanationEn }}</x-adminlte-text-editor>
            <x-adminlte-text-editor name="sales_agreement_explanation_tr"
                label="{{ __('app.sales-agreement-explanation-tr') }}" igroup-size="md" placeholder="..."
                fgroup-class="col-12" :config="$editorConfig">{{ $salesAgreementExplanationTr }}</x-adminlte-text-editor>
        </div>

        <h3>{{ __('app.home-page') }}</h3>

        <div class="row">
            <x-adminlte-input name="home_title" label="{{ __('app.home-title') }}" placeholder="..."
                fgroup-class="col-12 col-md-6" disable-feedback required value="{!! $homeTitle ?? null !!}" />
            <x-adminlte-input name="home_description" label="{{ __('app.home-description') }}" placeholder="..."
                fgroup-class="col-12 col-md-6" disable-feedback required value="{!! $homeDesc ?? null !!}" />
            <x-adminlte-input name="home_keywords" label="{{ __('app.home-keywords') }}" placeholder="a,b,c,d,e"
                fgroup-class="col-12 col-md-12" disable-feedback required value="{!! $homeKeywords ?? null !!}" />
        </div>

        <button class="btn btn-lg btn-success my-2">
            {{ __('app.save') }}
        </button>
    </form>

    <h3>{{ __('app.system-actions') }}</h3>

    <div class="row pb-5">
        <a href="{{ route('configuration.cache-flush') }}" class="col-2 btn btn-md btn-primary my-2">
            {{ __('app.clear-cache') }}
        </a>
    </div>

@stop
