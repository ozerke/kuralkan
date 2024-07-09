<x-app-layout>
    @section('title')
        {{ !empty($category->currentTranslation->seo_title) ? $category->currentTranslation->seo_title : $category->currentTranslation->category_name }}
    @endsection

    @push('header-tags')
        <meta name="description" content="{{ $category->currentTranslation->seo_description }}" />
        <meta name="keywords" content="{{ $category->currentTranslation->seo_keywords }}" />
    @endpush

    <div class="flex my-2 justify-between w-full px-[15px] lg:px-[60px]">
        <ol class="breadcrumbs" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li class="breadcrumb-item block" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="https://www.ekuralkan.com/"">
                    <span itemprop="name">{{ __('web.home') }}</span> </a>
                <meta itemprop="position" content="1" />
            </li>
            <li class="breadcrumb-item block font-bold" itemprop="itemListElement" itemscope
                itemtype="https://schema.org/ListItem">
                <span itemprop="name">{{ $category->currentTranslation->category_name }}</span>
                <meta itemprop="position" content="2" />
            </li>
        </ol>
    </div>
    <section class="flex mb-0 pt-0 pb-0 bg-white w-full">
        <div class="w-full px-[0px] lg:px-[60px] flex flex-col">
            <div class="my-[25px] lg:my-[20px] section-header">
                <span class="bold">
                    <span class="heading-text">{{ $category->currentTranslation->category_name }}</span>
                </span>
            </div>

            <div class="mb-[25px] lg:mb-[50px] w-full px-[20px] lg:px-[0px]">
                <form method="GET" id="sort-form">
                    <label for="sort" class="block my-2 text-sm">{{ __('web.price-sort') }}</label>
                    <select id="sort" name="sort"
                        class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-[200px] p-2.5">
                        <option value="" @if (!request()->sort) selected @endif>
                            {{ __('web.default') }}
                        </option>
                        <option value="asc" @if (request()->sort == 'asc') selected @endif>
                            {{ __('web.low-to-high') }}</option>
                        <option value="desc" @if (request()->sort == 'desc') selected @endif>
                            {{ __('web.high-to-low') }}</option>
                    </select>
                </form>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($category->getProducts(request()->sort) as $product)
                    <div class="swiper-slide category-item">
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

            @if ($category->currentTranslation->description)
                <div class="flex flex-col mb-[25px] lg:mb-[50px] w-full px-[20px] lg:px-[0px]">
                    {!! $category->currentTranslation->description !!}
                </div>
            @endif
        </div>
    </section>

    @section('js')
        <script>
            $("#sort").on('change', function() {
                $("#sort-form").submit();
            });
        </script>
    @endsection
</x-app-layout>
