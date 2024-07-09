<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel'))</title>
    @stack('header-tags')
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
    <link href="{{ asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>

    <!-- Scripts -->
    @vite(['resources/scss/main.scss', 'resources/js/app.js'])
    @stack('head-js')
</head>

<body class="font-sans text-gray-900 antialiased">
    <main class="flex flex-col">
        @include('home.header')
        @include('layouts.app-messages')
        {{ $slot }}
        @include('home.footer')
    </main>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://fastly.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
    <script>
        let phonePrefix = null;
        const input = document.querySelector("#phone-input");
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
    </script>
    @stack('js')
    @yield('js')
</body>

</html>
