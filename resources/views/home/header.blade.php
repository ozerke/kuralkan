@php
    $locale = App::currentLocale();

    $bajajSections = App\Models\MenuSection::getSectionsForBrand('Bajaj');
    $kanuniSections = App\Models\MenuSection::getSectionsForBrand('Kanuni');
@endphp
{{-- <div class="w-full py-4 px-4 bg-gray-400 main-banner">
    place for banner script
</div> --}}
<div id="header" class="flex flex-row w-full flex-wrap shadow-lg">
    <div class="text-center px-[30px] lg:px-[60px] w-full flex justify-between lg:justify-normal items-center">
        <div class="flex items-center">
            <div class="flex lg:hidden pr-[20px] cursor-pointer">
                <img src="{{ URL::asset('build/images/menu-icon.png') }}" alt="Menu" class="h-6 w-6 menu-btn">
            </div>
            <div id="logo">
                <a href="/">
                    <img src="{{ URL::asset('build/images/kuralkanlogo-white.png') }}" alt="Kuralkan logo">
                </a>
            </div>
            <div id="mobile-menu" class="ham-menu">
                <div class="relative h-full w-full flex flex-col">
                    <div class="cursor-pointer py-5 px-5">
                        <img src="{{ URL::asset('build/images/menu-icon.png') }}" alt="Menu"
                            class="h-8 w-8 menu-btn">
                    </div>
                    <div class="flex flex-col">
                        <div class="mobile-item flex items-center justify-center cursor-pointer group"
                            onclick="showModal('bajaj-modal')">
                            <img src="{{ URL::asset('build/images/menu/bajaj.png') }}" alt="Bajaj"
                                class="h-[30px] w-auto filter-menu-item group-hover:filter-none">
                        </div>
                        <div class="mobile-item flex items-center justify-center cursor-pointer group"
                            style="border-top: 0;" onclick="showModal('kanuni-modal')">

                            <img src="{{ URL::asset('build/images/menu/kanuni.png') }}" alt="Kanuni"
                                class="h-[30px] w-auto filter-menu-item group-hover:filter-none">
                        </div>
                        <div class="mobile-item flex items-center justify-center cursor-pointer group"
                            style="border-top: 0;">
                            <a href="https://yedekparca.ekuralkan.com/">
                                <img src="{{ URL::asset('build/images/menu/yedek-parca.png') }}" alt="Yeded Parca"
                                    class="h-[30px] w-auto filter-menu-item group-hover:filter-none">
                            </a>
                        </div>
                    </div>
                    <div class="flex flex-col mobile-item-group text-left">
                        <span class="title">{{ __('web.my-account') }}</span>
                        @if (!auth()->user())
                            <a href="{{ route('login') }}">
                                <img src="{{ URL::asset('build/images/menu/menub1.svg') }}" data-ll-status="loaded">
                                <span>{{ __('web.login') }}</span>
                            </a>
                        @endif
                        @if (auth()->user())
                            <a href="{{ route('panel') }}">
                                <img src="{{ URL::asset('build/images/menu/orders.svg') }}" data-ll-status="loaded">
                                <span>{{ __('web.my-orders') }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="flex flex-col mobile-item-group text-left social">
                        <span class="title">{{ __('web.follow-us') }}</span>

                        <div class="flex gap-6">
                            <a href="/" rel="noreferrer" target="_blank"
                                class="text-gray-700 transition hover:opacity-75">
                                <span class="sr-only">Facebook</span>

                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="/" rel="noreferrer" target="_blank"
                                class="text-gray-700 transition hover:opacity-75">
                                <span class="sr-only">Instagram</span>

                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="/" rel="noreferrer" target="_blank"
                                class="text-gray-700 transition hover:opacity-75">
                                <span class="sr-only">Twitter</span>

                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path
                                        d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                </svg>
                            </a>
                            <a href="/" rel="noreferrer" target="_blank"
                                class="text-gray-700 transition hover:opacity-75">
                                <span class="sr-only">WhatsApp</span>

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                </svg>
                            </a>
                            <a href="/" rel="noreferrer" target="_blank"
                                class="text-gray-700 transition hover:opacity-75">
                                <span class="sr-only">Youtube</span>

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z" />
                                </svg>
                            </a>

                        </div>

                    </div>
                </div>

            </div>
        </div>
        <div class="hidden lg:flex flex-row ml-[30px] border-l-[1px] navigation">
            <div class="px-[25px] py-[33px] border-r-[1px] nav-item cursor-pointer">
                <div>
                    <img src="{{ URL::asset('build/images/menu/bajaj.png') }}" alt="Bajaj"
                        class="h-[25px] w-auto filter-menu-item hover:filter-none object-contain">
                </div>

                <div class="altMenu rounded-b-md border-t-[1px] gap-6 flex-col shadow-lg max-h-[80vh] custom-scroll">
                    @if (count($bajajSections))
                        <div class="flex flex-col px-4 pt-4 gap-4">
                            @foreach ($bajajSections as $section)
                                @if (count($section['items']))
                                    <div class="flex justify-center items-center gap-2">
                                        @if ($section['categoryUrl'])
                                            <a href="{{ $section['categoryUrl'] }}"
                                                class="text-2xl font-bold rounded-md uppercase">
                                                {{ $section['title'] }}</a>
                                        @else
                                            <h2 class="text-2xl font-bold rounded-md uppercase">
                                                {{ $section['title'] }}</h2>
                                        @endif
                                        <div class="bg-brand h-[4px] flex-auto rounded-md"></div>
                                    </div>
                                    <div class="flex w-full gap-4 overflow-x-auto custom-scroll py-2">
                                        @foreach ($section['items'] as $product)
                                            <a href="{{ $product->detailsUrl() }}"
                                                class="min-h-[180px] h-[180px] min-w-[140px] w-[140px] flex flex-col bg-white rounded-md group cursor-pointer transition-all border-[1px] hover:border-[#0e60ae] overflow-hidden">
                                                <div class="h-[130px] rounded-t-md">
                                                    @if (optional($product->firstDisplayableVariation)->firstMedia)
                                                        <img style="object-fit: contain; border-radius:12px;"
                                                            class="h-[130px] w-full p-2"
                                                            src="{{ $product->firstDisplayableVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}">
                                                    @else
                                                        <img src="{{ URL::asset('build/images/kuralkanlogo-white.png') }}"
                                                            style="object-fit: contain; border-radius:12px;"
                                                            class="h-[130px] w-full p-2">
                                                    @endif
                                                </div>
                                                <div
                                                    class="h-[50px] bg-[#e9e9e9] flex flex-col items-center justify-center rounded-t-md text-sm transition-colors group-hover:bg-[#0e60ae] group-hover:text-white text-center">
                                                    <span
                                                        class="font-bold">{{ $product->currentTranslation->getNameWithoutBrand('Bajaj') }}</span>

                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <a title="{{ __('web.bajaj-spare-parts') }}" target="_self"
                        href="https://yedekparca.ekuralkan.com/"
                        class="flex bg-brand rounded-b-md items-center justify-center hover:bg-blue-800 transition-colors p-4 gap-2">
                        <i class="fa-solid fa-wrench text-white text-md"></i><span
                            class="text-white font-bold">{{ __('web.bajaj-spare-parts') }}</span>
                    </a>
                </div>
            </div>
            <div class="px-[25px] py-[33px] border-r-[1px] nav-item cursor-pointer">
                <div>
                    <img src="{{ URL::asset('build/images/menu/kanuni.png') }}" alt="Kanuni"
                        class="h-[25px] w-auto filter-menu-item hover:filter-none object-contain">
                </div>

                <div class="altMenu rounded-b-md border-t-[1px] gap-6 flex-col shadow-lg max-h-[80vh] custom-scroll">
                    @if (count($kanuniSections))
                        <div class="flex flex-col px-4 pt-4 gap-4">
                            @foreach ($kanuniSections as $section)
                                @if (count($section['items']))
                                    <div class="flex justify-center items-center border-gray-300 gap-2">
                                        @if ($section['categoryUrl'])
                                            <a href="{{ $section['categoryUrl'] }}"
                                                class="text-2xl font-bold rounded-md uppercase">
                                                {{ $section['title'] }}</a>
                                        @else
                                            <h2 class="text-2xl font-bold rounded-md uppercase">
                                                {{ $section['title'] }}</h2>
                                        @endif
                                        <div class="bg-brand h-[4px] flex-auto rounded-md"></div>
                                    </div>
                                    <div class="flex w-full gap-4 overflow-x-auto custom-scroll py-2">
                                        @foreach ($section['items'] as $product)
                                            <a href="{{ $product->detailsUrl() }}"
                                                class="min-h-[180px] h-[180px] min-w-[140px] w-[140px] flex flex-col bg-white rounded-md group cursor-pointer transition-all border-[1px] hover:border-[#0e60ae] overflow-hidden">
                                                <div class="h-[130px] rounded-t-md">
                                                    @if (optional($product->firstDisplayableVariation)->firstMedia)
                                                        <img style="object-fit: contain; border-radius:12px;"
                                                            class="h-[130px] w-full p-2"
                                                            src="{{ $product->firstDisplayableVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}">
                                                    @else
                                                        <img src="{{ URL::asset('build/images/kuralkanlogo-white.png') }}"
                                                            style="object-fit: contain; border-radius:12px;"
                                                            class="h-[130px] w-full p-2">
                                                    @endif
                                                </div>
                                                <div
                                                    class="h-[50px] bg-[#e9e9e9] flex flex-col items-center justify-center rounded-t-md font-bold text-sm transition-colors group-hover:bg-[#0e60ae] group-hover:text-white text-center">
                                                    <span>{{ $product->currentTranslation->getNameWithoutBrand('Kanuni') }}</span>

                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <a title="{{ __('web.kanuni-spare-parts') }}" target="_self"
                        href="https://yedekparca.ekuralkan.com/"
                        class="flex bg-brand rounded-b-md items-center justify-center hover:bg-blue-800 transition-colors p-4 gap-2">
                        <i class="fa-solid fa-wrench text-white text-md"></i><span
                            class="text-white font-bold">{{ __('web.kanuni-spare-parts') }}</span>
                    </a>
                </div>

            </div>
            <div class="px-[25px] py-[33px] border-r-[1px] nav-item">
                <a href="https://yedekparca.ekuralkan.com/">
                    <img src="{{ URL::asset('build/images/menu/yedek-parca.png') }}" alt="Yeded Parca"
                        class="h-[25px] w-auto filter-menu-item hover:filter-none object-contain">
                </a>
            </div>
        </div>

        <div class="hidden xl:flex items-center px-[25px] border-r-[1px] h-full flex-grow">
            <form method="GET" class="w-full" action="{{ route('searchForProducts') }}">
                <div class="relative text-gray-600 focus-within:text-gray-400 w-full">
                    <input name="q" placeholder="{{ __('web.search') }}"
                        class="w-full bg-white h-10 px-1 rounded-none text-sm focus:outline-none border-none search-input">
                    <button type="submit" class="absolute right-0 top-0 mt-3 mr-4">
                        <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px"
                            viewBox="0 0 56.966 56.966" style="enable-background:new 0 0 56.966 56.966;"
                            xml:space="preserve" width="512px" height="512px">
                            <path
                                d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23  s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92  c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17  s-17-7.626-17-17S14.61,6,23.984,6z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
        <div class="hidden lg:flex hover:bg-[#ededed] transition-default border-r-[1px]">
            <a href="{{ route('salesPoints') }}" class="icon-menu-item filter-menu-item hover:filter-none">
                <img src="{{ URL::asset('build/images/menu/hr1.png') }}" alt="Sales Points"
                    class="h-[25px] w-auto mr-[10px]">
                <span>{{ __('web.sales-points') }}</span>
            </a>
        </div>
        <div class="hidden lg:flex hover:bg-[#ededed] transition-default border-r-[1px]">
            <a href="{{ route('servicePoints') }}" class="icon-menu-item filter-menu-item hover:filter-none">
                <img src="{{ URL::asset('build/images/menu/hr2.png') }}" alt="Service Points"
                    class="h-[25px] w-auto mr-[10px]">
                <span>{{ __('web.service-points') }}</span>
            </a>
        </div>
        <div class="flex items-center ml-[20px] gap-3 flex-1 justify-end min-w-[125px]">
            <div class="dropdown inline-block relative mr-[10px]">
                <button class="rounded-md text-gray-700 font-semibold py-2 inline-flex items-center">
                    <img src="{{ URL::asset("build/images/flags/{$locale}.svg") }}" class="rounded-md elevation-1"
                        alt="Language flag" height="25" width="25">
                </button>
                <div
                    class="dropdown-menu absolute hidden min-w-fit z-[999] border-2 shadow-lg border-gray-400 rounded-md">
                    <div class="flex flex-col min-w-fit bg-white gap-2 py-3 px-2 rounded-md">
                        <div class="group">
                            <a class="w-max flex gap-2 rounded-0 bg-white group-hover:font-bold" href="#"
                                onclick="event.preventDefault(); document.getElementById('lang-en-form').submit();">
                                <img src="{{ URL::asset('build/images/flags/en.svg') }}"
                                    class="elevation-1 rounded-md" alt="Language flag" height="25"
                                    width="25">
                                English
                            </a>
                        </div>
                        <div class="group">
                            <a class="w-max flex gap-2 rounded-0 bg-white group-hover:font-bold" href="#"
                                onclick="event.preventDefault(); document.getElementById('lang-tr-form').submit();">
                                <img src="{{ URL::asset('build/images/flags/tr.svg') }}"
                                    class="elevation-1 rounded-md" alt="Language flag" height="25"
                                    width="25">
                                Türkçe
                            </a>
                        </div>
                        <form id="lang-en-form" action="/change-language/en" method="GET" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                        <form id="lang-tr-form" action="/change-language/tr" method="GET" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </div>
            </div>
            <a href="{{ route('login') }}" class="">
                <img src="{{ URL::asset('build/images/menu/menub1.svg') }}" alt="User area"
                    class="w-[22px] h-[22px] mr-[10px]">
            </a>
            <a href="{{ route('cart') }}" class="relative">
                <img src="{{ URL::asset('build/images/menu/menub3.svg') }}" alt="Cart"
                    class="w-[22px] h-[22px] mr-[10px]">
                <span id="cart-number" class="cart-label">{{ count(Cart::name('current')->getItems()) }}</span>
            </a>

        </div>
    </div>
    <div class="flex flex-1 lg:hidden flex-row w-full">
        <div class="text-center w-full flex items-center">
            <div class="flex items-center px-[10px] border-r-[1px] border-t-[1px] h-[50px] w-[50%]">
                <form method="GET" class="w-full" action="{{ route('searchForProducts') }}">
                    <div class="relative text-gray-600 focus-within:text-gray-400 w-full">
                        <input name="q" placeholder="{{ __('web.search') }}"
                            class="bg-white h-10 px-1 rounded-none text-sm focus:outline-none border-none w-full mobile-search-input">
                        <button type="submit" class="absolute right-0 top-0 mt-3 mr-4">
                            <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px"
                                y="0px" viewBox="0 0 56.966 56.966" style="enable-background:new 0 0 56.966 56.966;"
                                xml:space="preserve" width="512px" height="512px">
                                <path
                                    d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23  s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92  c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17  s-17-7.626-17-17S14.61,6,23.984,6z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
            <div class="flex hover:bg-[#ededed] transition-default border-r-[1px] border-t-[1px] w-[50%] h-full">
                <a href="{{ route('salesPoints') }}"
                    class="icon-menu-item-mobile filter-menu-item hover:filter-none">
                    <img src="{{ URL::asset('build/images/menu/hr1.png') }}" alt="Sales Points"
                        class="h-[18px] w-auto mr-[10px]">
                    <span>{{ __('web.sales-points') }}</span>
                </a>
            </div>
            <div class="flex hover:bg-[#ededed] transition-default border-t-[1px] w-[50%] h-full">
                <a href="{{ route('servicePoints') }}"
                    class="icon-menu-item-mobile filter-menu-item hover:filter-none">
                    <img src="{{ URL::asset('build/images/menu/hr2.png') }}" alt="Service Points"
                        class="h-[18px] w-auto mr-[10px]">
                    <span>{{ __('web.service-points') }}</span>
                </a>
            </div>

        </div>
    </div>
</div>

<input id="locale" hidden value="{{ $locale }}" />

<x-bladewind.modal name="bajaj-modal" size="omg" show_action_buttons="false" showCloseIcon="true"
    body_css="!p-1">
    <div class="flex w-full items-center justify-end px-4 pb-2">
        <div class="flex items-center justify-center bg-brand rounded-md h-[25px] w-[25px] cursor-pointer"
            onclick="hideModal('bajaj-modal')"><i class="fa-solid fa-close text-white text-lg"></i>
        </div>
    </div>
    <div class="flex gap-6 flex-col max-h-[85dvh] overflow-y-scroll">
        @if (count($bajajSections))
            <div class="flex flex-col px-4 pt-4 gap-4">
                @foreach ($bajajSections as $section)
                    @if (count($section['items']))
                        <div class="flex justify-center items-center gap-2">
                            @if ($section['categoryUrl'])
                                <a href="{{ $section['categoryUrl'] }}"
                                    class="text-lg font-bold rounded-md uppercase">
                                    {{ $section['title'] }}</a>
                            @else
                                <h2 class="text-lg font-bold rounded-md uppercase">
                                    {{ $section['title'] }}</h2>
                            @endif
                            <div class="bg-brand h-[4px] flex-auto rounded-md"></div>
                        </div>
                        <div class="flex flex-col w-full gap-4 overflow-x-auto custom-scroll py-2">
                            @foreach ($section['items'] as $product)
                                <a href="{{ $product->detailsUrl() }}"
                                    class="min-h-[80px] h-[80px] w-full flex bg-white rounded-md group cursor-pointer transition-all border-[1px] hover:border-[#0e60ae] overflow-hidden">
                                    <div class="h-[80px] w-[80px] rounded-t-md">
                                        @if (optional($product->firstDisplayableVariation)->firstMedia)
                                            <img style="object-fit: contain; border-radius:12px;"
                                                class="h-[80px] w-full p-2"
                                                src="{{ $product->firstDisplayableVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}">
                                        @else
                                            <img src="{{ URL::asset('build/images/kuralkanlogo-white.png') }}"
                                                style="object-fit: contain; border-radius:12px;"
                                                class="h-[80px] w-full p-2">
                                        @endif
                                    </div>
                                    <div
                                        class="h-full bg-[#e9e9e9] px-2 flex flex-col flex-auto items-center justify-center rounded-l-md text-md transition-colors group-hover:bg-[#0e60ae] group-hover:text-white text-center">
                                        <span
                                            class="font-bold">{{ $product->currentTranslation->getNameWithoutBrand('Bajaj') }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <a title="{{ __('web.bajaj-spare-parts') }}" target="_self" href="https://yedekparca.ekuralkan.com/"
            class="flex bg-brand rounded-md items-center justify-center hover:bg-blue-800 transition-colors p-4 gap-2">
            <i class="fa-solid fa-wrench text-white text-md"></i><span
                class="text-white font-bold">{{ __('web.bajaj-spare-parts') }}</span>
        </a>
    </div>
</x-bladewind.modal>

<x-bladewind.modal name="kanuni-modal" size="omg" show_action_buttons="false" showCloseIcon="true"
    body_css="!p-1">
    <div class="flex w-full items-center justify-end px-4 pb-2">
        <div class="flex items-center justify-center bg-brand rounded-md h-[25px] w-[25px] cursor-pointer"
            onclick="hideModal('kanuni-modal')"><i class="fa-solid fa-close text-white text-lg"></i>
        </div>
    </div>
    <div class="flex gap-6 flex-col max-h-[85dvh] overflow-y-scroll">
        @if (count($kanuniSections))
            <div class="flex flex-col px-4 pt-4 gap-4">
                @foreach ($kanuniSections as $section)
                    @if (count($section['items']))
                        <div class="flex justify-center items-center gap-2">
                            @if ($section['categoryUrl'])
                                <a href="{{ $section['categoryUrl'] }}"
                                    class="text-lg font-bold rounded-md uppercase">
                                    {{ $section['title'] }}</a>
                            @else
                                <h2 class="text-lg font-bold rounded-md uppercase">
                                    {{ $section['title'] }}</h2>
                            @endif
                            <div class="bg-brand h-[4px] flex-auto rounded-md"></div>
                        </div>
                        <div class="flex flex-col w-full gap-4 overflow-x-auto custom-scroll py-2">
                            @foreach ($section['items'] as $product)
                                <a href="{{ $product->detailsUrl() }}"
                                    class="min-h-[80px] h-[80px] w-full flex bg-white rounded-md group cursor-pointer transition-all border-[1px] hover:border-[#0e60ae] overflow-hidden">
                                    <div class="h-[80px] w-[80px] rounded-t-md">
                                        @if (optional($product->firstDisplayableVariation)->firstMedia)
                                            <img style="object-fit: contain; border-radius:12px;"
                                                class="h-[80px] w-full p-2"
                                                src="{{ $product->firstDisplayableVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}">
                                        @else
                                            <img src="{{ URL::asset('build/images/kuralkanlogo-white.png') }}"
                                                style="object-fit: contain; border-radius:12px;"
                                                class="h-[130px] w-full p-2">
                                        @endif
                                    </div>
                                    <div
                                        class="h-full bg-[#e9e9e9] px-2 flex flex-col flex-auto items-center justify-center rounded-l-md text-md transition-colors group-hover:bg-[#0e60ae] group-hover:text-white text-center">
                                        <span
                                            class="font-bold">{{ $product->currentTranslation->getNameWithoutBrand('Kanuni') }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <a title="{{ __('web.kanuni-spare-parts') }}" target="_self" href="https://yedekparca.ekuralkan.com/"
            class="flex bg-brand rounded-md items-center justify-center hover:bg-blue-800 transition-colors p-4 gap-2">
            <i class="fa-solid fa-wrench text-white text-md"></i><span
                class="text-white font-bold">{{ __('web.kanuni-spare-parts') }}</span>
        </a>
    </div>
</x-bladewind.modal>

<x-bladewind.modal name="search-modal" size="big" show_action_buttons="false" showCloseIcon="true"
    body_css="!p-1">
    <div class="flex w-full items-center justify-between px-4 pb-2 gap-6">
        <div class="relative text-gray-600 w-full rounded-md border-2">
            <input id="inner-search-input" placeholder="{{ __('web.search') }}"
                class="rounded-md bg-white h-10 px-1 text-sm focus:outline-none border-none w-full">
            <div class="absolute right-0 top-0 mt-3 mr-4">
                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px"
                    viewBox="0 0 56.966 56.966" style="enable-background:new 0 0 56.966 56.966;" xml:space="preserve"
                    width="512px" height="512px">
                    <path
                        d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23  s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92  c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17  s-17-7.626-17-17S14.61,6,23.984,6z" />
                </svg>
            </div>
        </div>
        <div class="flex items-center justify-center bg-brand rounded-md h-[25px] w-[25px] cursor-pointer"
            onclick="hideModal('search-modal')"><i class="fa-solid fa-close text-white text-lg"></i>
        </div>
    </div>
    <div class="flex gap-6 flex-col max-h-[85dvh] overflow-y-scroll">
        <div class="flex flex-col px-4 pt-4 gap-4">
            <div class="flex justify-center items-center gap-2">
                <h2 class="text-lg font-bold rounded-md uppercase">
                    {{ __('web.search') }}</h2>
                <div class="bg-brand h-[4px] flex-auto rounded-md"></div>
            </div>
            <div class="flex flex-col w-full gap-4 overflow-x-auto custom-scroll py-2" id="search-results">
                <a href=""
                    class="min-h-[80px] h-[80px] w-full flex bg-white rounded-md group cursor-pointer transition-all border-[1px] hover:border-[#0e60ae] overflow-hidden">
                    <div class="h-[80px] w-[80px] rounded-t-md">
                        <img style="object-fit: contain; border-radius:12px;" class="h-[80px] w-full p-2"
                            src="">
                    </div>
                    <div
                        class="h-full bg-[#e9e9e9] px-2 flex flex-col flex-auto items-center justify-center rounded-l-md text-md transition-colors group-hover:bg-[#0e60ae] group-hover:text-white text-center">
                        <span class="font-bold"></span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-bladewind.modal>

@push('js')
    <script>
        $(".menu-btn").on('click', function() {
            if ($("#mobile-menu").hasClass('active')) {
                // disable
                $("#mobile-menu").removeClass('active');
                $('.main-banner').removeClass('hidden');
                $("body").removeClass("overflow-hidden");
            } else {
                // enable
                $("#mobile-menu").addClass('active');
                $('.main-banner').addClass('hidden');
                $("body").addClass("overflow-hidden");

            }
        });

        $(".nav-item").on('click', function() {
            $('.altMenu').not($(this).find('.altMenu')).removeClass('menu-active');
            const menu = $(this).find('.altMenu');
            menu.toggleClass('menu-active');
        });

        const debounce = (callback, wait) => {
            let timeoutId = null;
            return (...args) => {
                window.clearTimeout(timeoutId);
                timeoutId = window.setTimeout(() => {
                    callback.apply(null, args);
                }, wait);
            };
        }

        async function search(query, reopen) {
            const response = await fetch(`/api/search?q=${query}&locale=${$("#locale").val()}`, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    "Content-Type": "application/json",
                }
            });

            const results = await response.json();

            $("#search-results").empty();

            if (results?.products) {
                results.products.forEach(function(item) {

                    $("#search-results").append(`
                    <a href="${item.url}"
                    class="min-h-[80px] h-[80px] w-full flex bg-white rounded-md group cursor-pointer transition-all border-[1px] hover:border-[#0e60ae] overflow-hidden">
                            <div class="h-[80px] w-[80px] rounded-t-md">
                                <img style="object-fit: contain; border-radius:12px;" class="h-[80px] w-full p-2"
                                    src="${item.img}">
                            </div>
                            <div
                                class="h-full bg-[#e9e9e9] px-2 flex flex-col flex-auto items-center justify-center rounded-l-md text-md transition-colors group-hover:bg-[#0e60ae] group-hover:text-white text-center">
                                <span class="font-bold">${item.fullTitle}</span>
                            </div>
                        </a>
                    `);
                });

                if (reopen) {
                    showModal('search-modal');
                }
            }
        }

        function handleSearch(q, reopen) {
            let results = [];

            if (q && q.length >= 3) {
                results = search(q, reopen);
            } else {
                results = [];
                hideModal('search-modal');
            }
        }

        function onSearchInput(q, reopen = true) {
            handleSearch(q, reopen);
        }

        $(".search-input").on('input', debounce(function() {
            const q = $(".search-input").val();
            $("#inner-search-input").val(q);
            onSearchInput(q);
        }, 700));

        $(".mobile-search-input").on('input', debounce(function() {
            const q = $(".mobile-search-input").val();
            $("#inner-search-input").val(q);
            onSearchInput(q);
        }, 700));

        $("#inner-search-input").on('input', debounce(function() {
            const q = $("#inner-search-input").val();
            $(".mobile-search-input").val(q);
            $(".search-input").val(q)
            onSearchInput(q, false);
        }, 700));
    </script>
@endpush
