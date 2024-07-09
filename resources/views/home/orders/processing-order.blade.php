@php
    $labels = json_encode([
        'from' => __('web.from'),
        'to' => __('web.to'),
    ]);
@endphp

<x-app-layout>
    @section('title')
        {{ __('web.order-loading-info') }}
    @endsection
    <div class="flex flex-col p-5 lg:p-10 text-gray-900 gap-5">

        <x-bladewind.alert shade="faint" show_close_icon="false" class="py-5">
            <p class="mb-4 font-bold">{!! __('web.order-processing-description') !!}</p>
            <a href="{{ route('panel') }}"
                class="font-bold rounded-md bg-blue-500 text-white px-2 py-2">{{ __('web.go-to-my-orders') }}</a>
        </x-bladewind.alert>

        <input hidden id="order_no" value="{{ $order->order_no }}" />

        <ul role="list" class="bg-[#f2f2f2] p-4 rounded-md">
            <li
                class="flex flex-col lg:flex-row justify-between gap-10 py-5 border-[1px] border-gray-300 rounded-md p-4 bg-white lg:hover:bg-gray-300 transition-colors group">
                <div class="flex gap-x-4 lg:items-center flex-col lg:flex-row flex-[0.35]">
                    <img class="h-auto w-full lg:w-24 flex-none rounded-md bg-gray-50"
                        src="{{ $order->productVariation->firstMedia->photo_url ?? URL::asset('build/images/kuralkanlogo-white.png') }}"
                        alt="Product image">
                    <div class="flex flex-col gap-2">
                        <p class="text-lg font-bold leading-6 text-gray-900">
                            {{ $order->productVariation->product->currentTranslation->product_name }}</p>
                        <p class="text-md font-bold leading-5 text-gray-900 flex items-center gap-2">
                            @if ($order->productVariation->color->color_image_url)
                                <img src="{{ $order->productVariation->color->color_image_url }}"
                                    class="inline-block rounded-full elevation-1 h-[20px] w-[20px]"
                                    alt="Color image item" height="40" width="40">
                            @endif
                            <span>{{ $order->productVariation->color->currentTranslation->color_name }}</span>
                        </p>
                        <p class="text-md leading-5 text-gray-900">
                            <span class="font-bold">{{ __('web.order-no') }}:</span>
                            {{ $order->order_no }}
                        </p>
                        <p class="text-md leading-5 text-gray-900">
                            <span class="font-bold">{{ __('web.delivery-point') }}:</span>
                            {{ $order->deliveryUser->address }}
                        </p>
                        @if ($order->delivery_date)
                            <p class="text-md leading-6 text-gray-900"><span
                                    class="font-bold">{{ __('web.delivery-date') }}:</span>
                                <span
                                    class="bg-blue-500 text-white font-bold p-1 rounded-md text-nowrap">{{ $order->delivery_date }}</span>
                            </p>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col lg:flex-row flex-[0.65] justify-between gap-5">
                    <div class="flex flex-col items-start lg:items-start justify-center gap-2">
                        <p class="text-md leading-6 text-gray-900"><span
                                class="font-bold">{{ __('web.order-date') }}:</span>
                            {{ $order->created_at->format('d-m-Y H:i') }}</p>
                        <p class="text-md leading-6 text-gray-900"><span
                                class="font-bold">{{ __('web.order-status') }}:</span>
                            @if ($order->erp_order_id)
                                <span
                                    class="bg-blue-500 text-white font-bold p-1 rounded-md text-nowrap">{{ optional($order->latestStatusHistory)->orderStatus->currentTranslation->status ?? '-' }}</span>
                            @else
                                <span
                                    class="bg-gray-500 text-white font-bold p-1 rounded-md text-nowrap">{{ __('web.pending') }}</span>
                            @endif
                        </p>
                        @if ($order->erp_order_id)
                            <p class="text-md leading-6 text-gray-900"><span
                                    class="font-bold">{{ __('web.payment-type') }}:</span>
                                {{ $order->getOrderPaymentType(true) }}</p>
                        @endif
                        <p class="text-md leading-6 text-gray-900"><span
                                class="font-bold">{{ $order->isSalesAgreementOrder() ? __('web.down-payment') : __('web.order-amount') }}:</span>
                            ₺{{ number_format($order->getOrderPaymentsState(true)['total_amount'], 2, ',', '.') }}
                        </p>
                        @if (!$order->getOrderPaymentsState(true)['is_paid'] && $order->erp_order_id)
                            <p class="text-md leading-6 text-gray-900"><span
                                    class="font-bold">{{ __('web.remaining-amount') }}:</span>
                                ₺{{ number_format($order->getOrderPaymentsState(true)['remaining_amount'], 2, ',', '.') }}
                            </p>
                        @endif
                    </div>

                    @if ($order->erp_order_id)
                        <div class="flex flex-col lg:flex-row items-center justify-center gap-2">
                            @if (!$order->getOrderPaymentsState(true)['is_paid'] && !$order->isCancelled())
                                <a href="{{ route('redirect-to-payment', ['orderNo' => $order->order_no]) }}"
                                    class="text-white font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors w-full lg:w-auto text-center">
                                    <i class="fa-solid fa-money-bill"></i> {{ __('web.pay-now') }}
                                </a>
                            @endif
                            <a href="{{ route('customer.order-details', ['orderNo' => $order->order_no]) }}"
                                class="text-white font-bold bg-blue-500 rounded-md py-2 px-4 hover:bg-blue-600 transition-colors w-full lg:w-auto text-center">
                                <i class="fa-solid fa-eye"></i> {{ __('web.order-details') }}
                            </a>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center gap-2 px-10">
                            <x-bladewind.spinner size="big" />
                            <div class="font-bold text-md">
                                {{ __('web.order-loading-info') }}
                            </div>
                        </div>
                    @endif
                </div>
            </li>
        </ul>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @section('js')
        <script>
            $(".card-col").on('click', function() {
                const currentStatus = $("#status").val();
                const status = $(this).attr('data-status');

                if (status === currentStatus) {
                    $("#status").val('');
                    $("#status").change();
                } else {
                    $("#status").val(status);
                    $("#status").change();
                }
            });

            $("#status").on('change', function() {
                $("#filter-form").submit();
            })

            const orderNo = $("#order_no").val();

            let checkStatusInterval;

            function checkStatus() {
                fetch("/check-order-processing", {
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
                        if (res.terminated) {
                            clearInterval(checkStatusInterval);
                            window.location.replace("/panel");
                        }

                        if (res.redirectTo) {
                            clearInterval(checkStatusInterval);
                            window.location.replace(res.redirectTo);
                        }
                    })
                    .catch(function(res) {
                        clearInterval(checkStatusInterval);
                        window.location.replace("/panel");
                    })
            }

            $(function() {
                checkStatusInterval = setInterval(checkStatus, 2500);
            });
        </script>
    @endsection

</x-app-layout>
