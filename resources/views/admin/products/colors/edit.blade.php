@extends('adminlte::page')

@section('content_header')
<h1>{{__('app.edit_color')}}</h1>
@stop

@php
$config = [
"placeholder" => __('app.select-multiple'),
];

@endphp

@section('content')
@include('layouts.messages')
<form method="POST" action="{{ route('colors.update', $color->id) }}" enctype="multipart/form-data">
    @method("patch")
    @csrf
    <div class="row bg-white p-2">
        <div class="col-12 col-md-4">
            <h4 class="font-weight-bold">{{__('app.color-information')}}</h4>
            <p>{{__('app.color_code')}}: <b>{{$color->color_code}}</b></p>
            <p>{{__('app.erp_color_name')}}: <b>{{$color->erp_color_name}}</b></p>
            <div class="flex flex-row justify-between">
                <span>{{__('app.color_image')}}:</span>
                @if($color->color_image_url) <img src="{{$color->color_image_url}}" class="rounded-full elevation-1" alt="Color image item" height="40" width="40"> @else <span>-</span> @endif
            </div>
            @if($color->color_image_url)
            <button class="btn btn-md btn-danger my-4" id="delete-image" type="button">
                {{__('app.delete-image')}}
            </button>
            @endif
        </div>
        <div class="col-12 col-md-8">
            <x-adminlte-input name="color_name_tr" label="{{__('app.color_name_tr')}}" disable-feedback value="{{optional($color->getTranslationByKey('tr'))->color_name ?? '-'}}" required readonly />
            <x-adminlte-input name="color_name_en" label="{{__('app.color_name_en')}}" disable-feedback value="{{optional($color->getTranslationByKey('en'))->color_name ?? '-'}}" required />
            <x-adminlte-input-file name="color_image" igroup-size="md" placeholder="..." label="{{__('app.color_image')}}" accept="image/*">
                <x-slot name="prependSlot">
                    <div class="input-group-text bg-blue">
                        <i class="fas fa-file"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-file>
        </div>
        <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-md btn-success my-4">
                {{__('app.save')}}
            </button>
        </div>
    </div>
</form>

<form id="delete-image-form" method="POST" action="{{route('colors.deleteImage', $color->id) }}">
    @csrf
    @method('DELETE')
</form>

@stop

@section('js')
<script>
    $("#delete-image").on('click', function() {
        $("#delete-image-form").submit();
    });

</script>
@endsection
