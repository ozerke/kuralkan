<x-app-layout>
    @section('title')
        {{ __('web.my-payment-plan') }}
    @endsection
    <div class="flex flex-col p-5 lg:p-10 text-gray-900 gap-5">
        @include('customer.menu')
        <div class="bg-[#f2f2f2] rounded-md p-4 flex flex-col shadow-sm">
            <h1 class="uppercase font-bold text-xl text-center lg:text-left mb-5">
                {{ __('web.my-payment-plan') }}</h1>
            @if ($bonds)
                @if ($ebonds)
                    <div class="w-full mb-8 overflow-hidden rounded-lg shadow-lg">
                        <div class="w-full overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr
                                        class="text-md font-semibold tracking-wide text-left text-white bg-blue-600 uppercase border-b">
                                        <th class="px-4 py-3">{{ __('web.bond-no') }}</th>
                                        <th class="px-4 py-3">{{ __('web.date') }}</th>
                                        <th class="px-4 py-3">{{ __('web.payment-amount') }}</th>
                                        <th class="px-4 py-3">{{ __('web.remaining-amount') }}</th>
                                        <th class="px-4 py-3">{{ __('web.paid') }}</th>
                                        <th class="px-4 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @foreach ($bonds as $bondPayment)
                                        <tr class="text-gray-700">
                                            <td class="px-4 py-3 border">
                                                {{ $bondPayment['e_bond_no'] }}<br />{{ $bondPayment['bond_description'] }}
                                            </td>
                                            <td class="px-4 py-3 border">{{ $bondPayment['due_date']->format('d-m-Y') }}
                                            </td>
                                            <td class="px-4 py-3 border">
                                                {{ number_format($bondPayment['bond_amount'], 2, ',', '.') }} TL</td>
                                            <td class="px-4 py-3 border">
                                                {{ number_format($bondPayment['remaining_amount'], 2, ',', '.') }} TL
                                            </td>
                                            <td class="px-4 py-3 border"><span
                                                    class="font-bold {{ !$bondPayment->isPaid() ? 'text-red-400' : 'text-green-500' }}">{{ !$bondPayment->isPaid() ? __('app.no') : __('app.yes') }}</span>
                                            </td>
                                            <td class="px-4 py-3 border">
                                                <div class="flex flex-col gap-2">
                                                    @if (!$bondPayment->isPaid())
                                                        <a href="{{ route('sales-agreements.bond-payment-page', ['orderNo' => $bondPayment->order->order_no, 'bond_no' => $bondPayment['e_bond_no']]) }}"
                                                            class="bg-green-500 font-bold hover:bg-green-600 rounded-md text-white text-center p-2">
                                                            <i class="fa-solid fa-money-bill"></i>
                                                            {{ __('web.pay-now') }}
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('sales-agreements.bond-payments-list', ['orderNo' => $bondPayment->order->order_no, 'bond_no' => $bondPayment['e_bond_no']]) }}"
                                                        class="bg-blue-500 font-bold hover:bg-blue-600 rounded-md text-white text-center p-2">
                                                        <i class="fa-solid fa-list"></i> {{ __('web.show-payments') }}
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full">
                        @foreach ($bonds as $bond)
                            <x-bladewind.card reduce_padding="true"
                                class="hover:bg-gray-200 transition-colors hover:shadow-md">
                                <div class="flex flex-col gap-3">
                                    <div class="grow">
                                        <p class="text-left">
                                            <span class="font-bold">{{ __('web.name-surname') }}:</span>
                                            {{ $bond['fullname'] }}
                                        </p>
                                        <p class="text-left">
                                            <span class="font-bold">{{ __('web.bond-no') }}:</span>
                                            {{ $bond['bondNo'] }}
                                        </p>
                                        <p class="text-left">
                                            <span class="font-bold">{{ __('web.due-date') }}:</span>
                                            {{ $bond['dueDate'] }}
                                        </p>
                                        <p class="text-left">
                                            <span class="font-bold">{{ __('web.bond-amount') }}:</span>
                                            {{ $bond['bondAmount'] }}
                                        </p>
                                        <p class="text-left">
                                            <span class="font-bold">{{ __('web.bank-name') }}:</span>
                                            {{ $bond['bankName'] }}
                                        </p>
                                        <p class="text-left">
                                            <span class="font-bold">{{ __('web.bank-branch-name') }}:</span>
                                            {{ $bond['bankBranchName'] }}
                                        </p>
                                        <p class="text-left">
                                            <span class="font-bold">{{ __('web.paid') }}:</span> <span
                                                class="font-bold {{ $bond['paid'] === 'N' ? 'text-red-400' : 'text-green-500' }}">{{ $bond['paid'] === 'Y' ? __('app.yes') : __('app.no') }}</span>
                                        </p>
                                    </div>
                                </div>
                            </x-bladewind.card>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="pb-3">
                    <x-bladewind.alert type="info" shade="faint" show_icon="true" show_close_icon="false"
                        class="font-bold">
                        {{ __('web.no-bonds-found') }}
                    </x-bladewind.alert>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
