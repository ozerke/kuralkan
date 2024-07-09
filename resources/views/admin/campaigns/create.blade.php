@extends('adminlte::page')

@section('content_header')
    <h1>{{ __('app.create-campaign') }}</h1>
@stop

@section('content')
    @include('layouts.messages')
    <form method="POST" action="{{ route('campaigns.store') }}">
        @method('POST')
        @csrf
        <div class="row bg-white p-2">

            <x-adminlte-select fgroup-class="col-12 col-md-6" label="{{ __('app.product') }}" name="product">
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->currentTranslation->product_name }}
                        ({{ $product->stock_code }})
                    </option>
                @endforeach
            </x-adminlte-select>

            <x-adminlte-input name="bt_payment_exp_code" label="{{ __('app.bt-payment-exp-code') }}"
                placeholder="PN6571097644986" fgroup-class="col-12 col-md-6" disable-feedback required />

            <x-adminlte-input name="down_payment" label="{{ __('app.down-payment') }}, %" placeholder="25"
                fgroup-class="col-12 col-md-4" disable-feedback required type="number" step="1" />

            <x-adminlte-input name="installments" label="{{ __('app.installments') }}" placeholder="6"
                fgroup-class="col-12 col-md-4" disable-feedback required type="number" step="1" />

            <x-adminlte-input name="rate" label="{{ __('app.rate') }}" placeholder="0" fgroup-class="col-12 col-md-4"
                disable-feedback required type="number" step="0.01" />
        </div>

        <div class="w-full d-flex justify-content-end">
            <button class="btn btn-md btn-success my-4">
                {{ __('app.create') }}
            </button>
        </div>
    </form>


@stop
