@php
    $user = auth()->user();
    $activeStyle = 'bg-blue-500 text-white hover:bg-blue-600';
    $inactiveStyle = 'bg-gray-500 text-white hover:bg-gray-600';

    $ordersStyle = request()->routeIs('customer.orders') ? $activeStyle : $inactiveStyle;
    $paymentsStyle = request()->routeIs('customer.payment-plan') ? $activeStyle : $inactiveStyle;
    $profileStyle = request()->routeIs('customer.profile') ? $activeStyle : $inactiveStyle;
    $supportStyle = request()->routeIs('customer.support') ? $activeStyle : $inactiveStyle;

@endphp
<div class="flex flex-col bg-[#f2f2f2] rounded-md p-4 gap-5">
    <div class="w-full flex flex-col lg:flex-row flex-wrap justify-between items-center gap-4">
        <div class="text-2xl font-bold">
            {{ __('web.hello') }}, {{ $user->full_name }}
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="text-white font-bold bg-red-500 rounded-md py-2 px-4 cursor-pointer hover:bg-red-600 transition-colors">
                <i class="fa-solid fa-power-off"></i> {{ __('web.logout') }}
            </button>
        </form>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 w-full">
        <a href="{{ route('customer.orders') }}"
            class="flex items-center justify-center border-2 px-2 py-3 gap-3 rounded-md transition-colors text-xl {{ $ordersStyle }}">
            <i class="fa-solid fa-cart-shopping"></i>
            <div class="font-bold">{{ __('web.my-orders') }}</div>
        </a>
        <a href="{{ route('customer.payment-plan') }}"
            class="flex items-center justify-center border-2 px-2 py-3 gap-3 rounded-md transition-colors text-xl {{ $paymentsStyle }}">
            <i class="fa-solid fa-money-bill"></i>
            <div class="font-bold">{{ __('web.my-payment-plan') }}</div>
        </a>
        <a href="{{ route('customer.profile') }}"
            class="flex items-center justify-center border-2 px-2 py-3 gap-3 rounded-md transition-colors text-xl  {{ $profileStyle }}">
            <i class="fa-solid fa-user-pen"></i>
            <div class="font-bold">{{ __('web.my-profile') }}</div>
        </a>
        <a href="#"
            class="flex items-center justify-center border-2 px-2 py-3 gap-3 rounded-md transition-colors text-xl  {{ $supportStyle }}">
            <i class="fa-solid fa-headset"></i>
            <div class="font-bold">{{ __('web.support') }}</div>
        </a>
    </div>
</div>
