<x-app-layout>
    @section('title')
        {{ __('web.my-profile') }}
    @endsection
    <div class="flex flex-col p-5 lg:p-10 text-gray-900 gap-5">
        @include('customer.menu')
        <form id="profile-form" method="POST" action="{{ route('customer.profile-update') }}">
            @method('post')
            @csrf
            <div class="bg-[#f2f2f2] rounded-md p-4 flex flex-col shadow-sm">
                <h1 class="uppercase font-bold text-xl text-center lg:text-left mb-5">
                    {{ __('web.my-profile') }}</h1>

                <div class="rounded-md bg-blue-200 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1 md:flex md:justify-between">
                            <p class="text-md font-bold text-blue-600">{{ __('web.phone-number-note') }}</p>
                        </div>
                    </div>
                </div>

                <div class="pb-3 hidden" id="alert-box">
                    <x-bladewind.alert container_id="alert-message" type="error" shade="dark" show_icon="false"
                        show_close_icon="false" class="font-bold">
                    </x-bladewind.alert>
                </div>

                <div class="flex flex-col lg:flex-row justify-between gap-5 mb-5">
                    <div class="w-full lg:w-1/2">
                        <x-input-label for="email" :value="__('web.email')" />
                        <x-text-input id="email" class="block mt-1 w-full disabled:bg-gray-200" type="email"
                            name="email" :value="$information['email']" required disabled />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div class="w-full lg:w-1/2">
                        <input hidden id="current_phone" value="{{ $information['phone'] }}" />
                        <x-input-label for="phone" :value="__('web.phone')" />
                        <x-text-input id="phone" class="block mt-1 w-full disabled:bg-gray-200" type="text"
                            name="phone" :value="$information['phone']" required />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        <input hidden name="full_phone" id="full_phone" />
                    </div>
                </div>

                <div id="phone-verify-btn" class="w-full items-center justify-end mt-2 mb-4 hidden">
                    <button type="button"
                        class="font-bold rounded-md h-[50px] text-white text-[15px] w-full inline-block align-top p-0 tracking-[0] uppercase border-[1px] border-[solid] border-[#0E60AE] hover:text-white bg-[#0e60ae] hover:bg-blue-500 transition-colors">{{ __('web.verify-phone') }}</button>
                </div>

                <div class="w-full mt-2 py-4 hidden" id="pin-container">
                    <x-bladewind.code name="pin_code" inputClass="bg-white border-2 border-gray-200"
                        on_verify="verifyCode" errorMessage="{{ __('web.verification-code-invalid') }}" />
                </div>

                <div class="flex flex-col lg:flex-row justify-between gap-5 mb-5">
                    <div class="w-full lg:w-1/2">
                        <x-input-label for="name" :value="__('web.name')" />
                        <x-text-input id="name" class="block mt-1 w-full disabled:bg-gray-200" type="text"
                            name="name" :value="$information['name']" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="w-full lg:w-1/2">
                        <x-input-label for="surname" :value="__('web.surname')" />
                        <x-text-input id="surname" class="block mt-1 w-full disabled:bg-gray-200" type="text"
                            name="surname" :value="$information['surname']" required />
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
                        <label for="company-radio" class="ms-2 text-sm text-gray-900">{{ __('web.company') }}</label>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row justify-between gap-5 mb-5 individual-field">
                    <div class="w-full flex flex-row gap-4">
                        <div class="flex flex-col w-1/4">
                            <x-input-label for="birth_day" :value="__('web.birth_day')" />
                            <x-text-input id="birth_day" class="block mt-1" type="text" name="birth_day"
                                :value="$information['birth_day']" required maxlength="2" />
                            <x-input-error :messages="$errors->get('birth_day')" class="mt-2" />
                        </div>
                        <div class="flex flex-col w-1/4">
                            <x-input-label for="birth_month" :value="__('web.birth_month')" />
                            <x-text-input id="birth_month" class="block mt-1 " type="text" name="birth_month"
                                :value="$information['birth_month']" required maxlength="2" />
                            <x-input-error :messages="$errors->get('birth_month')" class="mt-2" />
                        </div>
                        <div class="flex flex-col w-1/4">
                            <x-input-label for="birth_year" :value="__('web.birth_year')" />
                            <x-text-input id="birth_year" class="block mt-1 " type="text" name="birth_year"
                                :value="$information['birth_year']" required maxlength="4" />
                            <x-input-error :messages="$errors->get('birth_year')" class="mt-2" />
                        </div>
                    </div>
                    <div class="w-full">
                        <x-input-label for="national_id" :value="__('web.national_id')" />
                        <x-text-input id="national_id" class="block mt-1 w-full" type="text" name="national_id"
                            :value="$information['national_id']" required />
                        <x-input-error :messages="$errors->get('national_id')" class="mt-2" />
                        <input hidden id="id-state" value="" />
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
                        <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name"
                            :value="$information['company_name']" required />
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
                            <option @if ($information['country'] && $country->id == $information['country']->id) selected @endif value="{{ $country->id }}">
                                {{ $country->currentTranslation->country_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label for="city" class="block mb-2 text-sm">{{ __('web.city') }}</label>
                    <select id="city" name="city"
                        class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @foreach ($cities as $city)
                            <option @if ($city->id == $information['city']->id) selected @endif value="{{ $city->id }}">
                                {{ $city->currentTranslation->city_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label for="district" class="block mb-2 text-sm">{{ __('web.district') }}</label>
                    <select id="district" name="district"
                        class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @foreach ($districts as $district)
                            <option @if ($district->id == $information['district']->id) selected @endif value="{{ $district->id }}">
                                {{ $district->currentTranslation->district_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col lg:flex-row justify-between gap-5 mb-5">
                    <div class="w-full">
                        <x-input-label for="postal_code" :value="__('web.postal_code')" />
                        <x-text-input id="postal_code" class="block mt-1 w-full" type="text" name="postal_code"
                            :value="$information['postal_code']" required />
                        <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                    </div>
                </div>

                <div class="flex-col lg:flex-row justify-between gap-5 mb-10 company-field hidden">
                    <div class="w-full">
                        <x-input-label for="tax_office" :value="__('web.tax_office')" />
                        <x-text-input id="tax_office" class="block mt-1 w-full" type="text" name="tax_office"
                            :value="$information['tax_office']" required />
                        <x-input-error :messages="$errors->get('tax_office')" class="mt-2" />
                    </div>
                    <div class="w-full">
                        <x-input-label for="tax_id" :value="__('web.tax_id')" />
                        <x-text-input id="tax_id" class="block mt-1 w-full" type="text" name="tax_id"
                            :value="$information['tax_id']" required />
                        <x-input-error :messages="$errors->get('tax_id')" class="mt-2" />
                    </div>
                </div>
                <div class="flex items-center justify-end w-full">
                    <button disabled type="button"
                        class="text-white font-bold bg-blue-500 rounded-md py-2 px-4 hover:bg-blue-600 transition-colors w-auto lg:w-auto text-center save-btn disabled:bg-gray-400 disabled:border-gray-500">
                        <i class="fa-solid fa-check"></i> {{ __('app.save') }}
                    </button>
                </div>
            </div>
        </form>

        <form id="password-form" method="POST" action="{{ route('profile.password-update') }}">
            @method('post')
            @csrf
            <div class="bg-[#f2f2f2] rounded-md p-4 flex flex-col shadow-sm">
                <h1 class="uppercase font-bold text-xl text-center lg:text-left mb-5">
                    {{ __('web.password') }}</h1>

                <div class="flex flex-col lg:flex-row justify-between gap-5 mb-5">
                    <div class="w-full">
                        <x-input-label for="current_password" :value="__('app.current_password')" />
                        <x-text-input id="current_password" class="block mt-1 w-full" type="password"
                            name="current_password" required />
                        <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                    </div>
                    <div class="w-full">
                        <x-input-label for="password" :value="__('web.password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                            required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div class="w-full">
                        <x-input-label for="password_confirmation" :value="__('app.password_confirmation')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                            name="password_confirmation" required />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end w-full">
                    <button type="submit"
                        class="text-white font-bold bg-blue-500 rounded-md py-2 px-4 hover:bg-blue-600 transition-colors w-auto lg:w-auto text-center disabled:bg-gray-400 disabled:border-gray-500">
                        <i class="fa-solid fa-check"></i> {{ __('app.update') }}
                    </button>
                </div>
            </div>
        </form>
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

            $("#full_phone").val(getPhoneNumber());

            const isTr = $("#current-locale").val() === 'tr';

            let isCompany = false;

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
                const request = $.get("/data/cities/" + $(this).val());
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
                const request = $.get("/data/districts/" + $(this).val());
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


            $("#profile-form").on('submit', function() {
                $('.save-btn').attr('disabled', true);
            })

            function checkNationalValidity() {
                const name = $('#name').val();
                const surname = $('#surname').val();
                const nationalId = $("#national_id").val();
                const birthDay = $('#birth_day').val();
                const birthMonth = $('#birth_month').val();
                const birthYear = $('#birth_year').val();
                const birthDate = `${birthDay}-${birthMonth}-${birthYear}`;

                if (!nationalId) {
                    $("#national_id").removeClass('border-1 border-green-500');
                    $("#national_id").addClass('border-1 border-red-500');
                    $("#id-state").val('');
                    checkFormValidity();
                    return;
                }

                const request = $.post("/data/check-national-id", {
                    name,
                    surname,
                    nationalId,
                    birthDate
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
                    } else {
                        $("#national_id").removeClass('border-1 border-green-500');
                        $("#national_id").addClass('border-1 border-red-500');
                        $("#id-state").val('');
                        checkFormValidity();
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
                    const keys = ['name', 'surname', 'phone', 'email', 'company_name', 'address', 'country',
                        'city', 'district', 'postal_code', 'tax_office', 'tax_id'
                    ];
                    isValid = keys.map((key) => !!$('#' + key).val());
                } else {
                    const idState = $("#id-state").val();
                    const keys = ['name', 'surname', 'phone', 'email', 'address', 'country', 'city',
                        'district', 'postal_code', 'national_id', 'birth_day', 'birth_month', 'birth_year'
                    ];
                    isValid = keys.map((key) => !!$('#' + key).val());
                    if (!idState) isValid = [false];
                }

                if (!isValid.includes(false)) {
                    $('.save-btn').attr('disabled', false);
                } else {
                    $('.save-btn').attr('disabled', true);
                }
            }

            $(".save-btn").on('click', function() {
                $("#profile-form").submit();
            });

            let prevPhone = null;

            $("#phone").on('blur', async function() {
                $(this).val($(this).val().replace(/\D/g, ''));
                const value = $(this).val();

                if (value[0] == "0") {
                    $(this).val(value.substring(1));
                }

                if (!$("#email").val()) return;

                const fullPhone = getPhoneNumber();

                if (fullPhone == prevPhone) return;

                if (fullPhone == $("#current_phone").val()) {
                    $("#alert-box").addClass('hidden');
                    $("#phone-verify-btn").addClass('hidden');
                    $("#pin-container").addClass('hidden');
                    $("#alert-message").empty();
                    refreshPin('pin_code');
                    prevPhone = $("#current_phone").val();
                } else {
                    prevPhone = $(this).val();

                    $("#alert-box").addClass('hidden');
                    $("#phone-verify-btn").addClass('hidden');
                    $("#pin-container").addClass('hidden');

                    $("#alert-message").empty();
                    refreshPin('pin_code');

                    if (fullPhone.length > 7) {
                        $("#phone-verify-btn").removeClass('hidden');
                    } else {
                        $("#phone-verify-btn").addClass('hidden');
                    }
                }
            });

            $("#phone-verify-btn").on('click', async function() {
                const response = await fetch('/profile-verify/otp', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({
                        phone: getPhoneNumber()
                    })
                });

                const result = await response.json();

                if (result.status) {
                    $("#phone-verify-btn").addClass('hidden');
                    $("#pin-container").removeClass('hidden');
                    $("#full_phone").val(getPhoneNumber());
                } else {
                    // alert(result.message);
                    $("#alert-box").removeClass('hidden');
                    $("#alert-message").append(`<span>${result.message}</span>`);
                    $("#pin-container").addClass('hidden');
                }
            });

            async function verifyCode(code) {
                showSpinner('pin_code');

                const response = await fetch('/profile-verify/verify', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({
                        phone: getPhoneNumber(),
                        code
                    })
                });

                const result = await response.json();

                if (result.status) {
                    showPinSuccess('pin_code');
                    $("#phone").attr('readonly', true);
                    $("#full_phone").val(getPhoneNumber());
                } else {
                    showPinError('pin_code');
                    hideSpinner('pin_code');
                }
            }

            function onlyNumberKey(evt) {
                let ASCIICode = (evt.which) ? evt.which : evt.keyCode
                if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
                    return false;
                return true;
            }

            $("#birth_day, #birth_month, #birth_year").on('keypress', function(e) {
                return onlyNumberKey(e);
            });

            $("#birth_year").on('change', function() {
                if (!$(this).val()) return;

                if ($(this).val() < 1900) {
                    $(this).val("1900");
                }

                if ($(this).val() > new Date().getFullYear()) {
                    $(this).val(new Date().getFullYear());
                }
            });

            $("#birth_day").on('change', function() {
                if (!$(this).val()) return;

                if ($(this).val() < 1) {
                    $(this).val("1");
                }

                if ($(this).val() > 31) {
                    $(this).val("31");
                }
            });

            $("#birth_month").on('change', function() {
                if (!$(this).val()) return;

                if ($(this).val() === '1') {
                    return $(this).val("01");
                }

                if ($(this).val() < 1) {
                    return $(this).val("01");
                }

                const available = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

                if ($(this).val() > 12) {
                    return $(this).val("12");
                }

                if (!available.includes($(this).val())) {
                    const res = available.find((it) => it == `0${$(this).val()}`);

                    if (res) {
                        return $(this).val(res);
                    } else {
                        return $(this).val("01");
                    }
                }

            });
        </script>
    @endsection

</x-app-layout>
