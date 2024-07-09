<x-app-layout>
    @section('title')
        {{ __('web.findeks-is-initiating') }}
    @endsection
    <div class="flex flex-col p-5 lg:p-10 text-gray-900 gap-5">

        <x-bladewind.alert shade="faint" show_close_icon="false" class="py-5">
            <p class="font-bold">{!! __('web.findeks-initiating-description') !!}</p>
        </x-bladewind.alert>

        <input hidden id="order_no" value="{{ $order->order_no }}" />

        <ul role="list" class="bg-[#f2f2f2] p-4 rounded-md">
            <li
                class="flex flex-col lg:flex-row justify-between gap-10 py-5 border-[1px] border-gray-300 rounded-md p-4 bg-white lg:hover:bg-gray-300 transition-colors group">
                <div class="flex gap-x-4 lg:items-center flex-col lg:flex-row">
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
                <div class="flex flex-col lg:flex-row justify-between gap-5">
                    <div class="flex flex-row items-center justify-center gap-2">
                        <x-bladewind.spinner size="medium" />
                        <p class="text-md leading-6 text-gray-900 font-bold" id="stage">
                            {{ $stage }}
                        </p>
                    </div>
                </div>
            </li>
        </ul>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @section('js')
        <script>
            const orderNo = $("#order_no").val();

            let checkStatusInterval;

            function checkStatus() {
                fetch("/sales-agreement/check-processing-status", {
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
                        if (res.stage) {
                            $("#stage").html(res.stage);
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
                checkStatusInterval = setInterval(checkStatus, 1500);
            });
        </script>
    @endsection

</x-app-layout>
