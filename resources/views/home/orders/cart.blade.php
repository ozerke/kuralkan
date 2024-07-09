<x-app-layout>
    @section('title')
        {{ __('web.your-cart') }}
    @endsection
    <div class="px-[40px] lg:px-[120px] bg-[#F2F2F2] pt-[150px] lg:pt-[100px]">
        @if ($item)
            <h1 class="uppercase font-bold text-2xl mt-4 text-center lg:text-left">{{ __('web.your-cart') }}</h1>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 py-[40px]">
                <div class="col-span-1 lg:col-span-8 bg-white p-10 rounded-md shadow-sm">
                    <div class="flex flex-col lg:flex-row gap-4 justify-between px-10 py-5 border-[1px] rounded-md">
                        @if ($item->firstMedia)
                            <div class="flex justify-center">
                                <img class="max-w-full max-h-[100px] object-contain"
                                    src="{{ $item->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}">
                            </div>
                        @endif
                        <div class="flex flex-col justify-center items-center lg:items-start text-center lg:text-left">
                            <p class="font-bold">{{ $item->product->currentTranslation->product_name }}</p>
                            <p>{{ __('web.product_id') }}: <b>{{ $item->id }}</b></p>
                            <p>{{ __('web.stock_code') }}: <b>{{ $item->product->stock_code }}</b></p>
                            <div class="flex justify-center items-center gap-2"><span>{{ __('web.color') }}:</span>
                                @if ($item->color->color_image_url)
                                    <img src="{{ $item->color->color_image_url }}"
                                        class="inline-block rounded-full elevation-1 h-[20px] w-[20px]"
                                        alt="Color image item" height="40" width="40">
                                @endif <b>{{ $item->color->currentTranslation->color_name }}</b>
                            </div>
                            <p>{{ __('web.estimated-delivery-date') }}:
                                <b>{{ $item->getEstimatedDeliveryDate(isset($consignedProduct)) }}</b>
                            </p>
                            @if (isset($consignedProduct) && $consignedProduct->chasis_no)
                                <p>{{ __('web.chasis-no') }}: <b>{{ $consignedProduct->chasis_no }}</b>
                                </p>
                            @endif
                        </div>
                        <div class="flex justify-center items-center">
                            <p class="text-[#0E60AE] text-[18px] leading-[24px] font-semibold">
                                ₺{{ number_format($item->vat_price, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="flex justify-end mt-4">
                        <form method="GET" action="{{ route('clear-cart') }}">
                            <button
                                class="font-bold rounded-md h-[50px] hover:text-white text-[15px] inline-block align-top px-4 tracking-[0] uppercase hover:bg-red-400 border-[1px] border-[solid] border-red-500 text-white bg-red-500 transition-colors">{{ __('web.clear-cart') }}</button>
                        </form>
                    </div>
                </div>
                <div class="col-span-1 lg:col-span-4 flex flex-col gap-6 justify-center">
                    <div class="flex flex-col bg-white shadow-sm p-4 rounded-md">
                        <div class="flex justify-between">
                            <p>{{ __('web.order-amount') }}</p>
                            <p>₺{{ number_format($item->vat_price, 2, ',', '.') }}</p>
                        </div>
                        <div class="flex justify-between">
                            <p class="font-bold">{{ __('web.basket-total') }}</p>
                            <p class="font-bold text-[#0E60AE]">₺{{ number_format($item->vat_price, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('invoice-information') }}"
                        class="font-bold rounded-md text-center leading-[50px] h-[50px] hover:text-white text-[15px] w-full inline-block align-top p-0 tracking-[0] uppercase hover:bg-black border-[1px] border-[solid] border-[#0E60AE] text-white bg-[#0e60ae] transition-colors">{{ __('web.complete-order') }}</a>
                </div>
            </div>
        @else
            <h1 class="uppercase font-bold text-2xl mt-4 text-center">{{ __('web.your-cart') }}</h1>
            <div class="flex py-10 w-full justify-center items-center">
                <div class="flex flex-col bg-white border-[1px] p-4 w-auto gap-8">
                    <div class="flex justify-between">
                        <p class="font-bold">{{ __('web.cart-is-empty') }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p>{{ __('web.your-added-items-appear-here') }}</p>
                    </div>
                    <a href="{{ route('home') }}"
                        class="font-bold rounded-md flex items-center justify-center cursor-pointer h-[50px] hover:text-white text-[15px] w-full align-top p-0 tracking-[0] uppercase hover:bg-black border-[1px] border-[solid] border-[#0E60AE] text-white bg-[#0e60ae] transition-colors">{{ __('web.go-to-shop') }}</a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
