<x-app-layout>
    @section('title')
        {{ __('web.payment-plans') }}
    @endsection
    <div class="flex flex-col p-5 lg:p-10 text-gray-900 gap-5">
        @include('shop.menu')

        <form id="plan-form" method="GET" action="{{ route('shop.payment-plans') }}">
            @csrf
            <div class="bg-[#f2f2f2] rounded-md p-4 flex flex-col lg:flex-row shadow-sm gap-6">
                <div class="flex flex-col flex-[0.5] gap-3">
                    <div>
                        <label for="product"
                            class="block mb-2 text-md font-bold">{{ __('web.select-a-product') }}</label>
                        <select name="product" id="product"
                            class="border border-gray-300 text-gray-900 text-md rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option selected value="">{{ __('web.select-a-product') }}</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @if (request()->product == $product->id) selected @endif>
                                    {{ $product->currentTranslation->product_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if ($downPayments)
                        <div>
                            <label for="down_payment"
                                class="block mb-2 text-md font-bold">{{ __('web.select-down-payment-amount') }}</label>
                            <select name="down_payment" id="down_payment"
                                class="border border-gray-300 text-gray-900 text-md rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option selected>{{ __('web.select-an-amount') }}</option>
                                @foreach ($downPayments as $payment)
                                    <option value="{{ $payment->id }}"
                                        @if (request()->down_payment == $payment->id) selected @endif>
                                        {{ $payment->amount }} TL
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                @if ($installments)
                    <div class="bg-white p-0 flex flex-col rounded-md shadow-sm overflow-x-auto flex-[0.5]">
                        <table class="w-full divide-y divide-gray-300">
                            <thead class="bg-brand">
                                <tr>
                                    <th scope="col"
                                        class="p-3 text-center text-sm lg:text-lg font-semibold text-white">
                                        {{ __('web.payment-amount') }}</th>
                                    <th scope="col"
                                        class="p-3 text-center text-sm lg:text-lg font-semibold text-white">
                                        {{ __('web.installments') }}</th>
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </form>
    </div>

    @section('js')
        <script>
            $("select").on('change', function() {
                $("#plan-form").submit();
                if (showLoader) showLoader();
            });
        </script>
    @endsection
</x-app-layout>
