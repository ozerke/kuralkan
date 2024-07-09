<x-app-layout>
    @section('title')
        {{ __('web.payment') }}
    @endsection
    <div class="px-[20px] lg:px-[110px] bg-[#F2F2F2] pt-[20px] w-full">
        <form method="POST" action="{{ route('sales-agreements.check-findeks-pin', ['orderNo' => $order->order_no]) }}">
            @method('POST')
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-10 gap-8 py-[40px] w-full">
                <div class="col-span-1 lg:col-span-7 flex flex-col gap-8 order-2">
                    <div class="bg-white px-10 py-5 flex flex-col rounded-md shadow-sm gap-6">
                        <h1 class="uppercase font-bold text-xl text-center lg:text-left">
                            {{ __('web.sales-agreement-application') }} >
                            {{ __('web.findeks-sms-approval') }}
                        </h1>

                        <div class="rounded-md bg-blue-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor"
                                        aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1 flex items-center">
                                    <p class="text-md text-blue-700 font-bold">
                                        {{ __('web.findeks-sms-notification') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex-col lg:flex-row justify-between gap-5 mb-5">
                            <div class="w-full">
                                <x-input-label for="verification_code" :value="__('web.verification-code')" />
                                <x-text-input id="verification_code" class="block mt-1 w-full" type="text"
                                    name="verification_code" required />
                                <x-input-error :messages="$errors->get('verification_code')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex flex-col lg:flex-row gap-6 lg:justify-end border-gray-300 lg:items-center">
                            <button disabled id="submit-btn"
                                class="rounded-md disabled:bg-gray-500 disabled:cursor-not-allowed hover:text-white p-4 inline-block align-top tracking-[0] uppercase hover:bg-black border-[1px] border-[solid] border-[#0E60AE] text-white bg-[#0e60ae] transition-colors trigger-disable">
                                <p class="text-md font-bold">{{ __('web.continue') }}</p>
                            </button>
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
        </form>
    </div>
    @section('js')
        <script>
            $(window).scroll(function(event) {
                $('input, textarea').blur();
            });

            $("#verification_code").on('change', function() {
                const code = $(this).val();
                if (code && code.length > 0) {
                    $("#submit-btn").attr('disabled', false);
                } else {
                    $("#submit-btn").attr('disabled', true);
                }
            });

            $("form").on('submit', function() {
                $('.trigger-disable').attr('disabled', true);
                if (showLoader) showLoader();
            })
        </script>
    @endsection
</x-app-layout>
