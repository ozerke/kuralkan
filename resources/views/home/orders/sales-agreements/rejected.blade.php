<x-app-layout>
    @section('title')
        {{ __('web.payment') }}
    @endsection
    <div class="px-[20px] lg:px-[110px] bg-[#F2F2F2] pt-[20px] w-full">

        <div class="grid grid-cols-1 lg:grid-cols-10 gap-8 py-[40px] w-full">
            <div class="col-span-1 lg:col-span-7 flex flex-col gap-8 order-2">
                <div class="bg-white px-10 py-5 flex flex-col rounded-md shadow-sm gap-6">
                    <h1 class="uppercase font-bold text-xl text-center lg:text-left">
                        {{ __('web.sales-agreement-application') }} >
                        {{ __('web.rejected') }}
                    </h1>
                    <div class="rounded-md bg-red-200 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-700">
                                    {{ __('web.your-application-was-rejected') }}
                                </h3>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-span-1 lg:col-span-3 flex flex-col gap-6 order-1 lg:order-2">
                <h3 class="font-bold text-2xl">{{ __('web.order-summary') }}</h3>
                <div class="flex flex-col bg-white p-4 gap-2 rounded-md shadow-md">
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
                        <p class="font-bold w-1/2">{{ __('web.estimated-delivery-date') }}</p>
                        <p class="font-bold text-[#0E60AE] text-right">
                            {{ $order->productVariation->getEstimatedDeliveryDate() }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold w-1/2">{{ __('web.delivery-place') }}</p>
                        <p class="font-bold text-[#0E60AE] text-right">{{ $order->getDeliveryInformation() }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold w-1/2">{{ __('web.order-amount') }}</p>
                        <p class="font-bold text-[#0E60AE] text-right">
                            ₺{{ number_format($order->total_amount, 2, ',', '.') }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold w-1/2">{{ __('web.order-no') }}</p>
                        <p class="font-bold text-[#0E60AE] text-right">{{ $order->order_no }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold w-1/2">{{ __('web.order-date') }}</p>
                        <p class="font-bold text-[#0E60AE] text-right">{{ $order->created_at->format('d-m-Y') }}
                        </p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold w-1/2">{{ __('web.down-payment') }}</p>
                        <p class="font-bold text-[#0E60AE] text-right">{{ $salesAgreement->down_payment_amount }}
                            TL
                        </p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold w-1/2">{{ __('web.installment-amount') }}</p>
                        <p class="font-bold text-[#0E60AE] text-right">{{ $salesAgreement->monthly_payment }}
                            TL/{{ __('web.month') }}
                        </p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-bold w-1/2">{{ __('web.installments') }}</p>
                        <p class="font-bold text-[#0E60AE] text-right">
                            {{ $salesAgreement->number_of_installments }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
