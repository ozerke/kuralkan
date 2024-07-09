@extends('adminlte::page')

@section('content_header')
    <h1>
        {{ __('app.user-information') }}</h1>
@stop

@section('content')
    @include('layouts.messages')
    <div class="row bg-white p-2">
        <div class="col-12 col-md-6 p-2">
            <p>{{ __('app.user-active') }}: <b>
                    @if ($user->user_active === 'Y')
                        <span class="badge badge-success text-md">{{ __('app.yes') }}</span>
                    @else
                        <span class="badge badge-danger text-md">{{ __('app.no') }}</span>
                    @endif
                </b>
            </p>
            <p>{{ __('app.name-surname') }}: <b>{{ $user->full_name ?? '-' }}</b></p>
            <p>{{ __('app.erp-id') }}: <b>{{ $user->erp_user_id }}</b></p>
            <p>{{ __('app.erp-username') }}: <b>{{ $user->erp_user_name ?? '-' }}</b></p>
            <p>{{ __('app.user-no') }}: <b>{{ $user->user_no }}</b></p>
            <p>{{ __('app.user-type') }}: <b>{{ $user->getUserType() }}</b></p>
            <p>{{ __('app.email') }}: <b>{{ $user->email ?? '-' }}</b></p>
            @if ($user->isShopOrService())
                <p>{{ __('app.erp-email') }}: <b>{{ $user->erp_email ?? '-' }}</b></p>
            @endif
            <p class="mb-0">{{ __('app.created-at') }}: <b>{{ $user->created_at }}</b></p>
        </div>
        <div class="col-12 col-md-6 p-2">
            <p>{{ __('app.company') }}: <b>
                    @if ($user->company === 'Y')
                        <span class="badge badge-success text-md">{{ __('app.yes') }}</span>
                    @else
                        <span class="badge badge-danger text-md">{{ __('app.no') }}</span>
                    @endif
                </b></p>
            @if ($user->company === 'Y')
                <p>{{ __('app.company_name') }}: <b>{{ $user->company_name }}</b></p>
            @endif
            <p>{{ __('app.phone') }}: <b>{{ $user->phone }}</b></p>
            <p>{{ __('app.address') }}: <b>{{ $user->address ?? '-' }}</b></p>
            <p>{{ __('app.district') }}: <b>{{ $user->district->currentTranslation->district_name ?? '-' }}</b></p>
            <p>{{ __('app.postal-code') }}: <b>{{ $user->postal_code ?? '-' }}</b></p>
            <p>{{ __('app.city') }}: <b>{{ $user->district->city->currentTranslation->city_name ?? '-' }}</b></p>
            <p>{{ __('app.national-id') }}: <b>{{ $user->national_id ?? '-' }}</b></p>
            <p>{{ __('app.tax-id') }}: <b>{{ $user->tax_id ?? '-' }}</b></p>
            @if ($user->company === 'Y')
                <p>{{ __('app.tax-office') }}: <b>{{ $user->tax_office }}</b></p>
            @endif
            <p class="mb-0">{{ __('app.registered-by') }}:
                <b>{{ optional($user->registeredBy)->getErpName() ?? '-' }}</b></p>
        </div>
    </div>

@stop
