<x-app-layout>
    @section('title')
        {!! $homeTitle ?? __('web.home-page-title') !!}
    @endsection
    @push('header-tags')
        <meta name="description" content="{{ $homeDesc ?? __('web.home-page-description') }}" />
        <meta name="keywords" content="{{ $homeKeywords ?? __('web.home-page-keywords') }}" />
    @endpush

    <div class="swiper mySwiper">
        <div class="swiper-wrapper h-fit max-h-[100vh]">
            @foreach ($slides as $slide)
                @if ($slide->url)
                    <div class="swiper-slide"><a href="{{ $slide->url }}"><img src="{{ $slide->photo_url }}"
                                alt="{{ $slide->title }}"></a></div>
                @else
                    <div class="swiper-slide"><img src="{{ $slide->photo_url }}" alt="{{ $slide->title }}"></div>
                @endif
            @endforeach
        </div>
        <div class="swiper-button-next swiper-button-next-1"></div>
        <div class="swiper-button-prev swiper-button-prev-1"></div>
        <div class="swiper-pagination"></div>
    </div>

    {{-- <div class="bg-gray-400 py-4 px-2">Banner script here</div> --}}

    <section class="mt-[10px] lg:mt-[100px] mb-[20px] lg:mb-0 pt-0 pb-0 bg-white w-full">
        <div class="flex flex-wrap w-full">
            <div class="text-container-home w-full">
                <small class="text-small-blue">ekuralkan</small>
                <span>{{ __('web.intro-text') }}</span>
                <b>{{ __('web.intro-text-bold') }}</b>
            </div>
        </div>
    </section>

    @if (count($newProducts))
        <section class="mt-[10px] lg:mt-[100px] mb-0 pt-0 pb-0 bg-white w-full flex">
            <div class="w-full px-[0px] lg:px-[60px]">
                <div class="mb-[50px] section-header">
                    <span class="bold">
                        <span class="heading-text">{{ __('web.new-products') }}</span>
                    </span>
                </div>
                <div class="swiper" id="new-products-carousel">
                    <div class="swiper-wrapper h-fit w-full">
                        @foreach ($newProducts as $product)
                            <div class="swiper-slide product-item">
                                <div class="wrapper flex flex-col">
                                    <div class="product-image">
                                        <a href="{{ $product->detailsUrl() }}">
                                            @if (optional($product->firstDisplayableVariation)->firstMedia)
                                                <img
                                                    src="{{ $product->firstDisplayableVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}">
                                            @else
                                                <img src="{{ URL::asset('build/images/kuralkanlogo-white.png') }}"
                                                    style="object-fit: contain">
                                            @endif
                                        </a>
                                    </div>
                                    <div class="flex flex-col py-[20px] w-full h-[150px]">
                                        <span class="product-brand">{{ $product->brand_name }}</span>
                                        <span class="product-title"><a
                                                href="{{ $product->detailsUrl() }}">{{ $product->currentTranslation->product_name }}</a></span>
                                        <span
                                            class="product-price">₺{{ number_format(optional($product->firstDisplayableVariation)->vat_price ?? 0, 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next swiper-button-next-2"></div>
                    <div class="swiper-button-prev swiper-button-prev-2"></div>
                </div>
            </div>
        </section>
    @endif

    @if (count($motorcycles))
        <section class="mt-[10px] lg:mt-[100px] mb-0 pt-0 pb-0 bg-white w-full flex">
            <div class="w-full px-[0px] lg:px-[60px]">
                <div class="mb-[50px] section-header">
                    <span class="bold">
                        <span class="heading-text">{{ __('web.motorcycle') }}</span>
                    </span>
                </div>
                <div class="swiper" id="motorcycle-carousel">
                    <div class="swiper-wrapper h-fit w-full">
                        @foreach ($motorcycles as $product)
                            <div class="swiper-slide product-item">
                                <div class="wrapper flex flex-col">
                                    <div class="product-image">
                                        <a href="{{ $product->detailsUrl() }}">
                                            @if (optional($product->firstDisplayableVariation)->firstMedia)
                                                <img
                                                    src="{{ $product->firstDisplayableVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}">
                                            @else
                                                <img src="{{ URL::asset('build/images/kuralkanlogo-white.png') }}"
                                                    style="object-fit: contain">
                                            @endif
                                        </a>
                                    </div>
                                    <div class="flex flex-col py-[20px] w-full h-[150px]">
                                        <span class="product-brand">{{ $product->brand_name }}</span>
                                        <span class="product-title"><a
                                                href="{{ $product->detailsUrl() }}">{{ $product->currentTranslation->product_name }}</a></span>
                                        <span
                                            class="product-price">₺{{ number_format(optional($product->firstDisplayableVariation)->vat_price ?? 0, 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next swiper-button-next-3"></div>
                    <div class="swiper-button-prev swiper-button-prev-3"></div>
                </div>
            </div>
        </section>
    @endif

    @if (count($scooters))
        <section class="mt-[10px] lg:mt-[100px] mb-0 pt-0 pb-0 bg-white w-full flex">
            <div class="w-full px-[0px] lg:px-[60px]">
                <div class="mb-[50px] section-header">
                    <span class="bold">
                        <span class="heading-text">{{ __('web.scooter') }}</span>
                    </span>
                </div>

                <div class="swiper" id="scooter-carousel">
                    <div class="swiper-wrapper h-fit w-full">
                        @foreach ($scooters as $product)
                            <div class="swiper-slide product-item">
                                <div class="wrapper flex flex-col">
                                    <div class="product-image">
                                        <a href="{{ $product->detailsUrl() }}">
                                            @if (optional($product->firstDisplayableVariation)->firstMedia)
                                                <img
                                                    src="{{ $product->firstDisplayableVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}">
                                            @else
                                                <img src="{{ URL::asset('build/images/kuralkanlogo-white.png') }}"
                                                    style="object-fit: contain">
                                            @endif
                                        </a>
                                    </div>
                                    <div class="flex flex-col py-[20px] w-full h-[150px]">
                                        <span class="product-brand">{{ $product->brand_name }}</span>
                                        <span class="product-title"><a
                                                href="{{ $product->detailsUrl() }}">{{ $product->currentTranslation->product_name }}</a></span>
                                        <span
                                            class="product-price">₺{{ number_format(optional($product->firstDisplayableVariation)->vat_price ?? 0, 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next swiper-button-next-1"></div>
                    <div class="swiper-button-prev swiper-button-prev-1"></div>
                </div>
            </div>
        </section>
    @endif

    @if (count($categories))
        <section
            class="mt-[10px] lg:mt-[80px] mb-[40px] lg:mb-0 pt-0 pb-0 bg-white w-full flex flex-col px-[15px] lg:px-[60px]">
            <div class="text-container-home w-full mb-[30px] lg:mb-[50px]">
                <small class="text-small-blue">ekuralkan</small>
                <span class="heading-text">{{ __('web.choose-one-that-suits-you') }}</span>
            </div>
            <div class="swiper" id="category-carousel">
                <div class="swiper-wrapper h-fit">
                    @foreach ($categories as $category)
                        <div class="swiper-slide item-carousel">
                            <a href="{{ $category->innerUrl() }}">
                                <div class="wrapper">
                                    @if (optional(optional(optional($category->firstProduct)->firstDisplayableVariation)->firstMedia))
                                        <img
                                            src="{{ $category->firstProduct->firstDisplayableVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}">
                                    @else
                                        <img src="{{ URL::asset('build/images/kuralkanlogo-white.png') }}"
                                            style="object-fit: contain">
                                    @endif
                                </div>
                                <span>{{ $category->currentTranslation->category_name }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="mt-[10px] lg:mt-[90px] mb-0 pt-0 pb-0 bg-white w-full flex px-[15px] lg:px-[60px]">
        <div class="swiper" id="brand-carousel">
            <div class="swiper-wrapper h-fit">
                <div class="swiper-slide hoverable-banner"><a href="/category/kanuni"><img
                            src="{{ URL::asset('build/images/menu/slides/kanuni-banner.jpeg') }}" alt="Kanuni"></a>
                </div>
                <div class="swiper-slide hoverable-banner"><a href="/category/bajaj"><img
                            src="{{ URL::asset('build/images/menu/slides/bajaj-banner.jpeg') }}" alt="Bajaj"></a>
                </div>
                <div class="swiper-slide hoverable-banner"><a href="/category/kanuni"><img
                            src="{{ URL::asset('build/images/menu/slides/kanuni-banner.jpeg') }}" alt="Kanuni"></a>
                </div>
                <div class="swiper-slide hoverable-banner"><a href="/category/bajaj"><img
                            src="{{ URL::asset('build/images/menu/slides/bajaj-banner.jpeg') }}" alt="Bajaj"></a>
                </div>
                <div class="swiper-slide hoverable-banner"><a href="/category/kanuni"><img
                            src="{{ URL::asset('build/images/menu/slides/kanuni-banner.jpeg') }}" alt="Kanuni"></a>
                </div>
                <div class="swiper-slide hoverable-banner"><a href="/category/bajaj"><img
                            src="{{ URL::asset('build/images/menu/slides/bajaj-banner.jpeg') }}" alt="Bajaj"></a>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-[15px] lg:mt-[80px] px-0 lg:px-[45px] mb-0 pt-0 pb-0 bg-white w-full flex">
        <div class="flex w-full flex-col lg:flex-row">
            <div class="px-[15px] mb-[15px] lg:mb-0"><a href="{{ route('salesPoints') }}"><img
                        src="{{ URL::asset('build/images/menu/slides/horizontal-banner-1.jpeg') }}"
                        alt="Motisklet"></a></div>
            <div class="px-[15px]"><a href="{{ route('servicePoints') }}"><img
                        src="{{ URL::asset('build/images/menu/slides/horizontal-banner-2.jpeg') }}"
                        alt="Motisklet"></a></div>
        </div>
    </section>

    <section class="mt-[15px] lg:mt-[30px] px-0 lg:px-[45px] mb-0 pt-0 pb-0 bg-white w-full flex">
        <div class="flex w-full flex-col lg:flex-row">
            <div class="px-[15px] lg:mb-0"><a href="#"><img
                        src="{{ URL::asset('build/images/menu/slides/big-banner.jpeg') }}" alt="Motisklet"></a></div>
        </div>
    </section>

    <section class="mt-[15px] lg:mt-[30px] px-0 lg:px-[45px] mb-0 pt-0 pb-0 bg-white w-full flex">
        <div class="flex w-full flex-col lg:flex-row">
            <div class="px-[15px] mb-[15px] lg:mb-0"><a href="https://yedekparca.ekuralkan.com/"><img
                        src="{{ URL::asset('build/images/menu/slides/yedek-bajaj.jpeg') }}" alt="Motisklet"></a>
            </div>
            <div class="px-[15px] mb-[15px] lg:mb-0"><a href="https://yedekparca.ekuralkan.com/"><img
                        src="{{ URL::asset('build/images/menu/slides/yedek-kanuni.jpeg') }}" alt="Motisklet"></a>
            </div>
            <div class="px-[15px]"><a href="https://yedekparca.ekuralkan.com/"><img
                        src="{{ URL::asset('build/images/menu/slides/yedek-soco.jpeg') }}" alt="Motisklet"></a></div>
        </div>
    </section>

    <section class="mt-[15px] lg:mt-[30px] px-0 lg:px-[45px] mb-[60px] pt-0 pb-0 bg-white w-full flex">
        <div class="flex w-full flex-col lg:flex-row">
            <div class="mx-[15px] lg:mb-0 relative"><a class="video-banner"
                    href="https://www.youtube.com/c/ekuralkan"><img
                        src="{{ URL::asset('build/images/menu/slides/video-banner.jpeg') }}" alt="Motisklet"></a>
            </div>
        </div>
    </section>

    @section('js')
        <script>
            var swiper = new Swiper(".mySwiper", {
                autoplay: true,
                loop: true,
                pagination: {
                    el: ".swiper-pagination"
                },
                navigation: {
                    nextEl: ".swiper-button-next-1",
                    prevEl: ".swiper-button-prev-1",
                },
            });

            var bannerSwiper = new Swiper("#brand-carousel", {
                autoplay: true,
                loop: true,
                spaceBetween: 0,
                slidesPerView: 1,
                breakpoints: {
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 40,
                    },
                },
            });

            var categorySwiper = new Swiper("#category-carousel", {
                autoplay: true,
                loop: true,
                spaceBetween: 20,
                slidesPerView: 2,
                breakpoints: {
                    780: {
                        slidesPerView: 3,
                        spaceBetween: 20
                    },
                    950: {
                        slidesPerView: 4,
                        spaceBetween: 20
                    },
                    1024: {
                        slidesPerView: 5,
                        spaceBetween: 20,
                    },
                    1300: {
                        slidesPerView: 6,
                        spaceBetween: 20,
                    },
                },
            });

            var scooterSwiper = new Swiper("#scooter-carousel", {
                navigation: {
                    nextEl: ".swiper-button-next-1",
                    prevEl: ".swiper-button-prev-1",
                },
                autoplay: true,
                loop: true,
                spaceBetween: 20,
                slidesPerView: 2,
                breakpoints: {
                    780: {
                        slidesPerView: 3,
                        spaceBetween: 20
                    },
                    950: {
                        slidesPerView: 4,
                        spaceBetween: 20
                    },
                    1024: {
                        slidesPerView: 5,
                        spaceBetween: 20,
                    }

                    ,
                },
            });

            var motoSwiper = new Swiper("#motorcycle-carousel", {
                navigation: {
                    nextEl: ".swiper-button-next-3",
                    prevEl: ".swiper-button-prev-3",
                },
                autoplay: true,
                loop: true,
                spaceBetween: 20,
                slidesPerView: 2,
                breakpoints: {
                    780: {
                        slidesPerView: 3,
                        spaceBetween: 20
                    },
                    950: {
                        slidesPerView: 4,
                        spaceBetween: 20
                    },
                    1024: {
                        slidesPerView: 5,
                        spaceBetween: 20,
                    }

                    ,
                },
            });

            var newProductsSwiper = new Swiper("#new-products-carousel", {
                navigation: {
                    nextEl: ".swiper-button-next-2",
                    prevEl: ".swiper-button-prev-2",
                },
                autoplay: true,
                loop: true,
                spaceBetween: 20,
                slidesPerView: 2,
                breakpoints: {
                    780: {
                        slidesPerView: 3,
                        spaceBetween: 20
                    },
                    950: {
                        slidesPerView: 4,
                        spaceBetween: 20
                    },
                    1024: {
                        slidesPerView: 5,
                        spaceBetween: 20,
                    }

                    ,
                },
            });
        </script>
    @endsection
</x-app-layout>
