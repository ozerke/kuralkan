@php
    $confirmMessage = [
        'text' => __('web.confirm-text'),
        'fullname' => __('web.fullname'),
        'birthdate' => __('web.date_of_birth'),
        'nationalId' => __('web.national_id'),
    ];
@endphp

<x-app-layout>
    @section('title')
        {{ __('web.invoice-information') }}
    @endsection
    <div class="px-[40px] lg:px-[120px] bg-[#F2F2F2] pt-[150px] lg:pt-[100px]">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 py-[40px]">
            <div class="col-span-1 lg:col-span-8 flex flex-col gap-10">
                <form id="order-form" class="flex flex-col gap-10" method="POST"
                    action="{{ route('submit-order-information') }}">
                    @method('post')
                    @csrf
                    <div class="bg-white px-10 py-5 flex flex-col rounded-md shadow-sm">
                        <h1 class="uppercase font-bold text-xl text-center lg:text-left">{{ __('web.delivery-point') }}
                        </h1>

                        <label for="delivery-city" class="block my-2 text-sm">{{ __('web.delivery-city') }}</label>
                        <select id="delivery-city"
                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option selected>{{ __('web.select-a-city') }}</option>
                            @foreach ($deliveryCities as $city)
                                <option value="{{ $city->id }}">{{ $city->currentTranslation->city_name }}</option>
                            @endforeach
                        </select>

                        <label for="delivery-district"
                            class="block my-2 text-sm">{{ __('web.delivery-district') }}</label>
                        <select id="delivery-district"
                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option selected></option>
                        </select>

                        <label for="delivery-point"
                            class="block my-2 text-sm">{{ __('web.delivery-service-point') }}</label>
                        <select name="delivery_point" id="delivery-point"
                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option selected></option>
                        </select>
                    </div>
                    <div class="bg-white px-10 py-5 flex flex-col rounded-md shadow-sm">
                        <h1 class="uppercase font-bold text-xl text-center lg:text-left mb-5">
                            {{ __('web.invoice-information') }}</h1>

                        <div class="pb-3">
                            <x-bladewind.alert type="info" shade="dark" show_icon="false" show_close_icon="false"
                                class="font-bold">
                                {{ __('web.name-surname-message') }}
                            </x-bladewind.alert>
                        </div>

                        <div class="pb-3 hidden" id="alert-box">
                            <x-bladewind.alert container_id="alert-message" type="error" shade="dark"
                                show_icon="false" show_close_icon="false" class="font-bold">
                            </x-bladewind.alert>
                        </div>

                        <div class="flex flex-col lg:flex-row justify-between gap-5 mb-5">
                            <div class="w-full lg:w-1/2">
                                <x-input-label for="email" :value="__('web.email')" />
                                <x-text-input id="email" class="block mt-1 w-full disabled:bg-gray-200"
                                    type="email" name="email" :value="$information['email']" required :disabled="!auth()->user()->isShopOrService()" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div class="w-full lg:w-1/2">
                                <x-input-label for="phone" :value="__('web.phone')" />
                                <x-text-input id="phone" class="block mt-1 w-full disabled:bg-gray-200"
                                    type="text" name="phone" :value="$information['phone']" required :disabled="!auth()->user()->isShopOrService()" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                <input hidden name="full_phone" id="full_phone" />
                            </div>
                        </div>

                        <div id="phone-verify-btn" class="w-full items-center justify-end mt-2 mb-4 hidden">
                            <button type="button"
                                class="font-bold rounded-md h-[50px] text-[#0E60AE] text-[15px] w-full inline-block align-top p-0 tracking-[0] uppercase bg-transparent border-[1px] border-[solid] border-[#0E60AE] hover:text-white hover:bg-[#0e60ae] transition-colors">{{ __('web.verify-phone') }}</button>
                        </div>

                        <div class="w-full mt-2 py-4 hidden" id="pin-container">
                            <x-bladewind.code name="pin_code" inputClass="bg-gray-100 border-2 border-gray-200"
                                on_verify="verifyCode" errorMessage="{{ __('web.verification-code-invalid') }}" />
                        </div>

                        <div class="flex flex-col lg:flex-row justify-between gap-5 mb-5">
                            <div class="w-full lg:w-1/2">
                                <x-input-label for="name" :value="__('web.name')" />
                                <x-text-input id="name" class="block mt-1 w-full disabled:bg-gray-200"
                                    type="text" name="name" :value="$information['name']" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div class="w-full lg:w-1/2">
                                <x-input-label for="surname" :value="__('web.surname')" />
                                <x-text-input id="surname" class="block mt-1 w-full disabled:bg-gray-200"
                                    type="text" name="surname" :value="$information['surname']" required />
                                <x-input-error :messages="$errors->get('surname')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex flex-row gap-10 mb-5">
                            <div class="flex items-center">
                                <input id="individual-radio" type="radio" @checked($information['company'] == 'N' || !$information['company']) value="N"
                                    name="company"
                                    class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
                                <label for="individual-radio"
                                    class="ms-2 text-sm text-gray-900">{{ __('web.individual') }}</label>
                            </div>
                            <div class="flex items-center">
                                <input id="company-radio" type="radio" @checked($information['company'] == 'Y') value="Y"
                                    name="company"
                                    class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
                                <label for="company-radio"
                                    class="ms-2 text-sm text-gray-900">{{ __('web.company') }}</label>
                            </div>
                        </div>

                        <div class="pb-3 hidden" id="national-alert-box">
                            <x-bladewind.alert container_id="national-alert-message" type="error" shade="dark"
                                show_icon="false" show_close_icon="false" class="font-bold">
                            </x-bladewind.alert>
                        </div>

                        <div class="flex flex-row justify-between gap-5 mb-5 individual-field">
                            <div class="w-full flex flex-row  justify-between flex-wrap">
                                <div class="flex flex-col w-[30%] lg:w-[23%]">
                                    <x-input-label for="birth_day" :value="__('web.birth_day')" />
                                    <x-text-input id="birth_day" class="block mt-1" type="text" name="birth_day"
                                        :value="$information['birth_day']" required maxlength="2" />
                                    <x-input-error :messages="$errors->get('birth_day')" class="mt-2" />
                                </div>
                                <div class="flex flex-col w-[30%] lg:w-[23%]">
                                    <x-input-label for="birth_month" :value="__('web.birth_month')" />
                                    <x-text-input id="birth_month" class="block mt-1 " type="text"
                                        name="birth_month" :value="$information['birth_month']" required maxlength="2" />
                                    <x-input-error :messages="$errors->get('birth_month')" class="mt-2" />
                                </div>
                                <div class="flex flex-col w-[30%] lg:w-[23%]">
                                    <x-input-label for="birth_year" :value="__('web.birth_year')" />
                                    <x-text-input id="birth_year" class="block mt-1 " type="text"
                                        name="birth_year" :value="$information['birth_year']" required maxlength="4" />
                                    <x-input-error :messages="$errors->get('birth_year')" class="mt-2" />
                                </div>
                                <div class="flex flex-col w-[100%] lg:w-[23%] mt-4 lg:mt-0">
                                    <x-input-label for="national_id" :value="__('web.national_id')" />
                                    <x-text-input id="national_id" class="block mt-1" type="text"
                                        name="national_id" :value="$information['national_id']" required />
                                    <x-input-error :messages="$errors->get('national_id')" class="mt-2" />
                                    <input hidden id="id-state" value="" />
                                </div>
                            </div>

                        </div>

                        <div class="flex-col lg:flex-row justify-between gap-5 mb-5 company-field hidden">
                            <div class="w-full">
                                <div class="pb-3">
                                    <x-bladewind.alert type="info" shade="dark" show_icon="false"
                                        show_close_icon="false" class="font-bold">
                                        {{ __('web.company-name-message') }}
                                    </x-bladewind.alert>
                                </div>
                                <x-input-label for="company_name" :value="__('web.company_name')" />
                                <x-text-input id="company_name" class="block mt-1 w-full" type="text"
                                    name="company_name" :value="$information['company_name']" required />
                                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex flex-col lg:flex-row justify-between gap-5 mb-5">
                            <div class="w-full">
                                <x-input-label for="address" :value="__('web.address')" />
                                <x-text-input id="address" class="block mt-1 w-full" type="text" name="address"
                                    :value="$information['address']" required />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-5">
                            <label for="country" class="block mb-2 text-sm">{{ __('web.country') }}</label>
                            <select id="country" name="country"
                                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="">{{ __('web.select-a-country') }}</option>
                                @foreach ($countries as $country)
                                    <option @if ($information['country'] && $country->id == $information['country']->id) selected @endif
                                        value="{{ $country->id }}">{{ $country->currentTranslation->country_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-5">
                            <label for="city" class="block mb-2 text-sm">{{ __('web.city') }}</label>
                            <select id="city" name="city"
                                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @foreach ($cities as $city)
                                    <option @if ($city->id == $information['city']->id) selected @endif
                                        value="{{ $city->id }}">{{ $city->currentTranslation->city_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-5">
                            <label for="district" class="block mb-2 text-sm">{{ __('web.district') }}</label>
                            <select id="district" name="district"
                                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @foreach ($districts as $district)
                                    <option @if ($district->id == $information['district']->id) selected @endif
                                        value="{{ $district->id }}">
                                        {{ $district->currentTranslation->district_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col lg:flex-row justify-between gap-5 mb-5">
                            <div class="w-full">
                                <x-input-label for="postal_code" :value="__('web.postal_code')" />
                                <x-text-input id="postal_code" class="block mt-1 w-full" type="text"
                                    name="postal_code" :value="$information['postal_code']" required />
                                <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex-col lg:flex-row justify-between gap-5 mb-10 company-field hidden">
                            <div class="w-full">
                                <x-input-label for="tax_office" :value="__('web.tax_office')" />
                                <x-text-input id="tax_office" class="block mt-1 w-full" type="text"
                                    name="tax_office" :value="$information['tax_office']" required />
                                <x-input-error :messages="$errors->get('tax_office')" class="mt-2" />
                            </div>
                            <div class="w-full">
                                <x-input-label for="tax_id" :value="__('web.tax_id')" />
                                <x-text-input id="tax_id" class="block mt-1 w-full" type="text" name="tax_id"
                                    :value="$information['tax_id']" required />
                                <x-input-error :messages="$errors->get('tax_id')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                    <input hidden name="payment-method" id="payment-method" />
                </form>
                <div class="flex flex-col lg:flex-row justify-between gap-5 mb-5">
                    <button data-method="bank" disabled
                        class="rounded-md disabled:bg-gray-500 disabled:border-gray-500 disabled:cursor-not-allowed h-[120px] hover:text-white text-[12px] w-full inline-block align-top p-0 tracking-[0] uppercase hover:bg-black border-[1px] border-[solid] border-[#0E60AE] text-white bg-[#0e60ae] transition-colors payment-btn">
                        <p class="text-lg font-bold">{{ __('web.pay-with-card-bank') }}</p>
                        <p class="px-4">{{ __('web.card-bank-text') }}</p>
                    </button>

                    <button data-method="sales-agreement" disabled
                        class="rounded-md disabled:bg-gray-500 disabled:border-gray-500 disabled:cursor-not-allowed h-[120px] hover:text-white text-[12px] w-full align-top p-0 tracking-[0] uppercase hover:bg-black border-[1px] border-[solid] border-[#0E60AE] text-white bg-[#0e60ae] transition-colors individual-button hidden payment-btn">
                        <p class="text-lg font-bold">{{ __('web.sales-agreement') }}</p>
                        <p class="px-4">{{ __('web.sales-agreement-text') }}</p>
                    </button>
                </div>
            </div>
            <div class="col-span-1 lg:col-span-4 flex flex-col gap-6">
                <h3 class="font-bold text-xl">{{ __('web.order-summary') }}</h3>
                <div class="flex flex-col bg-white p-4 rounded-md shadow-sm">
                    <div class="flex flex-col lg:flex-row gap-4 py-5">
                        @if ($item->firstMedia)
                            <div class="flex justify-center">
                                <img class="max-w-full max-h-[100px] object-contain"
                                    src="{{ $item->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}">
                            </div>
                        @endif
                        <div class="flex flex-col justify-center items-center lg:items-start">
                            <p class="font-bold">{{ $item->product->currentTranslation->product_name }}</p>
                            <div class="flex justify-center items-center gap-2">
                                @if ($item->color->color_image_url)
                                    <img src="{{ $item->color->color_image_url }}"
                                        class="inline-block rounded-full elevation-1 h-[20px] w-[20px]"
                                        alt="Color image item" height="40" width="40">
                                @endif <b>{{ $item->color->currentTranslation->color_name }}</b>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold">{{ __('web.stock-code') }}</p>
                        <p class="font-bold text-[#0E60AE]">{{ $item->product->stock_code }}</p>
                    </div>
                    @if (isset($consignedProduct))
                        <div class="flex justify-between">
                            <p class="font-bold">{{ __('web.chasis-no') }}</p>
                            <p class="font-bold text-[#0E60AE]">{{ $consignedProduct->chasis_no }}</p>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <p class="font-bold">{{ __('web.estimated-delivery-date') }}</p>
                        <p class="font-bold text-[#0E60AE]">
                            {{ $item->getEstimatedDeliveryDate(isset($consignedProduct)) }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold">{{ __('web.basket-total') }}</p>
                        <p class="font-bold text-[#0E60AE]">₺{{ number_format($item->vat_price, 2, ',', '.') }}</p>
                    </div>
                </div>

                <button data-method="bank" disabled
                    class="rounded-md disabled:bg-gray-500 disabled:border-gray-500 disabled:cursor-not-allowed h-[120px] hover:text-white text-[12px] w-full inline-block align-top p-0 tracking-[0] uppercase hover:bg-black border-[1px] border-[solid] border-[#0E60AE] text-white bg-[#0e60ae] transition-colors payment-btn">
                    <p class="text-lg font-bold">{{ __('web.pay-with-card-bank') }}</p>
                    <p class="px-4">{{ __('web.card-bank-text') }}</p>
                </button>

                <button data-method="sales-agreement" disabled
                    class="rounded-md disabled:bg-gray-500 disabled:border-gray-500 disabled:cursor-not-allowed h-[120px] hover:text-white text-[12px] w-full inline-block align-top p-0 tracking-[0] uppercase hover:bg-black border-[1px] border-[solid] border-[#0E60AE] text-white bg-[#0e60ae] transition-colors individual-button payment-btn">
                    <p class="text-lg font-bold">{{ __('web.sales-agreement') }}</p>
                    <p class="px-4">{{ __('web.sales-agreement-text') }}</p>
                </button>

            </div>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <input id="current-locale" hidden value="{{ app()->getLocale() }}">

    @push('custom-css')
        <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
    @endpush

    @section('js')
        <script src="https://fastly.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
        <script>
            const input = document.querySelector("#phone");
            const telInput = window.intlTelInput(input, {
                autoInsertDialCode: true,
                nationalMode: false,
                utilsScript: "https://fastly.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
                initialCountry: "auto",
                separateDialCode: true,
                preferredCountries: ["tr"],
                geoIpLookup: function(callback) {
                    fetch("https://ipapi.co/json")
                        .then(function(res) {
                            return res.json();
                        })
                        .then(function(data) {
                            callback(data.country_code);
                        })
                        .catch(function() {
                            callback("us");
                        });
                },
            });

            function getPhoneNumber() {
                const phoneInput = $("#phone");
                const instance = telInput;
                const number = phoneInput.val();
                const phoneData = instance.getSelectedCountryData();

                return `+${phoneData.dialCode}${number}`;
            }
        </script>
        @if (auth()->user()->isShopOrService())
            <script>
                let pinValid = false;
                let isCompany = false;
                let countryId = null;
                let cityId = null;
                let districtId = null;

                const isTr = $("#current-locale").val() === 'tr';

                $("input[name=company]").on('change', function() {
                    if ($(this).val() == 'Y') {
                        isCompany = true;

                        $(".company-field").removeClass('hidden');
                        $(".company-field").addClass('flex');

                        $(".individual-field").removeClass('flex');
                        $(".individual-field").addClass('hidden');
                        $(".individual-button").removeClass('inline-block');
                        $(".individual-button").addClass('hidden');
                    } else {
                        isCompany = false;

                        $(".company-field").removeClass('flex');
                        $(".company-field").addClass('hidden');

                        $(".individual-field").removeClass('hidden');
                        $(".individual-field").addClass('flex');
                        $(".individual-button").removeClass('hidden');
                        $(".individual-button").addClass('inline-block');
                    }
                })

                $(function() {
                    if ($("input[name=company]").val() == 'Y') {
                        isCompany = true;
                        $(".company-field").removeClass('hidden');
                        $(".company-field").addClass('flex');

                        $(".individual-field").removeClass('flex');
                        $(".individual-field").addClass('hidden');
                        $(".individual-button").removeClass('inline-block');
                        $(".individual-button").addClass('hidden');
                    } else {
                        isCompany = false;
                        $(".company-field").removeClass('flex');
                        $(".company-field").addClass('hidden');

                        $(".individual-field").removeClass('hidden');
                        $(".individual-field").addClass('flex');
                        $(".individual-button").removeClass('hidden');
                        $(".individual-button").addClass('inline-block');
                    }

                    $("input,select").on('change', checkFormValidity);

                    if (!!$("#national_id").val()) {
                        checkNationalValidity();
                    }
                });

                $("#country").on("change", function() {
                    const request = $.get("data/cities/" + $(this).val());
                    request.done(function(resp) {
                        $("#city").empty();
                        $("#district").empty();
                        $("#city").append('<option value="">-</option>');
                        resp.forEach(function(item) {
                            $("#city").append(
                                '<option value="' + item.id + '">' + item.current_translation
                                .city_name + "</option>"
                            );
                        });

                        if (cityId) {
                            $("#city").val(cityId);
                            $("#city").trigger('change');
                        }
                    }).fail(function() {
                        $("#city").empty();
                        $("#district").empty();
                    });
                });

                $("#city").on("change", function() {
                    const request = $.get("data/districts/" + $(this).val());
                    request.done(function(resp) {
                        $("#district").empty();
                        $("#district").append('<option value="">-</option>');
                        resp.forEach(function(item) {
                            $("#district").append(
                                '<option value="' + item.id + '">' + item.current_translation
                                .district_name + "</option>"
                            );
                        });

                        if (districtId) {
                            $("#district").val(districtId);
                            $("#district").trigger('change');
                        }
                    }).fail(function() {
                        $("#district").empty();
                    });
                });

                $("#delivery-city").on("change", function() {
                    const request = $.get("data/delivery-districts/" + $(this).val());
                    request.done(function(resp) {
                        $("#delivery-district").empty();
                        $("#delivery-district").append('<option value="">-</option>');
                        resp.forEach(function(item) {
                            $("#delivery-district").append(
                                '<option value="' + item.id + '">' + item.current_translation
                                .district_name + "</option>"
                            );
                        });
                    }).fail(function() {
                        $("#delivery-district").empty();
                    });
                });

                $("#delivery-district").on("change", function() {
                    const request = $.get("data/service-points/" + $(this).val());
                    request.done(function(resp) {
                        $("#delivery-point").empty();
                        $("#delivery-point").append('<option value=""></option>');
                        resp.forEach(function(item) {
                            $("#delivery-point").append(
                                `<option value="${item.id}">${item.site_user_name} (${item.address})</option>`
                            );
                        });
                    }).fail(function() {
                        $("#delivery-point").empty();
                    });
                });

                $("#order-form").on('submit', function() {
                    $('.payment-btn').attr('disabled', true);
                    if (showLoader) showLoader();
                })

                function checkNationalValidity() {
                    const name = $('#name').val();
                    const surname = $('#surname').val();
                    const nationalId = $("#national_id").val();
                    const birthDay = $('#birth_day').val();
                    const birthMonth = $('#birth_month').val();
                    const birthYear = $('#birth_year').val();
                    const email = $("#email").val();
                    const birthDate = `${birthDay}-${birthMonth}-${birthYear}`;

                    if (!nationalId) {
                        $("#national_id").removeClass('border-1 border-green-500');
                        $("#national_id").addClass('border-1 border-red-500');
                        $("#id-state").val('');
                        checkFormValidity();
                        return;
                    }

                    const request = $.post("data/check-national-id", {
                        name,
                        surname,
                        nationalId,
                        birthDate,
                        email
                    });

                    request.done(function(resp) {
                        if (resp.status) {
                            $("#national_id").removeClass('border-1 border-red-500');
                            $("#national_id").addClass('border-1 border-green-500');
                            $("#id-state").val('y');
                            checkFormValidity();
                            $("#name").attr('readonly', true);
                            $("#surname").attr('readonly', true);
                            $("#national_id").attr('readonly', true);
                            $("#birth_day").attr('readonly', true);
                            $("#birth_month").attr('readonly', true);
                            $("#birth_year").attr('readonly', true);
                            $("#national-alert-message").empty();
                            $("#national-alert-box").addClass('hidden');
                        } else {
                            $("#national_id").removeClass('border-1 border-green-500');
                            $("#national_id").addClass('border-1 border-red-500');
                            $("#id-state").val('');
                            checkFormValidity();

                            if (resp.emailMismatch) {
                                $("#national-alert-message").empty();
                                $("#national-alert-box").removeClass('hidden');
                                $("#national-alert-message").append(`<span>${resp.message}</span>`);
                            } else {
                                $("#national-alert-message").empty();
                                $("#national-alert-box").addClass('hidden');
                            }
                        }
                    }).fail(function(e) {
                        $("#national_id").removeClass('border-1 border-green-500');
                        $("#national_id").addClass('border-1 border-red-500');
                        $("#id-state").val('');
                        checkFormValidity();
                    });
                }

                $("#national_id").on("blur", checkNationalValidity);
                $("#surname").on("blur", checkNationalValidity);
                $("#name").on("blur", checkNationalValidity);
                $("#birth_day").on("change", checkNationalValidity);
                $("#birth_month").on("change", checkNationalValidity);
                $("#birth_year").on("change", checkNationalValidity);

                function checkFormValidity() {
                    let isValid = false;

                    if (isCompany) {
                        const keys = ['delivery-point', 'name', 'surname', 'phone', 'email', 'company_name', 'address', 'country',
                            'city', 'district', 'postal_code', 'tax_office', 'tax_id'
                        ];
                        isValid = keys.map((key) => !!$('#' + key).val());
                    } else {
                        const idState = $("#id-state").val();
                        const keys = ['delivery-point', 'name', 'surname', 'phone', 'email', 'address', 'country', 'city',
                            'district', 'postal_code', 'national_id', 'birth_day', 'birth_month', 'birth_year'
                        ];
                        isValid = keys.map((key) => !!$('#' + key).val());
                        if (!idState) isValid = [false];
                    }

                    if (!isValid.includes(false) && pinValid) {
                        $('.payment-btn').attr('disabled', false);
                    } else {
                        $('.payment-btn').attr('disabled', true);
                    }
                }

                const confirmText = @json($confirmMessage);

                function submitForm(method) {
                    $("#payment-method").val(method);
                    const fname = `${$("#name").val()} ${$("#surname").val()}`;
                    const natId = $("#national_id").val();
                    const birthDay = $('#birth_day').val();
                    const birthMonth = $('#birth_month').val();
                    const birthYear = $('#birth_year').val();
                    const birthDate = `${birthDay}-${birthMonth}-${birthYear}`;

                    const confirmMessage =
                        `${confirmText.text}\n\n${confirmText.fullname}: ${fname}\n${confirmText.birthdate}: ${birthDate}\n${confirmText.nationalId}: ${natId}`;

                    if (method == 'sales-agreement') {
                        if (confirm(confirmMessage)) {
                            $("#order-form").submit();
                        }
                    } else {
                        $("#order-form").submit();
                    }

                }

                $(".payment-btn").on('click', function() {
                    submitForm($(this).attr('data-method'));
                });

                let prevEmail = null;
                let prevPhone = null;

                $("#email").on('blur', async function() {
                    if ($(this).val() == prevEmail) return;

                    if ($("#national_id").val()) {
                        checkNationalValidity();
                    }

                    prevEmail = $(this).val();

                    const response = await fetch('/customer-verify/check-email', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        body: JSON.stringify({
                            email: $("#email").val(),
                        })
                    });

                    const result = await response.json();

                    $("#alert-message").empty();
                    refreshPin('pin_code');
                    pinValid = false;

                    if (result.status) {
                        $("#email").removeClass('border-1 border-red-500');
                        $("#email").addClass('border-1 border-green-500');
                        $("#alert-box").addClass('hidden');

                        if (result.phone) {
                            $(".iti__flag-container").hide();
                            $("#phone").addClass('!px-3');
                            $("#phone").val(result.phone);
                            $("#full_phone").val(result.phone);
                            $("#phone").attr('readonly', true);
                            $("#phone").removeClass('border-1 border-red-500');
                            $("#phone").addClass('border-1 border-green-500');
                            $("#phone-verify-btn").removeClass('hidden');
                        } else {
                            $(".iti__flag-container").show();
                            $("#phone").removeClass('!px-3');
                            $("#phone").val("");
                            $("#full_phone").val("");
                            $("#phone").attr('readonly', false);
                            $("#phone").removeClass('border-1 border-red-500');
                            $("#phone").removeClass('border-1 border-green-500');
                            $("#phone-verify-btn").addClass('hidden');
                        }

                        $("#pin-container").addClass('hidden');
                    } else {
                        $(".iti__flag-container").show();
                        $("#phone").removeClass('!px-3');
                        $("#email").addClass('border-1 border-red-500');
                        $("#email").removeClass('border-1 border-green-500');
                        $("#alert-box").removeClass('hidden');
                        $("#alert-message").append(`<span>${result.message}</span>`);
                        $("#phone-verify-btn").addClass('hidden');
                        $("#pin-container").addClass('hidden');
                    }
                });

                $("#phone").on('blur', async function() {
                    if ($(this).val().includes('*')) return;
                    $(this).val($(this).val().replace(/\D/g, ''));
                    const value = $(this).val();

                    if (value[0] == "0") {
                        $(this).val(value.substring(1));
                    }

                    if (!$("#email").val()) return;

                    const fullPhone = getPhoneNumber();

                    if (fullPhone == prevPhone) return;

                    prevPhone = $(this).val();

                    const response = await fetch('/customer-verify/check-phone', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        body: JSON.stringify({
                            phone: fullPhone,
                        })
                    });

                    const result = await response.json();

                    $("#alert-message").empty();
                    refreshPin('pin_code');
                    pinValid = false;

                    if (result.status) {
                        $("#phone").removeClass('border-1 border-red-500');
                        $("#phone").addClass('border-1 border-green-500');
                        $("#alert-box").addClass('hidden');

                        $("#phone-verify-btn").removeClass('hidden');
                        $("#pin-container").addClass('hidden');
                    } else {
                        $("#phone").addClass('border-1 border-red-500');
                        $("#phone").removeClass('border-1 border-green-500');
                        $("#alert-box").removeClass('hidden');
                        $("#alert-message").append(`<span>${result.message}</span>`);
                        $("#phone-verify-btn").addClass('hidden');
                        $("#pin-container").addClass('hidden');
                    }
                });

                $("#phone-verify-btn").on('click', async function() {
                    const response = await fetch('/customer-verify/otp-request', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        body: JSON.stringify({
                            phone: getPhoneNumber(),
                            email: $("#email").val(),
                            fullName: `${$("#name").val()} ${$("#surname").val()}`,
                        })
                    });

                    pinValid = false;

                    const result = await response.json();

                    if (result.status) {
                        $("#phone-verify-btn").addClass('hidden');
                        $("#pin-container").removeClass('hidden');
                    } else {
                        alert(result.message);
                        $("#pin-container").addClass('hidden');
                    }
                });

                async function verifyCode(code) {
                    showSpinner('pin_code');

                    const response = await fetch('/customer-verify/otp-verify', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        body: JSON.stringify({
                            phone: getPhoneNumber(),
                            email: $("#email").val(),
                            code
                        })
                    });

                    const result = await response.json();

                    if (result.status) {
                        pinValid = true;
                        showPinSuccess('pin_code');
                        $("#email").attr('readonly', true);
                        $("#phone").attr('readonly', true);

                        $("#full_phone").val(getPhoneNumber());

                        if (result.userData) {
                            $("#name").val(result.userData.name);
                            $("#surname").val(result.userData.surname);
                            $("#address").val(result.userData.address);
                            $("#postal_code").val(result.userData.postal_code);

                            if (result.userData.company === 'Y') {
                                $("#company-radio").click();
                            } else {
                                $("#individual-radio").click();
                            }

                            $("#national_id").val(result.userData.national_id);
                            $("#birth_day").val(result.userData.birth_day);
                            $("#birth_month").val(result.userData.birth_month);
                            $("#birth_year").val(result.userData.birth_year);
                            $("#tax_office").val(result.userData.tax_office);
                            $("#tax_id").val(result.userData.tax_id);

                            countryId = result.userData.country;

                            if (countryId) {
                                $("#country").val(countryId);
                                $("#country").trigger('change');
                            }

                            cityId = result.userData.city;
                            districtId = result.userData.district;

                            checkNationalValidity();
                        }
                    } else {
                        pinValid = false;
                        showPinError('pin_code');
                        hideSpinner('pin_code');
                    }
                }
            </script>
        @else
            <script>
                let isCompany = false;

                const isTr = $("#current-locale").val() === 'tr';

                $("input[name=company]").on('change', function() {
                    if ($(this).val() == 'Y') {
                        isCompany = true;

                        $(".company-field").removeClass('hidden');
                        $(".company-field").addClass('flex');

                        $(".individual-field").removeClass('flex');
                        $(".individual-field").addClass('hidden');
                        $(".individual-button").removeClass('inline-block');
                        $(".individual-button").addClass('hidden');
                    } else {
                        isCompany = false;

                        $(".company-field").removeClass('flex');
                        $(".company-field").addClass('hidden');

                        $(".individual-field").removeClass('hidden');
                        $(".individual-field").addClass('flex');
                        $(".individual-button").removeClass('hidden');
                        $(".individual-button").addClass('inline-block');
                    }
                })

                $(function() {
                    if ($("#company-radio").prop('checked')) {
                        isCompany = true;
                        $(".company-field").removeClass('hidden');
                        $(".company-field").addClass('flex');

                        $(".individual-field").removeClass('flex');
                        $(".individual-field").addClass('hidden');
                        $(".individual-button").removeClass('inline-block');
                        $(".individual-button").addClass('hidden');
                    } else {
                        isCompany = false;
                        $(".company-field").removeClass('flex');
                        $(".company-field").addClass('hidden');

                        $(".individual-field").removeClass('hidden');
                        $(".individual-field").addClass('flex');
                        $(".individual-button").removeClass('hidden');
                        $(".individual-button").addClass('inline-block');
                    }

                    $("input,select").on('change', checkFormValidity);

                    if (!!$("#national_id").val()) {
                        checkNationalValidity();
                    }
                });

                $("#country").on("change", function() {
                    const request = $.get("data/cities/" + $(this).val());
                    request.done(function(resp) {
                        $("#city").empty();
                        $("#district").empty();
                        $("#city").append('<option value="">-</option>');
                        resp.forEach(function(item) {
                            $("#city").append(
                                '<option value="' + item.id + '">' + item.current_translation
                                .city_name + "</option>"
                            );
                        });
                    }).fail(function() {
                        $("#city").empty();
                        $("#district").empty();
                    });
                });

                $("#city").on("change", function() {
                    const request = $.get("data/districts/" + $(this).val());
                    request.done(function(resp) {
                        $("#district").empty();
                        $("#district").append('<option value="">-</option>');
                        resp.forEach(function(item) {
                            $("#district").append(
                                '<option value="' + item.id + '">' + item.current_translation
                                .district_name + "</option>"
                            );
                        });
                    }).fail(function() {
                        $("#district").empty();
                    });
                });

                $("#delivery-city").on("change", function() {
                    const request = $.get("data/delivery-districts/" + $(this).val());
                    request.done(function(resp) {
                        $("#delivery-district").empty();
                        $("#delivery-district").append('<option value="">-</option>');
                        resp.forEach(function(item) {
                            $("#delivery-district").append(
                                '<option value="' + item.id + '">' + item.current_translation
                                .district_name + "</option>"
                            );
                        });
                    }).fail(function() {
                        $("#delivery-district").empty();
                    });
                });

                $("#delivery-district").on("change", function() {
                    const request = $.get("data/service-points/" + $(this).val());
                    request.done(function(resp) {
                        $("#delivery-point").empty();
                        $("#delivery-point").append('<option value=""></option>');
                        resp.forEach(function(item) {
                            $("#delivery-point").append(
                                `<option value="${item.id}">${item.site_user_name} (${item.address})</option>`
                            );
                        });
                    }).fail(function() {
                        $("#delivery-point").empty();
                    });
                });

                $("#order-form").on('submit', function() {
                    $('.payment-btn').attr('disabled', true);
                    if (showLoader) showLoader();
                })

                function checkNationalValidity() {
                    const name = $('#name').val();
                    const surname = $('#surname').val();
                    const nationalId = $("#national_id").val();
                    const birthDay = $('#birth_day').val();
                    const birthMonth = $('#birth_month').val();
                    const birthYear = $('#birth_year').val();
                    const email = $("#email").val();
                    const birthDate = `${birthDay}-${birthMonth}-${birthYear}`;

                    if (!nationalId) {
                        $("#national_id").removeClass('border-1 border-green-500');
                        $("#national_id").addClass('border-1 border-red-500');
                        $("#id-state").val('');
                        checkFormValidity();
                        return;
                    }

                    const request = $.post("data/check-national-id", {
                        name,
                        surname,
                        nationalId,
                        birthDate,
                        email
                    });

                    request.done(function(resp) {
                        if (resp.status) {
                            $("#national_id").removeClass('border-1 border-red-500');
                            $("#national_id").addClass('border-1 border-green-500');
                            $("#id-state").val('y');
                            checkFormValidity();
                            $("#name").attr('readonly', true);
                            $("#surname").attr('readonly', true);
                            $("#national_id").attr('readonly', true);
                            $("#date_of_birth").attr('readonly', true);
                            $("#birth_day").attr('readonly', true);
                            $("#birth_month").attr('readonly', true);
                            $("#birth_year").attr('readonly', true);
                            $("#national-alert-message").empty();
                            $("#national-alert-box").addClass('hidden');
                        } else {
                            $("#national_id").removeClass('border-1 border-green-500');
                            $("#national_id").addClass('border-1 border-red-500');
                            $("#id-state").val('');
                            checkFormValidity();

                            if (resp.emailMismatch) {
                                $("#national-alert-message").empty();
                                $("#national-alert-box").removeClass('hidden');
                                $("#national-alert-message").append(`<span>${resp.message}</span>`);
                            } else {
                                $("#national-alert-message").empty();
                                $("#national-alert-box").addClass('hidden');
                            }
                        }
                    }).fail(function(e) {
                        $("#national_id").removeClass('border-1 border-green-500');
                        $("#national_id").addClass('border-1 border-red-500');
                        $("#id-state").val('');
                        checkFormValidity();
                    });
                }

                $("#national_id").on("blur", checkNationalValidity);
                $("#surname").on("blur", checkNationalValidity);
                $("#name").on("blur", checkNationalValidity);
                $("#birth_day").on("change", checkNationalValidity);
                $("#birth_month").on("change", checkNationalValidity);
                $("#birth_year").on("change", checkNationalValidity);

                function checkFormValidity() {
                    let isValid = false;

                    if (isCompany) {
                        const keys = ['delivery-point', 'name', 'surname', 'phone', 'email', 'company_name', 'address', 'country',
                            'city', 'district', 'postal_code', 'tax_office', 'tax_id'
                        ];
                        isValid = keys.map((key) => !!$('#' + key).val());
                    } else {
                        const idState = $("#id-state").val();
                        const keys = ['delivery-point', 'name', 'surname', 'phone', 'email', 'address', 'country', 'city',
                            'district', 'postal_code', 'national_id', 'birth_day', 'birth_month', 'birth_year'
                        ];
                        isValid = keys.map((key) => !!$('#' + key).val());
                        if (!idState) isValid = [false];
                    }

                    if (!isValid.includes(false)) {
                        $('.payment-btn').attr('disabled', false);
                    } else {
                        $('.payment-btn').attr('disabled', true);
                    }
                }

                const confirmText = @json($confirmMessage);

                function submitForm(method) {
                    $("#payment-method").val(method);
                    const fname = `${$("#name").val()} ${$("#surname").val()}`;
                    const natId = $("#national_id").val();
                    const birthDay = $('#birth_day').val();
                    const birthMonth = $('#birth_month').val();
                    const birthYear = $('#birth_year').val();
                    const birthDate = `${birthDay}-${birthMonth}-${birthYear}`;

                    const confirmMessage =
                        `${confirmText.text}\n\n${confirmText.fullname}: ${fname}\n${confirmText.birthdate}: ${birthDate}\n${confirmText.nationalId}: ${natId}`;

                    if (method == 'sales-agreement') {
                        if (confirm(confirmMessage)) {
                            $("#order-form").submit();
                        }
                    } else {
                        $("#order-form").submit();
                    }

                }

                $(".payment-btn").on('click', function() {
                    submitForm($(this).attr('data-method'));
                });
            </script>
        @endif
    @endsection
</x-app-layout>
