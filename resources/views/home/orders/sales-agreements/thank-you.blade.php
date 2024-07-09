<x-app-layout>
    @section('title')
        {{ __('web.thank-you') }}
    @endsection
    <div class="px-[20px] lg:px-[110px] bg-[#F2F2F2] pt-[20px] w-full">

        <input hidden id="order_no" value="{{ $order->order_no }}" />

        <div class="grid grid-cols-1 lg:grid-cols-10 gap-8 py-[40px] w-full">
            <div class="col-span-1 lg:col-span-7 flex flex-col gap-8 order-2">
                <h3 class="font-bold text-2xl">{{ __('web.thank-you') }}!</h3>
                <div class="flex flex-col bg-white gap-4 px-6 py-4">
                    <p class="text-2xl font-bold">{{ __('web.your-order-has-been-received') }}</p>
                    @if ($order->hasBankTransferPayments())
                        <p class="text-xl">{{ __('web.please-complete-bank-transfers') }}</p>
                    @endif

                    <div class="rounded-md bg-blue-200 p-8">
                        <div class="flex items-center">
                            <div class="flex flex-col gap-4">
                                <div class="ml-3 flex-1 md:flex md:justify-between">
                                    <p class="text-lg font-bold text-blue-700 text-center">
                                        {{ __('web.sales-agreement-thank-you-text') }}</p>
                                </div>
                                <div class="w-full flex items-center justify-center">
                                    @if (!empty($order->salesAgreement->agreement_document_link))
                                        <a href="{{ $order->salesAgreement->agreement_document_link }}" target="_blank"
                                            class="text-white font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors">
                                            <i class="fa-solid fa-download"></i> {{ __('web.sales-agreement') }}
                                        </a>
                                    @else
                                        <a id="doc-btn"
                                            class="text-white font-bold bg-red-500 rounded-md py-2 px-4 cursor-not-allowed">
                                            <span id="doc-loader"><x-bladewind.spinner size="small" class="mr-2" />
                                            </span>
                                            {{ __('web.sales-agreement') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-xl">{{ __('web.you-may-track-your-order-from') }} <a class="font-bold text-blue-500"
                            href="{{ route('panel') }}">{{ __('web.my-account-page') }}</a></p>
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

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @section('js')
        <script>
            const orderNo = $("#order_no").val();

            let checkStatusInterval;

            function checkStatus() {
                fetch("/sales-agreement/check-document", {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        method: "POST",
                        body: JSON.stringify({
                            orderNo
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.documentUrl) {
                            $("#doc-btn").attr("href", res.documentUrl).attr("target", "_blank").removeClass(
                                "bg-red-500 cursor-not-allowed").addClass("bg-green-500 hover:bg-green-600").html(
                                '<i class="fa-solid fa-download"></i> ' + res.salesAgreementText);
                            $("#doc-loader").remove();
                            clearInterval(checkStatusInterval);
                        }

                    })
                    .catch(function(res) {
                        $("#doc-loader").remove();
                        clearInterval(checkStatusInterval);
                    })
            }

            $(function() {
                checkStatusInterval = setInterval(checkStatus, 1000);
            });
        </script>
    @endsection
</x-app-layout>
