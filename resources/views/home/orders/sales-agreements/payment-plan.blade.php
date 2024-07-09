@php
    $user = $order->invoiceUser;

    $confirmMessage = [
        'text' => __('web.confirm-text'),
        'downPayment' => __('web.down-payment'),
        'paymentAmount' => __('web.payment-amount'),
        'installments' => __('web.installments'),
    ];
@endphp
<x-app-layout>
    @section('title')
        {{ __('web.sales-agreement-application') }}
    @endsection
    <div class="px-[20px] lg:px-[110px] bg-[#F2F2F2] pt-[20px] w-full">
        <div class="grid grid-cols-1 lg:grid-cols-10 gap-8 py-[40px] w-full">
            <div class="col-span-1 lg:col-span-7 flex flex-col gap-8 order-2">
                <div class="bg-white px-10 py-5 flex flex-col rounded-md shadow-sm">
                    <form id="payment-plan-form" method="get"
                        action="{{ route('sales-agreements.payment-plan', ['orderNo' => $order->order_no]) }}">
                        <h1 class="uppercase font-bold text-xl text-center lg:text-left">
                            {{ __('web.sales-agreement-application') }} >
                            {{ __('web.choose-your-payment-plan') }}
                        </h1>

                        <div class="my-[12px]">
                            {!! $explanation !!}
                        </div>

                        <label for="down_payment_amount"
                            class="block my-2 text-sm">{{ __('web.select-down-payment-amount') }}</label>
                        <select name="down_payment_amount" id="down_payment_amount"
                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option selected>{{ __('web.select-an-amount') }}</option>
                            @foreach ($downPayments as $payment)
                                <option value="{{ $payment['amount'] }}"
                                    @if (request()->down_payment_amount == $payment['amount']) selected @endif>{{ $payment['amount'] }} TL
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @if (!$user->isFindeksVerified())
                    <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex flex-col gap-4">
                                <p class="text-md font-bold text-yellow-700">
                                    {{ __('web.findeks-unverified-message') }}
                                </p>
                                <div class="flex gap-4 flex-col lg:flex-row">
                                    <a class="bg-yellow-600 px-4 py-2 rounded-md hover:bg-yellow-500 transition-colors text-white font-bold text-center"
                                        href="https://findeks.com" target="_blank">{{ __('web.go-to-findeks') }}</a>
                                    <a class="bg-yellow-600 px-4 py-2 rounded-md hover:bg-yellow-500 transition-colors text-white font-bold text-center"
                                        href="{{ route('sales-agreements.check-findeks-verification', ['orderNo' => $order->order_no]) }}">{{ __('web.check-findeks-verification') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($installments)
                    <div class="bg-white p-0 flex flex-col rounded-md shadow-sm overflow-x-auto">
                        <form id="installment-form" method="post"
                            action="{{ route('sales-agreements.select-plan', ['orderNo' => $order->order_no]) }}">
                            @method('POST')
                            @csrf
                            <input hidden name="selected-installments" id="selected-installments" />
                            <input hidden name="selected-down-payment" id="selected-down-payment"
                                value="{{ request()->down_payment_amount }}" />

                            <table class="w-full divide-y divide-gray-300">
                                <thead class="bg-brand">
                                    <tr>
                                        <th scope="col"
                                            class="p-3 text-center text-sm lg:text-lg font-semibold text-white">
                                            {{ __('web.payment-amount') }}</th>
                                        <th scope="col"
                                            class="p-3 text-center text-sm lg:text-lg font-semibold text-white">
                                            {{ __('web.installments') }}</th>
                                        <th scope="col"
                                            class="p-3 text-center text-sm lg:text-lg font-semibold text-white">
                                            {{ __('web.action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($installments as $installment)
                                        <tr class="hover:bg-gray-200 transition-colors">
                                            <td
                                                class="whitespace-nowrap p-3 text-sm lg:text-lg lg:text-md font-medium text-black text-center">
                                                {{ number_format($installment['monthly_payment'], 0, '', '.') }} TL
                                            </td>
                                            <td
                                                class="whitespace-nowrap p-3 text-sm lg:text-lg lg:text-md text-black text-center">
                                                {{ $installment['installments'] }}</td>
                                            <td
                                                class="whitespace-nowrap p-3 text-sm lg:text-lg lg:text-md text-black flex justify-center text-center">
                                                @if ($user->isFindeksVerified())
                                                    <button data-installments="{{ $installment['installments'] }}"
                                                        data-paymentamount="{{ $installment['monthly_payment'] }}"
                                                        type="button"
                                                        class="selection-btn rounded-md disabled:bg-gray-500 disabled:cursor-not-allowed hover:text-white px-4 py-2 tracking-[0] uppercase hover:bg-black border-[1px] border-[solid] border-[#0E60AE] text-white bg-[#0e60ae] transition-colors trigger-disable">
                                                        <p class="text-sm lg:text-md font-bold">{{ __('web.select') }}
                                                        </p>
                                                    </button>
                                                @else
                                                    <button type="button" disabled
                                                        class="rounded-md disabled:bg-gray-500 disabled:cursor-not-allowed hover:text-white px-4 py-2 tracking-[0] uppercase hover:bg-black border-[1px] border-[solid] border-[#0E60AE] text-white bg-[#0e60ae] transition-colors trigger-disable">
                                                        <p class="text-sm lg:text-md font-bold">{{ __('web.select') }}
                                                        </p>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>
                @endif
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
                </div>
            </div>
        </div>

    </div>
    @section('js')
        <script>
            $("#down_payment_amount").on('change', function() {
                $("#payment-plan-form").submit();
                if (showLoader) showLoader();
            });

            const confirmText = @json($confirmMessage);

            $(".selection-btn").on('click', function() {
                const installments = $(this).attr('data-installments');
                const paymentAmount = $(this).attr('data-paymentamount');
                $("#selected-installments").val(installments);

                const downPayment = $("#selected-down-payment").val();

                const confirmMessage =
                    `${confirmText.text}\n\n${confirmText.downPayment}: ${downPayment} TL\n${confirmText.paymentAmount}: ${paymentAmount} TL\n${confirmText.installments}: ${installments}`;

                if (confirm(confirmMessage)) {
                    $("#installment-form").submit();
                    if (showLoader) showLoader();
                }
            });

            $("form").on('submit', function() {
                $('.trigger-disable').attr('disabled', true);
                if (showLoader) showLoader();
            })
        </script>
    @endsection
</x-app-layout>
