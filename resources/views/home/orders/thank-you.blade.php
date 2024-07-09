<x-app-layout>
    @section('title')
        {{ __('web.order-summary') }}
    @endsection
    <div class="px-[20px] lg:px-[110px] bg-[#F2F2F2] pt-[100px] w-full">
        <div class="grid grid-cols-1 lg:grid-cols-10 gap-8 py-[40px] w-full">
            <div class="col-span-1 lg:col-span-7 flex flex-col gap-6">
                <h3 class="font-bold text-2xl">{{ __('web.thank-you') }}!</h3>
                <div class="flex flex-col bg-white gap-4 px-6 py-4">
                    <p class="text-2xl font-bold">{{ __('web.your-order-has-been-received') }}</p>
                    @if ($order->hasBankTransferPayments())
                        <p class="text-xl">{{ __('web.please-complete-bank-transfers') }}</p>
                    @endif
                    <p class="text-xl">{{ __('web.you-may-track-your-order-from') }} <a class="font-bold text-blue-500"
                            href="{{ route('panel') }}">{{ __('web.my-account-page') }}</a></p>
                </div>
            </div>
            <div class="col-span-1 lg:col-span-3 flex flex-col gap-6">
                <h3 class="font-bold text-2xl">{{ __('web.order-summary') }}</h3>
                <div class="flex flex-col bg-white border-[1px] p-4 gap-2">
                    <div class="flex flex-col lg:flex-row gap-4 py-5">
                        @if ($order->productVariation->firstMedia)
                            <div class="flex justify-center">
                                <img class="max-w-full max-h-[100px] object-contain"
                                    src="{{ $order->productVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}">
                            </div>
                        @endif
                        <div class="flex flex-col justify-center items-center lg:items-start">
                            <p class="font-bold">
                                {{ $order->productVariation->product->currentTranslation->product_name }}</p>
                            <div class="flex justify-center items-center gap-2">
                                @if ($order->productVariation->color->color_image_url)
                                    <img src="{{ $order->productVariation->color->color_image_url }}"
                                        class="inline-block rounded-full elevation-1 h-[20px] w-[20px]"
                                        alt="Color image item" height="40" width="40">
                                @endif
                                <b>{{ $order->productVariation->color->currentTranslation->color_name }}</b>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold">{{ __('web.estimated-delivery-date') }}</p>
                        <p class="font-bold text-[#0E60AE]">{{ $order->productVariation->getEstimatedDeliveryDate() }}
                        </p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold">{{ __('web.order-amount') }}</p>
                        <p class="font-bold text-[#0E60AE]">₺{{ number_format($order->total_amount, 2, ',', '.') }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold">{{ __('web.order-no') }}</p>
                        <p class="font-bold text-[#0E60AE]">{{ $order->order_no }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold">{{ __('web.order-date') }}</p>
                        <p class="font-bold text-[#0E60AE]">{{ $order->created_at->format('d-m-Y') }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold">{{ __('web.payment-type') }}</p>
                        <p class="font-bold text-[#0E60AE]">
                            {{ optional($order->latest_payment)->getPaymentTypeTranslation() ?? '-' }}
                        </p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold">{{ __('web.order-status') }}</p>
                        <p class="font-bold text-[#0E60AE]">
                            {{ $order->latest_status->orderStatus->currentTranslation->status ?? __('web.pending') }}
                        </p>
                    </div>
                    <a href="{{ route('panel') }}"
                        class="text-center rounded-md disabled:bg-gray-500 disabled:cursor-not-allowed hover:text-white p-4 inline-block align-top tracking-[0] uppercase hover:bg-black border-[1px] border-[solid] border-[#0E60AE] text-white bg-[#0e60ae] transition-colors">
                        <p class="text-md font-bold">{{ __('web.my-account') }}</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
