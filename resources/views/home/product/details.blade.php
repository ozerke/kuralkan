@php
    $variationData = $product->getVariationsSaleData(true);

    $installments = $product->getLowestInstallmentOption();
@endphp

<x-app-layout>
    @section('title')
        {{ !empty($product->currentTranslation->seo_title) ? $product->currentTranslation->seo_title : $product->currentTranslation->product_name }}
    @endsection

    @push('header-tags')
        @if ($product->seo_no_index == 'noindex')
            <meta name=”robots” content=”noindex”>
        @endif
        @if ($product->seo_no_follow == 'nofollow')
            <meta name=”robots” content="nofollow">
        @endif
        <meta name="description" content="{{ $product->currentTranslation->seo_desc }}" />
        <meta name="keywords" content="{{ $product->currentTranslation->seo_keywords }}" />
    @endpush

    @section('js')
        <script>
            const variations = @json($product->getVariationsSaleData());

            $("#accordion").accordion({
                active: false,
                heightStyle: "content",
                collapsible: true,
                icons: {
                    header: "plus-icon",
                    activeHeader: "minus-icon"
                }
            });

            $(".custom-accordion").accordion({
                active: false,
                heightStyle: "content",
                collapsible: true,
                icons: {
                    header: "plus-icon",
                    activeHeader: "minus-icon"
                }
            });

            $('.zoom').magnify();

            $('.swipebox').swipebox();
            var tabs = new Tabby('[data-tabs]');

            function quickBuy() {
                $("#quick-buy").submit();
            }

            const firstVariationData = variations.length > 0 ? variations[0] : null;

            $("#estimated-delivery-date").html(firstVariationData?.delivery_date);

            $(".color-box").on('click', function() {
                const variation = $(this).attr('data-variation');
                $(".color-box").removeClass('selected');
                $(this).addClass('selected');
                $("#variation-input").val(variation);

                const variationData = variations.find((item) => item.variation == variation);

                $("#estimated-delivery-date").html(variationData.delivery_date);
                $("#variation-price").html(variationData.price);

                if (variationData.in_stock) {
                    $(".shop-button").removeClass('hidden');
                } else {
                    $(".shop-button").addClass('hidden');
                }

                document.dispatchEvent(new CustomEvent("variation_changed", {
                    detail: {
                        variation
                    }
                }));
            });

            $(".stock-trigger").on('change', function() {
                const color = $("#stocks-color").val();
                const country = $("#stocks-country").val();
                const city = $("#stocks-city").val();
                const district = $("#stocks-district").val();

                getStocks(color, country, city, district);

                if (color && country) {
                    toggleCityContainer(true);

                    if (city) {
                        toggleDistrictContainer(true);
                        toggleResultsContainer(true);
                    } else {
                        toggleDistrictContainer(false);
                        toggleResultsContainer(false);
                    }
                } else {
                    toggleCityContainer(false);
                    toggleDistrictContainer(false);
                    toggleResultsContainer(false);
                }
            });

            function toggleCityContainer(show = true) {
                const cityContainer = $("#city-container");

                if (show) {
                    cityContainer.removeClass('hidden');
                    cityContainer.addClass('flex');
                } else {
                    cityContainer.addClass('hidden');
                    cityContainer.removeClass('flex');
                }
            }

            function toggleDistrictContainer(show = true) {
                const districtContainer = $("#district-container");
                if (show) {
                    districtContainer.removeClass('hidden');
                    districtContainer.addClass('flex');
                } else {
                    districtContainer.addClass('hidden');
                    districtContainer.removeClass('flex');
                }
            }

            function toggleResultsContainer(show = true) {
                const resultsContainer = $("#results-container");
                if (show) {
                    resultsContainer.removeClass('hidden');
                    resultsContainer.addClass('flex');
                } else {
                    resultsContainer.addClass('hidden');
                    resultsContainer.removeClass('flex');
                }
            }

            function getStocks(colorId, countryId, cityId, districtId) {
                const productId = $("#product-id").val();
                const request = $.post("/data/get-stocks", {
                    productId,
                    colorId,
                    countryId,
                    cityId,
                    districtId
                });
                request.done(function(resp) {

                    $("#stocks-city > option.removable").remove();
                    $("#stocks-district > option.removable").remove();
                    $("#results-list").empty();

                    resp.cities.forEach(function(item) {
                        $("#stocks-city").append(
                            `<option value="${item.id}" class="removable" ${item.id == cityId ? 'selected' : ''}>${item.title}</option>`
                        );
                    });

                    resp.districts.forEach(function(item) {
                        $("#stocks-district").append(
                            `<option value="${item.id}" class="removable" ${item.id == districtId ? 'selected' : ''}>${item.title}</option>`
                        );
                    });

                    resp.results.forEach(function(item) {
                        $("#results-list").append(
                            `
                            <div class="flex flex-col gap-1 border-b-[1px] text-[0.9rem] hover:bg-gray-200 cursor-pointer duration-150 clickable-shop">
                                <input hidden value="${item?.map}" />
                                <b class="uppercase pt-2">${item?.title ?? '-'}</b>
                                <div class="uppercase">${item?.address ?? '-'}</div>
                                <div class="uppercase">{{ __('web.stock') }}: ${item?.stock ?? 0}</div>
                                ${item.phone ? `<div class="text-[#66666] text-[0.8rem] pb-2"><a href="tel:${item.phone}" class="hover:underline">${item.phone}</a></div>                                                                                                                                                                                                                            ` : ''}
                            </div>
                            `
                        );
                    });

                    if (resp.results.length > 0) {
                        $("#stock-map").attr('src', resp.results[0]?.map ?? item?.address ?? 'Turkey');
                    }

                    if (resp.cities.length == 0) {
                        $("#stocks-city > option.removable").remove();
                    }

                    if (resp.districts.length == 0) {
                        $("#stocks-district > option.removable").remove();
                    }

                    if (resp.results.length == 0) {
                        $("#results-list").empty();
                    }


                }).fail(function(e) {
                    alert('Error occurred.');
                });
            }

            $("#results-list").on('click', '.clickable-shop', function() {
                const mapUrl = $(".clickable-shop > input").val();
                const input = $(this).children('input').first();
                $("#stock-map").attr('src', input.attr('value'));
            });
        </script>
    @endsection

    <div class="flex my-2 justify-between w-full px-[15px] lg:px-[60px]">
        <ol class="breadcrumbs" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li class="breadcrumb-item block" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="https://www.ekuralkan.com/"">
                    <span itemprop="name">{{ __('web.home') }}</span> </a>
                <meta itemprop="position" content="1" />
            </li>
            @if ($product->breadcrumbCategory)
                <li class="breadcrumb-item block" itemprop="itemListElement" itemscope
                    itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="{{ $product->breadcrumbCategory->innerUrl() }}">
                        <span
                            itemprop="name">{{ $product->breadcrumbCategory->currentTranslation->category_name }}</span>
                    </a>
                    <meta itemprop="position" content="2" />
                </li>
                <li class="breadcrumb-item block font-bold" itemprop="itemListElement" itemscope
                    itemtype="https://schema.org/ListItem">
                    <span itemprop="name">{{ $product->currentTranslation->product_name }}</span>
                    <meta itemprop="position" content="3" />
                </li>
            @else
                <li class="breadcrumb-item block" itemprop="itemListElement" itemscope
                    itemtype="https://schema.org/ListItem">
                    <span itemprop="name">{{ $product->currentTranslation->product_name }}</span>
                    <meta itemprop="position" content="2" />
                </li>
            @endif
        </ol>
    </div>

    <div class="wrapper-details px-[30px] lg:px-[60px] mb-[20px]">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-0 lg:gap-8">
            <div class="absolute" id="zoom-context"></div>
            <div class="col-span-8 mb-[16px] lg:mb-0" id="image-gallery-component"
                data-variations='@json($variationData)'>
            </div>

            <div class="col-span-4 w-full">
                <div class="w-full text-[#3c3e3e] text-[24px] font-[600]">
                    {{ $product->currentTranslation->product_name }}</div>
                <div
                    class="mt-[14px] mx-[0] mb-[0] inline-block align-top text-[#3C3E3E] text-[16px] font-normal leading-[22px]">
                    {{ __('web.stock-code') }}: {{ $product->stock_code }}</div>
                @if (count($product->displayableVariations))
                    <div class="mt-[14px] text-[24px] font-semibold leading-[22px] text-[#3c3e3e]" id="variation-price">
                        ₺{{ number_format(optional($product->displayableVariations[0])->vat_price ?? 0, 2, ',', '.') }}
                    </div>
                @endif
                {{-- 
                <div class="mt-[10px] text-[#000] text-[14px] leading-[14px]">
                    @if (app()->getLocale() === 'tr')
                        ₺{{ $lowestInstallment }} {{ __('web.installments-starting-from') }}
                    @else
                        {{ __('web.installments-starting-from') }} ₺{{ $lowestInstallment }}
                    @endif
                </div>
                --}}
                <div class="mt-[20px] estimated-delivery text-[#161616] text-[14px] font-semibold leading-[22px]">
                    {{ __('web.estimated-delivery-time') }}: <span class="text-red-500"
                        id="estimated-delivery-date"></span>
                </div>

                <div class="mt-[20px]">
                    <p>
                        {{ $product->currentTranslation->short_description }}
                    </p>
                </div>

                <div class="mt-[20px] flex flex-col gap-4">
                    <div class="leading-[22px] text-[#3C3E3E] text-[16px] font-semibold">
                        {{ __('web.colors') }}
                    </div>
                    <div class="flex flex-wrap gap-4">
                        @foreach ($product->displayableVariations as $variation)
                            @if ($variation->in_stock)
                                <div @if ($loop->first) class="color-box selected rounded-md shadow-sm" @else class="color-box rounded-md shadow-sm" @endif
                                    data-variation="{{ $variation->id }}">
                                    @if ($variation->color->color_image_url)
                                        <img src="{{ $variation->color->color_image_url }}"
                                            alt="{{ $variation->color->currentTranslation->color_name }}">
                                    @endif
                                    <span
                                        class="text-center font-bold px-2">{{ $variation->color->currentTranslation->color_name }}</span>
                                </div>
                            @else
                                <div @if ($loop->first) class="color-box selected out-of-stock rounded-md shadow-sm" @else class="color-box out-of-stock rounded-md shadow-sm" @endif
                                    data-variation="{{ $variation->id }}">
                                    @if ($variation->color->color_image_url)
                                        <img src="{{ $variation->color->color_image_url }}"
                                            alt="{{ $variation->color->currentTranslation->color_name }}">
                                    @endif
                                    <span
                                        class="text-center font-bold px-2">{{ $variation->color->currentTranslation->color_name }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="mt-[30px] flex gap-2 justify-between shop-button">
                    <button onclick="quickBuy()"
                        class="rounded-md font-bold px-[15px] addToCart flex items-center justify-center h-[50px] text-white text-[15px] w-full align-top p-0 tracking-[0] uppercase bg-[#0e60ae] border-[1px] border-[solid] hover:border-[#0E60AE] hover:text-[#0e60ae] hover:bg-transparent transition-colors">{{ __('web.add-to-cart') }}</button>
                </div>
                <div class="mt-[20px] shop-button">
                    <form id="quick-buy" method="POST" action="{{ route('quick-buy') }}">
                        @csrf
                        @if (count($product->displayableVariations))
                            <input hidden name="variation" id="variation-input"
                                value="{{ $product->displayableVariations[0]->id }}" />
                        @endif
                        <button
                            class="rounded-md font-bold h-[50px] text-[#0E60AE] text-[15px] w-full inline-block align-top p-0 tracking-[0] uppercase bg-transparent border-[1px] border-[solid] border-[#0E60AE] hover:text-white hover:bg-[#0e60ae] transition-colors">{{ __('web.quick-buy') }}</button>
                    </form>
                </div>
                <div class="mt-[20px]">
                    <button onclick="showModal('stocks-modal')"
                        class="rounded-md font-bold h-[50px] text-[#0E60AE] text-[15px] w-full inline-block align-top p-0 tracking-[0] uppercase bg-transparent border-[1px] border-[solid] border-[#0E60AE] hover:text-white hover:bg-[#0e60ae] transition-colors">
                        <span>{{ __('web.stocks-in-shops') }}</span>
                    </button>
                </div>
                <div class="mt-[40px]">
                    <div class="flex flex-col w-full">
                        @foreach ($variationData as $data)
                            @if ($loop->last)
                                <div
                                    class="flex flex-row justify-between text-md font-bold border-b-[1px] border-[#0E60AE] px-4 items-center py-4 hover:bg-gray-200 transition-colors">
                                    <div class="flex flex-row gap-2 items-center">
                                        @if ($data['color_image'])
                                            <img src="{{ $data['color_image'] }}" alt="{{ $data['color'] }}"
                                                class="h-[30px] w-[30px] rounded-full">
                                        @endif
                                        <div>{{ $data['color'] }}</div>
                                    </div>
                                    <div>{{ $data['delivery_date'] }}</div>
                                </div>
                            @else
                                <div
                                    class="flex flex-row justify-between text-md font-bold border-b-[1px] border-[#0E60AE] px-4 items-center py-4 hover:bg-gray-200 transition-colors">
                                    <div class="flex flex-row gap-2 items-center">
                                        @if ($data['color_image'])
                                            <img src="{{ $data['color_image'] }}" alt="{{ $data['color'] }}"
                                                class="h-[30px] w-[30px] rounded-full">
                                        @endif
                                        <div>{{ $data['color'] }}</div>
                                    </div>
                                    <div>{{ $data['delivery_date'] }}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @if ($installments)
                    <div class="mt-[40px] border-2 rounded-md flex flex-col p-4 bg-[#0084ff64] border-[#0e60ae] gap-2">
                        <div class="text-md font-bold text-gray-800 text-center">
                            {{ __('web.product-sales-agreement-text', ['months' => $installments->installments, 'amount' => number_format($installments->monthly_payment, 0, ',', '.')]) }}
                        </div>
                    </div>
                @endif
                @if (count($campaigns) > 0)
                    @include('home.components.campaign-box')
                @endif
            </div>
        </div>
    </div>

    <div class="px-[30px] lg:px-[60px] my-[50px] hidden lg:block" id="detail-tabs">
        <ul data-tabs class="scrollbar-hidden">
            <li><a data-tabby-default href="#description" class="info-trigger">{{ __('app.description') }}</a></li>
            <li><a href="#payment" class="info-trigger">{{ __('app.payment-options') }}</a></li>
            <li><a href="#delivery" class="info-trigger">{{ __('app.delivery-info') }}</a></li>
            <li><a href="#documents" class="info-trigger">{{ __('app.documents') }}</a></li>
            <li><a href="#faq" class="info-trigger">{{ __('app.faq') }}</a></li>
            <li><a href="#tech" class="info-trigger">{{ __('app.technical-specs') }}</a></li>
            <li><a href="#e_sales_agreement" class="info-trigger">{{ __('app.e-sales-agreement') }}</a></li>
            @if (count($product->media) > 0)
                <li><a href="#photos" class="info-trigger">{{ __('app.photo-gallery') }}</a></li>
            @endif
        </ul>

        <div id="description" class="py-5 info-box">{!! $product->currentTranslation->description !!}</div>

        <div id="payment" class="py-5 info-box">

            <div class="flex justify-start gap-4 mb-8">
                <div class="border-2 rounded-md flex flex-col p-4 bg-[#0084ff64] border-[#0e60ae] gap-2">
                    <div class="text-sm text-gray-800">{{ __('web.payment-by-money-order') }}</div>
                    <div class="text-xl font-bold text-gray-800">
                        ₺{{ number_format(optional($product->displayableVariations[0])->vat_price ?? 0, 2, ',', '.') }}
                    </div>
                </div>

                <div class="border-2 rounded-md flex flex-col p-4 bg-[#0084ff64] border-[#0e60ae] gap-2">
                    <div class="text-sm text-gray-800">{{ __('web.credit-card-single-withdrawal') }}</div>
                    <div class="text-xl font-bold text-gray-800">
                        ₺{{ number_format(optional($product->displayableVariations[0])->vat_price ?? 0, 2, ',', '.') }}
                    </div>
                </div>

                @if ($installments)
                    <div class="border-2 rounded-md flex flex-col p-4 bg-[#0084ff64] border-[#0e60ae] gap-2">
                        <div class="text-sm text-gray-800">{{ __('web.pay-with-sales-agreement') }}</div>
                        <div class="text-xl font-bold text-gray-800">
                            {{ __('web.monthly-installments-text', ['months' => $installments->installments, 'amount' => number_format($installments->monthly_payment, 2, ',', '.')]) }}
                        </div>
                    </div>
                @endif
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($banks as $bank)
                    <div class="flex flex-col">
                        <div class="flex justify-center items-center">
                            <img src="{{ URL::asset('build/images/banks/' . $bank['image']) }}" class="h-[30px]">
                        </div>
                        <table class="styled-table">
                            <thead class="border-2 text-white text-shadow"
                                style="border-color: {{ $bank['color'] }}; background-color: {{ $bank['color'] }};">
                                <tr>
                                    <th class="h-[40px]"></th>
                                    <th>{{ __('web.installment-amount') }}</th>
                                    <th>{{ __('web.total-amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center hover:!bg-gray-300 cursor-pointer transition-all">
                                    <td class="font-medium py-[10px] px-[5px] border-r-[1px]">
                                        {{ __('web.one-shot') }}
                                    </td>
                                    <td class="font-medium py-[10px] px-[5px] border-r-[1px]">
                                        ₺{{ number_format(optional($product->displayableVariations[0])->vat_price ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="font-medium py-[10px] px-[5px]">
                                        ₺{{ number_format(optional($product->displayableVariations[0])->vat_price ?? 0, 2, ',', '.') }}
                                    </td>
                                </tr>
                                @foreach ($bank['installments'] as $installment)
                                    <tr class="text-center hover:!bg-gray-300 cursor-pointer transition-all">
                                        <td class="font-medium py-[10px] px-[5px] border-r-[1px] w-[40%]">
                                            {{ $installment['months'] }} {{ __('web.installments') }}</td>
                                        <td class="font-medium py-[10px] px-[5px] border-r-[1px]">
                                            ₺{{ $installment['perOne'] }}</td>
                                        <td class="font-medium py-[10px] px-[5px]">₺{{ $installment['total'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>

        </div>

        <div id="delivery" class="py-5 info-box">{!! $product->currentTranslation->delivery_info !!}</div>
        <div id="documents" class="py-5 info-box">{!! $product->currentTranslation->documents !!}</div>
        <div id="faq" class="py-5 info-box">{!! $product->currentTranslation->faq !!}</div>
        <div id="tech" class="py-5 info-box">
            <table class="styled-table">
                <tbody>
                    @foreach ($product->orderedSpecifications as $spec)
                        <tr>
                            <td class="font-[600] p-[10px] border-r-[1px]">{{ $spec->specification }}</td>
                            <td class="p-[10px]">{{ $spec->value }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div id="e_sales_agreement" class="py-5 info-box">{!! $eSalesAgreement !!}</div>
        @if (count($product->media) > 0)
            <div id="photos" class="py-5 info-box">
                <div class="grid grid-cols-4 lg:grid-cols-8 gap-2 images-list pt-[20px]">
                    @foreach ($product->media as $media)
                        <a rel="desktop-gallery" class="relative w-full swipebox" href="{{ $media->photo_url }}">
                            <img src="{{ $media->photo_url }}" alt="Product gallery thumbnail"
                                class="min-h-[100px] h-full object-contain border-[1px] border-gray-100">
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div id="accordion" class="px-[30px] lg:px-[60px] my-[50px] block lg:hidden">
        <h3>{{ __('app.description') }}</h3>
        <div class="py-5 info-box">{!! $product->currentTranslation->description !!}</div>

        <h3>{{ __('app.payment-options') }}</h3>
        <div class="py-5 info-box">
            <div class="flex justify-between gap-4 mb-8">
                <div class="border-2 rounded-md flex flex-col p-4 bg-[#0084ff64] border-[#0e60ae] gap-2">
                    <div class="text-sm text-gray-800">{{ __('web.payment-by-money-order') }}</div>
                    <div class="text-xl font-bold text-gray-800">
                        ₺{{ number_format(optional($product->displayableVariations[0])->vat_price ?? 0, 2, ',', '.') }}
                    </div>
                </div>

                <div class="border-2 rounded-md flex flex-col p-4 bg-[#0084ff64] border-[#0e60ae] gap-2">
                    <div class="text-sm text-gray-800">{{ __('web.credit-card-single-withdrawal') }}</div>
                    <div class="text-xl font-bold text-gray-800">
                        ₺{{ number_format(optional($product->displayableVariations[0])->vat_price ?? 0, 2, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($banks as $bank)
                    <div class="flex flex-col">
                        <div class="flex justify-center items-center">
                            <img src="{{ URL::asset('build/images/banks/' . $bank['image']) }}" class="h-[40px]">
                        </div>
                        <table class="styled-table">
                            <thead class="border-2 text-white text-shadow"
                                style="border-color: {{ $bank['color'] }}; background-color: {{ $bank['color'] }};">
                                <tr>
                                    <th class="h-[40px]"></th>
                                    <th>{{ __('web.installment-amount') }}</th>
                                    <th>{{ __('web.total-amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center hover:!bg-gray-300 cursor-pointer transition-all">
                                    <td class="font-medium py-[10px] px-[5px] border-r-[1px]">
                                        {{ __('web.one-shot') }}
                                    </td>
                                    <td class="font-medium py-[10px] px-[5px] border-r-[1px]">
                                        ₺{{ number_format(optional($product->displayableVariations[0])->vat_price ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="font-medium py-[10px] px-[5px]">
                                        ₺{{ number_format(optional($product->displayableVariations[0])->vat_price ?? 0, 2, ',', '.') }}
                                    </td>
                                </tr>
                                @foreach ($bank['installments'] as $installment)
                                    <tr class="text-center hover:!bg-gray-300 cursor-pointer transition-all">
                                        <td class="font-medium py-[10px] px-[5px] border-r-[1px] w-[40%]">
                                            {{ $installment['months'] }} {{ __('web.installments') }}</td>
                                        <td class="font-medium py-[10px] px-[5px] border-r-[1px]">
                                            ₺{{ $installment['perOne'] }}</td>
                                        <td class="font-medium py-[10px] px-[5px]">₺{{ $installment['total'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>

        <h3>{{ __('app.delivery-info') }}</h3>
        <div class="py-5 info-box">{!! $product->currentTranslation->delivery_info !!}</div>

        <h3>{{ __('app.documents') }}</h3>
        <div class="py-5 info-box">{!! $product->currentTranslation->documents !!}</div>

        <h3>{{ __('app.faq') }}</h3>
        <div class="py-5 info-box">{!! $product->currentTranslation->faq !!}</div>

        <h3>{{ __('app.technical-specs') }}</h3>
        <div class="py-5 info-box">
            <table class="styled-table">
                <tbody>
                    @foreach ($product->orderedSpecifications as $spec)
                        <tr>
                            <td class="font-[600] p-[10px] border-r-[1px]">{{ $spec->specification }}</td>
                            <td class="p-[10px]">{{ $spec->value }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h3>{{ __('app.e-sales-agreement') }}</h3>
        <div class="py-5 info-box">{!! $eSalesAgreement !!}</div>

        @if (count($product->media) > 0)
            <h3>{{ __('app.photo-gallery') }}</h3>
            <div class="py-5 info-box">
                <div class="grid grid-cols-4 lg:grid-cols-8 gap-2 images-list pt-[20px]">
                    @foreach ($product->media as $media)
                        <div class="col-auto">
                            <a rel="mobile-gallery" class="relative w-full swipebox" href="{{ $media->photo_url }}">
                                <img src="{{ $media->photo_url }}" alt="Product gallery thumbnail"
                                    class="min-h-[100px] h-auto object-contain border-[1px] border-gray-100">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>


    <x-bladewind.modal name="stocks-modal" title="{{ __('web.stocks-in-shops') }}" size="xl"
        cancel_button_label="" ok_button_label="{{ __('web.close') }}">
        <input hidden id="product-id" value="{{ $product->id }}" />
        <div class="flex w-full gap-4">
            <div class="flex flex-col flex-[0.5]">
                <label for="stocks-color" class="block my-2 text-sm">{{ __('web.color') }}</label>
                <select id="stocks-color"
                    class="stock-trigger border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">{{ __('web.select') }}</option>
                    @foreach ($product->displayableVariations as $variation)
                        <option value="{{ $variation->color->id }}">
                            {{ $variation->color->currentTranslation->color_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col flex-[0.5]">
                <label for="stocks-country" class="block my-2 text-sm">{{ __('web.country') }}</label>
                <select id="stocks-country"
                    class="stock-trigger border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">{{ __('web.select') }}</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->currentTranslation->country_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex w-full gap-4">
            <div class="flex-col flex-[0.5] hidden" id="city-container">
                <label for="stocks-city" class="block my-2 text-sm">{{ __('web.city') }}</label>
                <select id="stocks-city"
                    class="stock-trigger border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">{{ __('web.select') }}</option>
                </select>
            </div>
            <div class="flex-col flex-[0.5] hidden" id="district-container">
                <label for="stocks-district" class="block my-2 text-sm">{{ __('web.district') }}</label>
                <select id="stocks-district"
                    class="stock-trigger border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">{{ __('web.select') }}</option>
                </select>
            </div>
        </div>

        <div class="flex-col lg:flex-row w-full gap-2 mt-4 hidden" id="results-container">
            <div class="flex flex-col max-h-[150px] lg:max-h-none lg:h-[300px] flex-[0.5] overflow-x-scroll"
                id="results-list">
            </div>
            <div class="flex flex-col max-h-[150px] lg:max-h-none lg:h-[300px] flex-[0.5]">
                <iframe id="stock-map"
                    src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDS7I86-ZIHwqlylNOKQfftkprdym6Uuss&q=turkey"
                    width="100%" height="300" style="border: 0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </x-bladewind.modal>
</x-app-layout>
