@php
$locale = App::currentLocale();
@endphp

<li class="nav-item dropdown">
    <a href="#" class="nav-link" data-toggle="dropdown">
        <img src="{{ URL::asset("build/images/flags/{$locale}.svg") }}" class="rounded-circle elevation-1" alt="Language flag" height="25" width="25">

    </a>

    <ul class="dropdown-menu dropdown-menu-md dropdown-menu-right py-0">
        <li class="user-footer d-flex flex-column">
            <a class="btn btn-default rounded-0" href="#" onclick="event.preventDefault(); document.getElementById('lang-en-form').submit();">
                <img src="{{ URL::asset('build/images/flags/en.svg') }}" class="elevation-1 rounded-circle" alt="Language flag" height="20" width="20">
                English
            </a>
            <a class="btn btn-default rounded-0" href="#" onclick="event.preventDefault(); document.getElementById('lang-tr-form').submit();">
                <img src="{{ URL::asset('build/images/flags/tr.svg') }}" class="elevation-1 rounded-circle" alt="Language flag" height="20" width="20">
                Türkçe
            </a>
            <form id="lang-en-form" action="/change-language/en" method="GET" style="display: none;">
                {{ csrf_field() }}
            </form>
            <form id="lang-tr-form" action="/change-language/tr" method="GET" style="display: none;">
                {{ csrf_field() }}
            </form>
        </li>
    </ul>
</li>
