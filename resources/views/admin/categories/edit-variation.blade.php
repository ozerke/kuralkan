@extends('adminlte::page')

@section('content_header')
    <h1>{{ __('app.edit_product_variation') }}</h1>
@stop

@php
    $mediaProps = $variation->getInputMediaProps();

    $uploaderConfig = [
        'allowedFileTypes' => ['image', 'video'],
        'theme' => 'explorer-fa5',
        'showUpload' => false,
        'overwriteInitial' => false,
        'deleteUrl' => '/panel/admin/variations/' . $variation->id . '/delete-media',
        'fileActionSettings' => ['showRotate' => false],
    ];

    $config = array_merge($uploaderConfig, $mediaProps);
@endphp

@section('content')
    @include('layouts.messages')
    <form method="POST" action="{{ route('variations.update', $variation->id) }}" enctype="multipart/form-data">
        @method('patch')
        @csrf
        <div class="row bg-white p-2">
            <div class="col-12 col-md-4">
                <h4 class="font-weight-bold">{{ __('app.product_variation_information') }}</h4>
                <p>{{ __('app.stock_code') }}: <b>{{ $product->stock_code }}</b></p>
                <p>{{ __('app.product-name') }}: <b>{{ $product->currentTranslation->product_name ?? '-' }}</b></p>
                <p>{{ __('app.color') }}: <b>{{ $variation->color->currentTranslation->color_name ?? '-' }}</b></p>
                <p>{{ __('app.color_code') }}: <b>{{ $variation->color->color_code }}</b></p>
            </div>
            <div class="col-12 col-md-8">
                <x-adminlte-input-switch name="display" data-on-text="{{ __('app.yes') }}"
                    data-off-text="{{ __('app.no') }}" label="{{ __('app.display') }}" :checked="$variation->display === 't'" />
                <x-adminlte-input name="price" label="{{ __('app.price') }}" placeholder="..." disable-feedback
                    value="{{ $variation->price }}" required />
                <x-adminlte-input name="display-order" label="{{ __('app.display-order') }}" placeholder="1/2/3/4/5/6..."
                    disable-feedback value="{{ $variation->display_order }}" required />
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
            const variationId = "{{ $variation->id }}";

            fetch(`/panel/admin/variations/${variationId}/reorder-media`, {
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
