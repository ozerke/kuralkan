@php
    $labels = json_encode([
        'from' => __('web.from'),
        'to' => __('web.to'),
    ]);
@endphp

<x-app-layout>
    @section('title')
        {{ __('web.my-orders') }}
    @endsection
    <div class="flex flex-col p-5 lg:p-10 text-gray-900 gap-5">
        @include('customer.menu')

        <form id="filter-form"method="get" class="flex flex-col gap-4">
            <div class="flex flex-col lg:flex-row bg-[#f2f2f2] rounded-md p-4 gap-5">
                <div class="flex flex-col justify-center items-center flex-[0.5] gap-4">
                    <h1 class="font-bold text-2xl">{{ __('web.filter-by-date') }}</h1>
                    <div id="date-picker" data-lang="{{ app()->getLocale() }}" data-translations="{{ $labels }}">
                    </div>
                    <button type="submit"
                        class="text-white font-bold bg-blue-500 rounded-md py-2 px-4 cursor-pointer hover:bg-blue-600 transition-colors">
                        <i class="fa-solid fa-magnifying-glass"></i> {{ __('web.filter') }}
                    </button>
                </div>
                <input hidden name="status" value="{{ request()->status }}" id="status" />
                <div class="grid grid-cols-1 gap-4 flex-[0.5]">
                    <div class="card-col" data-status="1">
                        <div
                            class="flex rounded p-4 shadow border-b-4 border-gray-500 items-center gap-5 hover:bg-gray-500 hover:text-white transition-colors cursor-pointer @if (request()->status == 1) bg-gray-500 text-white @else bg-white @endif">
                            <div class="font-bold text-3xl bg-gray-500 px-4 py-1 rounded-md text-white">
                                {{ number_format($awaiting) }}
                            </div>
                            <div class="text-md font-semibold uppercase">
                                {{ $translations['awaiting'] }}
                            </div>
                        </div>
                    </div>
                    <div class="card-col" data-status="2">
                        <div
                            class="flex rounded p-4 shadow border-b-4 border-blue-500 items-center gap-5 hover:bg-blue-500 hover:text-white transition-colors cursor-pointer @if (request()->status == 2) bg-blue-500 text-white @else bg-white @endif">
                            <div class="font-bold text-3xl bg-blue-500 px-4 py-1 rounded-md text-white">
                                {{ number_format($confirmed) }}
                            </div>
                            <div class="text-md font-semibold uppercase">
                                {{ $translations['confirmed'] }}
                            </div>
                        </div>
                    </div>
                    <div class="card-col" data-status="3">
                        <div
                            class="flex rounded p-4 shadow border-b-4 border-yellow-500 items-center gap-5 hover:bg-yellow-500 hover:text-white transition-colors cursor-pointer @if (request()->status == 3) bg-yellow-500 text-white @else bg-white @endif">
                            <div class="font-bold text-3xl bg-yellow-500  px-4 py-1 rounded-md text-white">
                                {{ number_format($supplying) }}
                            </div>
                            <div class="text-md font-semibold uppercase">
                                {{ $translations['supplying'] }}
                            </div>
                        </div>
                    </div>
                    <div class="card-col" data-status="4">
                        <div
                            class="flex rounded p-4 shadow border-b-4 border-green-500 items-center gap-5 hover:bg-green-500 hover:text-white transition-colors cursor-pointer @if (request()->status == 4) bg-green-500 text-white @else bg-white @endif">
                            <div class="font-bold text-3xl bg-green-500 px-4 py-1 rounded-md text-white">
                                {{ number_format($servicePoint) }}
                            </div>
                            <div class="text-md font-semibold uppercase">
                                {{ $translations['servicePoint'] }}
                            </div>
                        </div>
                    </div>
                    <div class="card-col" data-status="5">
                        <div
                            class="flex rounded p-4 shadow border-b-4 border-red-500 items-center gap-5 hover:bg-red-500 hover:text-white transition-colors cursor-pointer @if (request()->status == 5) bg-red-500 text-white @else bg-white @endif">
                            <div class="font-bold text-3xl bg-red-500 px-4 py-1 rounded-md text-white">
                                {{ number_format($delivered) }}
                            </div>
                            <div class="text-md font-semibold uppercase">
                                {{ $translations['delivered'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

        <ul role="list" class="bg-[#f2f2f2] p-4 rounded-md">
            @foreach ($orders as $order)
                <li
                    class="flex flex-col lg:flex-row justify-between gap-10 py-5 border-[1px] border-gray-300 rounded-md p-4 bg-white lg:hover:bg-gray-300 transition-colors group mb-4">
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
                                @if ($order->erp_order_id || $order->isCancelled())
                                    <span
                                        class="bg-blue-500 text-white font-bold p-1 rounded-md text-nowrap">{{ optional($order->latestStatusHistory)->orderStatus->currentTranslation->status ?? '-' }}</span>
                                @else
                                    <span
                                        class="bg-gray-500 text-white font-bold p-1 rounded-md text-nowrap">{{ __('web.pending') }}</span>
                                @endif
                            </p>
                            @if ($order->erp_order_id || !$order->isSalesAgreementOrder())
                                <p class="text-md leading-6 text-gray-900"><span
                                        class="font-bold">{{ __('web.payment-type') }}:</span>
                                    {{ $order->getOrderPaymentType(true) }}</p>
                            @endif
                            <p class="text-md leading-6 text-gray-900"><span
                                    class="font-bold">{{ $order->isSalesAgreementOrder() ? __('web.down-payment') : __('web.order-amount') }}:</span>
                                ₺{{ number_format($order->getOrderPaymentsState(true)['total_amount'], 2, ',', '.') }}
                            </p>
                            @if (!$order->getOrderPaymentsState(true)['is_paid'] || !$order->isSalesAgreementOrder())
                                <p class="text-md leading-6 text-gray-900"><span
                                        class="font-bold">{{ __('web.remaining-amount') }}:</span>
                                    ₺{{ number_format($order->getOrderPaymentsState(true)['remaining_amount'], 2, ',', '.') }}
                                </p>
                            @endif
                            @if ($order->isSalesAgreementOrder())
                                <p class="text-md leading-6 text-gray-900"><span
                                        class="font-bold">{{ __('web.application-status') }}:</span>
                                    <span
                                        class="@if ($order->sa_status['color']) {{ $order->sa_status['color'] }} @endif text-white font-bold p-1 rounded-md text-nowrap">{{ $order->sa_status['text'] }}
                                        @if ($order->salesAgreement && $order->salesAgreement->is_sms_pending)
                                            ({{ __('web.sms-pending') }})
                                        @endif
                                    </span>
                                </p>
                            @endif
                        </div>

                        @if ($order->isCancelled())
                            @if ($order->erp_request_error)
                                <p class="flex justify-center items-center text-md leading-6 text-gray-900"><span
                                        class="font-bold bg-red-500 text-white rounded-md p-2">{{ __('app.erp-system-error') }}</span>
                                </p>
                            @endif
                        @else
                            @if ($order->erp_order_id || !$order->isSalesAgreementOrder())
                                <div class="flex flex-col lg:flex-row items-center justify-center gap-2">
                                    @if (!$order->getOrderPaymentsState(true)['is_paid'] && !$order->isCancelled())
                                        <a href="{{ route('redirect-to-payment', ['orderNo' => $order->order_no]) }}"
                                            class="text-white font-bold bg-green-500 rounded-md py-2 px-4 hover:bg-green-600 transition-colors w-full lg:w-auto text-center trigger-disable">
                                            <i class="fa-solid fa-money-bill"></i> {{ __('web.pay-now') }}
                                        </a>
                                    @endif
                                    <a href="{{ route('customer.order-details', ['orderNo' => $order->order_no]) }}"
                                        class="text-white font-bold bg-blue-500 rounded-md py-2 px-4 hover:bg-blue-600 transition-colors w-full lg:w-auto text-center trigger-disable">
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
                        @endif

                    </div>
                </li>
            @endforeach
        </ul>
        {{ $orders->onEachSide(1)->links('utility.paginator-links') }}
    </div>

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

            $(".trigger-disable").on('click', function() {
                if (showLoader) showLoader();
            })
        </script>
    @endsection

</x-app-layout>
