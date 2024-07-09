<x-app-layout>
    @section('title')
        {{ __('web.bond-payments-list') }}
    @endsection
    <div class="flex flex-col py-10 px-10 bg-gray-200 gap-5">
        <div class="flex w-full items-center justify-start">
            @if (auth()->user()->isShopOrService())
                <a href="{{ route('shop.order-details', ['orderNo' => $order->order_no]) }}"
                    class="flex justify-center items-center bg-blue-500 p-2 rounded-md text-white">{{ __('web.go-back') }}</a>
            @else
                <a href="{{ route('customer.order-details', ['orderNo' => $order->order_no]) }}"
                    class="flex justify-center items-center bg-blue-500 p-2 rounded-md text-white">{{ __('web.go-back') }}</a>
            @endif
        </div>
        <div class="w-full flex flex-col lg:flex-row bg-white p-5 justify-between items-center gap-5">
            <div class="flex flex-row lg:flex-col gap-2">
                <div class="text-gray-500">{{ __('web.order-number') }}</div>
                <div class="font-bold">{{ $order->order_no }}</div>
            </div>
            <div class="flex flex-row gap-5 items-center">
                <div class="font-bold">{{ __('app.order-date') }}: {{ $order->created_at->format('d-m-Y H:i') }}</div>
                <div class="flex justify-center items-center bg-blue-500 p-2 rounded-md">
                    <span
                        class="font-bold text-white">{{ optional($order->latestStatusHistory)->orderStatus->currentTranslation->status ?? __('web.pending') }}</span>
                </div>
            </div>
        </div>

        <div class="w-full flex flex-col bg-white p-5 justify-between items-center gap-5">
            <div class="border-b-2 border-black w-full">
                <span class="text-lg font-bold">{{ __('web.my-payments-for-bond') }}:
                    {{ $bondPaymentState['e_bond_no'] }}</span>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full">
                @foreach ($bondPaymentState['payments'] as $payment)
                    <x-bladewind.card reduce_padding="true">
                        <div class="flex flex-col gap-3">
                            @if ($payment->bankAccount)
                                <div class="flex items-center w-full justify-center">
                                    <img class="h-[40px] w-auto" style="max-width: 200px;object-fit:contain;"
                                        src="{{ $payment->getBankLogo() }}" />
                                </div>
                            @endif
                            <div class="grow">
                                <p class="text-left">
                                    <span class="font-bold">{{ __('web.payment-type') }}:</span>
                                    {{ $payment->getPaymentTypeTranslation() }}
                                </p>
                                <p class="text-left">
                                    <span class="font-bold">{{ __('web.date') }}:</span>
                                    {{ $payment->created_at->format('d-m-Y H:i') }}
                                </p>
                                <p class="text-left">
                                    <span class="font-bold">{{ __('web.payment-amount') }}:</span>
                                    {{ number_format($payment->payment_amount, 2, ',', '.') }}
                                    TL
                                </p>
                                <p class="text-left">
                                    <span class="font-bold">{{ __('web.installments') }}:</span>
                                    {{ $payment->number_of_installments }}
                                </p>
                                <p class="text-left">
                                    <span class="font-bold">{{ __('web.approved') }}:</span> <span
                                        class="font-bold {{ $payment->approved_by_erp === 'N' ? 'text-red-400' : 'text-green-500' }}">{{ $payment->approved_by_erp === 'Y' ? __('app.yes') : __('app.no') }}</span>
                                </p>
                                @if ($payment->failed)
                                    <p class="text-left">
                                        <span class="font-bold">{{ __('app.status') }}:</span> <span
                                            class="font-bold text-red-400">{{ __('app.failed-payment') }}</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </x-bladewind.card>
                @endforeach
            </div>
        </div>
    </div>

</x-app-layout>
