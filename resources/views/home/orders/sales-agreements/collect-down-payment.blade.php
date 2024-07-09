@php
    $translations = [
        'name' => __('web.credit-card-holder-name'),
        'number' => __('web.card-number'),
        'expiry' => __('web.expiry-date'),
        'cvc' => __('web.cvc'),
        'namePlaceholder' => __('web.your-name-here'),
        'validPlaceholder' => __('web.valid-thru'),
        'oneShot' => __('web.one-shot'),
        'installmentAmount' => __('web.installment-amount'),
        'totalAmount' => __('web.total-amount'),
        'installments' => __('web.installments'),
    ];
@endphp
<x-app-layout>
    @section('title')
        {{ __('web.down-payment') }}
    @endsection
    <div class="px-[20px] lg:px-[110px] bg-[#F2F2F2] pt-[20px] w-full">
        <form method="POST" action="{{ route('process-payment', ['orderNo' => $order->order_no]) }}">
            @method('POST')
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-10 gap-8 py-[40px] w-full">
                <div class="col-span-1 lg:col-span-7 flex flex-col gap-8 order-2">
                    <div class="bg-white px-10 py-5 flex flex-col rounded-md shadow-sm gap-6">
                        <h1 class="uppercase font-bold text-xl text-center lg:text-left">
                            {{ __('web.sales-agreement-application') }} >
                            {{ __('web.down-payment') }}
                        </h1>
                        <p>
                            {!! __('web.collect-payment-explanation', ['fullname' => $order->invoiceUser->full_name]) !!}
                        </p>
                    </div>
                    @if ($paidState['paid_amount'] > 0)
                        <div
                            class="w-full flex flex-col bg-blue-600 px-6 py-4 text-white rounded-md hover:bg-blue-700 hover:shadow-lg transition-colors">
                            <p class="text-md"><span class="font-bold">{{ __('web.paid-amount') }}:</span>
                                ₺{{ number_format($paidState['paid_amount'], 2, ',', '.') }}</p>
                            <p class="text-lg"><span class="font-bold">{{ __('web.remaining-amount') }}:</span>
                                <span
                                    class="font-bold">₺{{ number_format($paidState['remaining_amount'], 2, ',', '.') }}</span>
                            </p>
                            <p class="text-lg"><span class="font-bold">{{ __('web.partial-payment-limit') }}:</span>
                                <span class="font-bold">{{ $paidState['payment_count'] }}</span>
                            </p>
                        </div>
                    @endif
                    <div class="flex flex-col gap-4 lg:gap-2">
                        <h4 class="font-bold text-lg">{{ __('web.payment-amount') }}</h4>
                        <div class="flex flex-col lg:flex-row gap-4 lg:gap-8 min-h-[50px] lg:items-center">
                            <x-bladewind.radio-button checked="true" color="blue"
                                label="₺{{ number_format($paidState['remaining_amount'], 2, ',', '.') }}"
                                name="payment_amount" value="full" labelCss="!mb-0 !text-[18px] font-bold" />
                            <div class="flex flex-col lg:flex-row gap-2">
                                <x-bladewind.radio-button color="blue" label="{{ __('web.partial-amount') }}"
                                    name="payment_amount" value="partial"
                                    labelCss="!mb-0 w-full !text-[18px] font-bold" />
                                <x-bladewind.input type="number" prefix_icon_div_css="!z-[1]" disabled="true"
                                    addClearing="false" id="custom_amount" name="custom_amount"
                                    placeholder="{{ number_format($paidState['remaining_amount'], 2, ',', '.') }}"
                                    prefix="₺"
                                    class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm disabled:bg-gray-200 transition-colors text-black" />
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4">
                        <h4 class="font-bold text-lg">{{ __('web.payment-type') }}</h4>
                        <div class="flex flex-col gap-8">
                            <div class="flex flex-col gap-4">
                                <x-bladewind.radio-button checked="true" color="blue"
                                    label="{{ __('web.credit-card') }}" name="payment_type" value="credit-card"
                                    labelCss="!mb-0 !text-[18px] font-bold" />

                                <div id="credit-card-option" class="flex flex-col bg-white rounded-md shadow-md p-4">
                                    <input hidden id="order-remaining-amount"
                                        value="{{ $paidState['remaining_amount'] }}" />
                                    <div id="credit-card-component" data-translations='@json($translations)'>
                                    </div>
                                </div>

                            </div>

                            <div class="flex flex-col gap-4">
                                <x-bladewind.radio-button color="blue" label="{{ __('web.bank-transfer') }}"
                                    name="payment_type" value="bank-transfer" labelCss="!mb-0 !text-[18px] font-bold" />

                                <div id="bank-list-option"
                                    class="flex-col bg-white rounded-md shadow-md p-4 gap-4 hidden">
                                    <h5 class="font-bold text-md">{{ __('web.select-the-bank-you-will-pay-to') }}:
                                    </h5>
                                    <div class="flex flex-col">

                                        <div class="flex flex-col gap-4">
                                            @foreach ($bankAccounts as $bankAccount)
                                                <div
                                                    class="flex flex-row justify-start gap-4 p-4 rounded-md bank-account bank-default">
                                                    <x-bladewind.radio-button color="blue" name="selected_bank"
                                                        value="{{ $bankAccount->id }}"
                                                        labelCss="!mb-0 !text-[18px] font-bold !mr-0" />
                                                    <div class="flex flex-col lg:flex-row justify-start gap-4 lg:gap-8">
                                                        <div class="flex lg:justify-center items-center">
                                                            <img src="{{ URL::asset('build/images/banks/main/' . $bankAccount->bank->logo) }}"
                                                                class="h-[40px] w-[100px] object-contain">
                                                        </div>
                                                        <div class="flex flex-col gap-1">
                                                            <span
                                                                class="text-md font-bold">{{ $bankAccount->account_name }}</span>
                                                            <span
                                                                class="text-sm font-bold">{{ $bankAccount->bank->bank_name }}
                                                                - {{ $bankAccount->currency->currency_code }} -
                                                                {{ $bankAccount->branch_name }} -
                                                                {{ __('web.branch-code') }}:
                                                                {{ $bankAccount->branch_code }}</span>
                                                            <span class="text-sm">{{ __('web.account-no') }}:
                                                                {{ $bankAccount->account_no }}</span>
                                                            <div class="flex gap-2">
                                                                <span class="text-sm">{{ __('web.iban') }}:
                                                                    {{ $bankAccount->iban }}</span>
                                                                <button
                                                                    onclick="copyToClipboard('{{ $bankAccount->iban }}', '{{ __('web.copied-to-clipboard') }}')"
                                                                    type="button" class="copy-button"><img
                                                                        src="{{ URL::asset('build/images/icons/copy.png') }}"
                                                                        class="h-[18px] w-auto"></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col lg:flex-row gap-6 lg:justify-between border-gray-300 border-t-2 py-4">
                        <div class="flex flex-col items-start gap-4">
                            <div class="flex items-center gap-2">
                                <x-bladewind.checkbox add_clearing="false" color="blue" id="tos" labelCss="mr-0"
                                    required="true" />
                                <span>{{ __('web.admit') }}</span> <span
                                    class="font-bold hover:text-blue-500 cursor-pointer"
                                    onclick="showModal('tos-modal')">{{ __('web.terms-and-conditions') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-bladewind.checkbox add_clearing="false" color="blue" id="tos"
                                    labelCss="mr-0" required="true" />
                                <span>{{ __('web.admit') }}</span> <span
                                    class="font-bold hover:text-blue-500 cursor-pointer"
                                    onclick="showModal('remote-sales-modal')">{{ __('web.remote-sales-agreement') }}</span>
                            </div>
                        </div>

                        <button
                            class="rounded-md disabled:bg-gray-500 disabled:cursor-not-allowed hover:text-white p-4 inline-block align-top tracking-[0] uppercase hover:bg-black border-[1px] border-[solid] border-[#0E60AE] text-white bg-[#0e60ae] transition-colors trigger-disable">
                            <p class="text-md font-bold">{{ __('web.continue') }}</p>
                        </button>
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
                            <p class="font-bold text-[#0E60AE] text-right">{{ $order->getDeliveryInformation() }}
                            </p>
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
                            <p class="font-bold text-[#0E60AE] text-right">
                                {{ $order->created_at->format('d-m-Y') }}
                            </p>
                        </div>
                        <div class="flex justify-between">
                            <p class="font-bold w-1/2">{{ __('web.down-payment') }}</p>
                            <p class="font-bold text-[#0E60AE] text-right">
                                {{ $salesAgreement->down_payment_amount }}
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
        <x-bladewind.modal name="tos-modal" title="{{ __('web.terms-and-conditions') }}" size="omg"
            ok_button_label="{{ __('web.close') }}" cancel_button_label="" body_css="max-h-[80vh] overflow-scroll">
            <p class="p-2">
                @include('utility.tos', [
                    'dateTime' => $order->created_at->format('d-m-Y H:i:s'),
                    'tosAddress' => $order->invoiceUser->address,
                    'tosDeliveryAddress' => $order->deliveryUser->address,
                    'tosEmail' => $order->invoiceUser->email,
                    'tosFullname' => $order->invoiceUser->full_name,
                    'tosPhone' => $order->invoiceUser->phone,
                    'tosProductName' => $order->productVariation->getDocumentTitle(),
                    'tosPrice' => '₺' . number_format($order->total_amount, 2, ',', '.'),
                ])
            </p>
        </x-bladewind.modal>
        <x-bladewind.modal name="remote-sales-modal" title="{{ __('web.remote-sales-agreement') }}" size="omg"
            ok_button_label="{{ __('web.close') }}" cancel_button_label="" body_css="max-h-[80vh] overflow-scroll">
            <p class="p-2">
                @include('utility.remote-agreement', [
                    'dateTime' => $order->created_at->format('d-m-Y H:i:s'),
                    'tosAddress' => $order->invoiceUser->address,
                    'tosDeliveryAddress' => $order->deliveryUser->address,
                    'tosEmail' => $order->invoiceUser->email,
                    'tosFullname' => $order->invoiceUser->full_name,
                    'tosPhone' => $order->invoiceUser->phone,
                    'tosProductName' => $order->productVariation->getDocumentTitle(),
                    'tosPrice' => '₺' . number_format($order->total_amount, 2, ',', '.'),
                ])
            </p>
        </x-bladewind.modal>
    </div>
    @section('js')
        <script>
            $("input[name='payment_amount']").on('change', function() {
                if ($(this).val() === 'full') {
                    $("#custom_amount").attr('disabled', true);
                } else {
                    $("#custom_amount").attr('disabled', false);
                }
            });

            $("input[name='payment_type']").on('change', function() {
                if ($(this).val() === 'credit-card') {
                    $("#credit-card-option").removeClass('hidden');
                    $("#bank-list-option").addClass('hidden');
                } else {
                    $("#credit-card-option").addClass('hidden');
                    $("#bank-list-option").removeClass('hidden');
                    $("#bank-list-option").addClass('flex');

                }
            });

            $("input[name='selected_bank']").on('change', function() {
                $(".bank-account").removeClass('bank-selected');
                $(".bank-account").addClass('bank-default');

                $(this).parents('div').first().addClass('bank-selected').removeClass('bank-default');
            });

            async function copyToClipboard(value, message) {
                await navigator.clipboard.writeText(value);
                alert(message);
            }

            const paymentAmountEvent = new CustomEvent("payment_amount_changed", {
                bubbles: true,
            });

            $("input[name='payment_amount']").on('change', function() {
                document.dispatchEvent(paymentAmountEvent);
            })

            $("#custom_amount").on('blur', function() {
                if ($(this).val()) document.dispatchEvent(paymentAmountEvent);
            });

            $(':input[type=number]').on('wheel', function(e) {
                $(this).blur();
            });

            $("form").on('submit', function() {
                $('.trigger-disable').attr('disabled', true);
                if (showLoader) showLoader();
            })
        </script>
    @endsection
</x-app-layout>
