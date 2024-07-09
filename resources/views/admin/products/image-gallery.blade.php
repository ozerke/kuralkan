@extends('adminlte::page')

@section('content_header')
    <h1>{{ __('app.image-gallery') }}</h1>
@stop

@php
    $mediaProps = $product->getInputMediaProps();

    $uploaderConfig = [
        'allowedFileTypes' => ['image', 'video'],
        'theme' => 'explorer-fa5',
        'showUpload' => false,
        'overwriteInitial' => false,
        'deleteUrl' => '/panel/admin/products/' . $product->id . '/delete-media',
        'fileActionSettings' => ['showRotate' => false],
    ];

    $config = array_merge($uploaderConfig, $mediaProps);
@endphp

@section('content')
    @include('layouts.messages')
    <form method="POST" action="{{ route('products.update-media', $product->id) }}" enctype="multipart/form-data">
        @method('patch')
        @csrf
        <div class="row bg-white p-2">
            <div class="col-12 col-md-4">
                <h4 class="font-weight-bold">{{ __('app.product-information') }}</h4>
                <p>{{ __('app.stock_code') }}: <b>{{ $product->stock_code }}</b></p>
                <p>{{ __('app.gtip_code') }}: <b>{{ $product->gtip_code ?? '-' }}</b></p>
                <p>{{ __('app.currency') }}: <b>{{ optional($product->currency)->currency_code ?? '-' }}</b></p>
                <p>{{ __('app.country') }}: <b>{{ $product->country->currentTranslation->country_name }}</b></p>
            </div>
            <div class="col-12 col-md-8">
                <x-adminlte-input-file-krajee label="{{ __('app.upload-media') }}" id="media-input" name="media[]"
                    igroup-size="md" data-msg-placeholder="..." data-show-cancel="false" data-show-close="false" multiple
                    :config="$config" />
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
    <script>
        $('#media-input').on('filesorted', function(event, params) {
            const productId = "{{ $product->id }}";

            fetch(`/panel/admin/products/${productId}/reorder-media`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    stack: params.stack
                })

            })
        });

        $('#media-input').on('filedeleted', function(event, key, jqXHR, data) {
            window.location.reload();
        });
    </script>
@endsection
