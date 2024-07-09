<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @stack('header-tags')

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="/vendor/swipebox/src/css/swipebox.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnify/2.3.3/css/magnify.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link href="{{ asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>
    <!-- Scripts -->
    @vite(['resources/scss/main.scss', 'resources/js/app.js'])
    @stack('custom-css')
    @stack('head-js')

    <script>
        localStorage.theme = 'light'
    </script>

    @env('production')
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-542DBLD');
    </script>
    <!-- End Google Tag Manager -->
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-542DBLD" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <style type="text/css">
        .wa-float-img-circle {
            width: 56px;
            height: 56px;
            bottom: 20px;
            right: 20px;
            border-radius: 100%;
            position: fixed;
            z-index: 99999;
            display: flex;
            transition: all .3s;
            align-items: center;
            justify-content: center;
            background: #25D366;
        }

        .wa-float-img-circle img {
            position: relative;
        }

        .wa-float-img-circle:before {
            position: absolute;
            content: '';
            background-color: #25D366;
            width: 70px;
            height: 70px;
            bottom: -7px;
            right: -7px;
            border-radius: 100%;
            animation: wa-float-circle-fill-anim 2.3s infinite ease-in-out;
            transform-origin: center;
            opacity: .2;
        }

        .wa-float-img-circle:hover {
            box-shadow: 0px 3px 16px #24af588a;
        }

        .wa-float-img-circle:focus {
            box-shadow: 0px 0 0 3px #25d36645;
        }

        .wa-float-img-circle:hover:before,
        .wa-float-img-circle:focus:before {
            display: none;
        }

        @keyframes wa-float-circle-fill-anim {
            0% {
                transform: rotate(0deg) scale(0.7) skew(1deg);
            }

            50% {
                transform: rotate(0deg) scale(1) skew(1deg);
            }

            100% {
                transform: rotate(0deg) scale(0.7) skew(1deg);
            }
        }
    </style>
    @endenv
</head>

<body class="font-sans antialiased">
    <div id="main-loader" class="fixed h-[100vh] w-[100vw] flex justify-center items-center z-[999] bg-white"
        style="display:none;">
        <x-bladewind.spinner size="omg" />
    </div>
    <div class="min-h-screen bg-white">
        <main>
            @include('home.header')
            @include('layouts.app-messages')
            {{ $slot }}
            @include('home.footer')

            @env('production')
            <a href="https://wa.me/905382313135" class="wa-float-img-circle" target="_blank">
                <img src="https://cdn.sendpulse.com/img/messengers/sp-i-small-forms-wa.svg" alt="WhatsApp" />
            </a>
            @endenv
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://fastly.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="/vendor/swipebox/src/js/jquery.swipebox.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnify/2.3.3/js/jquery.magnify.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnify/2.3.3/js/jquery.magnify-mobile.min.js"></script>
    <script src="https://fastly.jsdelivr.net/gh/cferdinandi/tabby@12.0/dist/js/tabby.polyfills.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

    <script>
        function showLoader() {
            $("body").addClass("max-h-[100vh] overflow-hidden");
            $("#main-loader").show();
        }

        function hideLoader() {
            $("body").removeClass("max-h-[100vh] overflow-hidden");
            $("#main-loader").hide();
        }
    </script>

    @stack('js')
    @yield('js')
</body>

</html>
