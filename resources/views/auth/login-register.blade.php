<x-guest-layout>
    @section('title')
        {!! $authTitle ?? __('web.home-page-title') !!}
    @endsection
    @push('header-tags')
        <meta name="description" content="{{ $authDesc ?? '' }}" />
        <meta name="keywords" content="{{ $authKeywords ?? '' }}" />
    @endpush
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-[40px] lg:gap-[80px] px-[30px] lg:px-[60px] py-[40px] bg-[#f5f5f5]">
        <div class="col-span-6 border-[1px] h-fit bg-white rounded-lg">
            <h2 class="text-xl font-bold mb-[12px] uppercase bg-[#0e60ae] p-4 rounded-t-lg text-white">
                {{ __('web.login') }}</h2>
            <form method="POST" action="{{ route('login') }}" class="p-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('web.email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                        required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('web.password')" />

                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                        autocomplete="current-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('web.remember-me') }}</span>
                    </label>
                </div>

                <div class="flex flex-col items-center gap-4 mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            href="{{ route('password.request') }}">
                            {{ __('web.forgot-your-password') }}
                        </a>
                    @endif

                    <button
                        class="font-bold rounded-md h-[50px] text-[#0E60AE] text-[15px] w-full inline-block align-top p-0 tracking-[0] uppercase bg-transparent border-[1px] border-[solid] border-[#0E60AE] hover:text-white hover:bg-[#0e60ae] transition-colors">{{ __('web.login') }}</button>
                </div>
            </form>
        </div>
        <div class="col-span-6 border-[1px] bg-white rounded-lg">
            <h2 class="text-xl font-bold mb-[12px] uppercase bg-[#0e60ae] p-4 rounded-t-lg text-white">
                {{ __('web.register') }}</h2>
            <form method="POST" action="{{ route('register') }}" class="p-4">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('web.name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                        :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="surname" :value="__('web.surname')" />
                    <x-text-input id="surname" class="block mt-1 w-full" type="text" name="surname"
                        :value="old('surname')" required autofocus autocomplete="surname" />
                    <x-input-error :messages="$errors->get('surname')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="register_email" :value="__('web.email')" />
                    <x-text-input id="register_email" class="block mt-1 w-full" type="email" name="register_email"
                        :value="old('register_email')" required autocomplete="email" />
                    <x-input-error :messages="$errors->get('register_email')" class="mt-2" />
                </div>

                <div id="error-container" class="rounded-md bg-red-100 p-4 mt-4 hidden">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1 md:flex md:justify-between">
                            <p id="message-block" class="text-md font-bold text-red-700">
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <x-input-label for="phone" :value="__('web.phone')" />
                    <x-text-input id="phone-input" class="block mt-1 w-full" type="tel" name="phone"
                        :value="old('phone')" required autocomplete="phone" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <input hidden name="full_phone" id="full_phone" />

                <div id="phone-verify-btn" class="items-center justify-end mt-8 mb-8 hidden">
                    <button type="button"
                        class="font-bold rounded-md h-[50px] text-[#0E60AE] text-[15px] w-full inline-block align-top p-0 tracking-[0] uppercase bg-transparent border-[1px] border-[solid] border-[#0E60AE] hover:text-white hover:bg-[#0e60ae] transition-colors">{{ __('web.verify-phone') }}</button>
                </div>

                <div class="mt-4 py-4 hidden" id="pin-container">
                    <x-bladewind.code name="pin_code" inputClass="bg-gray-100 border-2 border-gray-200"
                        on_verify="verifyCode" errorMessage="{{ __('web.verification-code-invalid') }}" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('web.password')" />

                    <x-text-input id="new-password" class="block mt-1 w-full" type="password" name="password" required
                        autocomplete="new-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('web.password-confirm')" />

                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                        name="password_confirmation" required autocomplete="new-password" />

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="block mt-4">
                    <label for="conditions" class="inline-flex items-center">
                        <input id="conditions" type="checkbox"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            name="conditions">
                        <span
                            class="ms-2 text-sm text-red-500 font-bold">{{ __('web.conditions-accept-text') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-8">
                    <button id="register-btn" disabled
                        class="font-bold rounded-md h-[50px] text-[#0E60AE] text-[15px] w-full inline-block align-top p-0 tracking-[0] uppercase bg-transparent border-[1px] border-[solid] border-[#0E60AE] hover:text-white hover:bg-[#0e60ae] transition-colors disabled:bg-gray-400 disabled:text-gray-200 disabled:border-gray-200 disabled:cursor-not-allowed">{{ __('web.register') }}</button>
                </div>
            </form>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="csrf-token-verify" content="{{ csrf_token() }}">

    @section('js')
        <script>
            $("#phone-input").on('blur', function() {
                if ($(this).val().length > 0) {
                    $(this).val($(this).val().replace(/\D/g, ''));
                    const value = $(this).val();

                    if (value[0] == "0") {
                        $(this).val(value.substring(1));
                    }

                    if ($(this).val().length > 0) {
                        $("#phone-verify-btn").removeClass('hidden');
                    }
                } else {
                    $("#phone-verify-btn").addClass('hidden');
                }
            });

            function getPhoneNumber() {
                const phoneInput = $("#phone-input");
                const instance = telInput;
                const number = phoneInput.val();
                const phoneData = instance.getSelectedCountryData();

                return `+${phoneData.dialCode}${number}`;
            }

            $("#phone-verify-btn").on('click', async function() {
                const response = await fetch('/otp/request', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({
                        phone: getPhoneNumber(),
                        fullName: `${$("#name").val()} ${$("#surname").val()}`,
                    })
                });

                const result = await response.json();

                if (result.status) {
                    $("#phone-input").attr('readonly', true);
                    $("#phone-verify-btn").addClass('hidden');
                    $("#pin-container").removeClass('hidden');
                    $("#full_phone").val(getPhoneNumber());
                    $("#error-container").addClass('hidden');
                    $("#message-block").val('');
                } else {
                    $("#error-container").removeClass('hidden');
                    $("#message-block").html(result.message);
                }
            });

            async function verifyCode(code) {
                showSpinner('pin_code');

                const response = await fetch('/otp/verify', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token-verify"]').attr('content')
                    },
                    body: JSON.stringify({
                        phone: getPhoneNumber(),
                        code
                    })
                });

                const result = await response.json();

                if (result.status) {
                    showPinSuccess('pin_code');
                    $("#register-btn").attr('disabled', false);
                    $("#full_phone").val(getPhoneNumber());
                } else {
                    showPinError('pin_code');
                    hideSpinner('pin_code');
                }
            }
        </script>
    @endsection

</x-guest-layout>
