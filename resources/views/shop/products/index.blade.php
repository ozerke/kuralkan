<x-app-layout>
    @section('title')
        {{ __('web.consigned-products') }}
    @endsection
    <div class="flex flex-col p-5 lg:p-10 text-gray-900 gap-5">
        @include('shop.menu')

        <form id="search-form"method="get" class="flex flex-col gap-4">
            <div class="bg-[#f2f2f2] rounded-md p-4 w-full flex flex-row gap-4">
                <div class="w-full">
                    <label for="search" class="sr-only">Search</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="search" name="search" value="{{ request()->search }}"
                            class="block w-full rounded-md border-0 text-lg bg-white py-1.5 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 font-bold"
                            placeholder="{{ __('web.search') }}" type="search">
                    </div>
                </div>
                <button type="submit"
                    class="text-white font-bold bg-blue-500 rounded-md px-4 cursor-pointer hover:bg-blue-600 transition-colors h-auto">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 bg-[#f2f2f2] p-4 rounded-md">
            @foreach ($products as $product)
                <x-bladewind.card
                    class="bg-white lg:hover:bg-gray-300 transition-colors group mb-4 p-4 rounded-md border-[1px] border-gray-300">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center justify-center flex-row flex-[0.4]">
                            <img class="h-auto w-full lg:w-24 flex-none rounded-md bg-gray-50"
                                src="{{ $product->productVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}"
                                alt="Product image">
                        </div>
                        <div class="flex flex-col flex-[0.6] justify-center gap-5">
                            <div class="flex flex-col gap-2 text-center">
                                <p class="text-lg font-bold leading-6 text-gray-900">
                                    {{ $product->productVariation->product->currentTranslation->product_name }}</p>
                                <p
                                    class="text-md font-bold leading-5 text-gray-900 flex items-center gap-2 text-center justify-center">
                                    @if ($product->productVariation->color->color_image_url)
                                        <img src="{{ $product->productVariation->color->color_image_url }}"
                                            class="inline-block rounded-full elevation-1 h-[20px] w-[20px]"
                                            alt="Color image item" height="40" width="40">
                                    @endif
                                    <span>{{ $product->productVariation->color->currentTranslation->color_name }}</span>
                                </p>
                            </div>
                            <div class="flex flex-col lg:flex-row items-center justify-center gap-2">
                                <p class="text-md leading-5 text-gray-900">
                                    <span class="font-bold">{{ __('web.chasis-no') }}:</span>
                                    {{ $product->chasis_no }}
                                </p>
                            </div>
                            @if ($product->in_stock)
                                <div class="flex flex-col lg:flex-row items-center justify-center gap-2">
                                    <a href="{{ route('shop.consigned-products.buy', ['consignedOrderId' => $product->id]) }}"
                                        class="text-white font-bold bg-blue-500 rounded-md py-2 px-4 hover:bg-blue-600 transition-colors w-full lg:w-auto text-center trigger-disable">
                                        <i class="fa-solid fa-shopping-cart"></i> {{ __('web.add-to-cart') }}
                                    </a>
                                </div>
                            @else
                                <div class="flex flex-col lg:flex-row items-center justify-center gap-2">
                                    <button href="#" disabled
                                        class="text-white font-bold bg-gray-500 rounded-md py-2 px-4 hover:bg-gray-600 transition-colors w-full lg:w-auto text-center disabled:cursor-not-allowed">
                                        <i class="fa-solid fa-shopping-cart"></i> {{ __('web.add-to-cart') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </x-bladewind.card>
            @endforeach
        </div>
        {{ $products->onEachSide(1)->links('utility.paginator-links') }}
    </div>

    @section('js')
    @endsection
</x-app-layout>
