@php
    $user = auth()->user();
    $activeStyle = 'bg-blue-500 text-white hover:bg-blue-600';
    $inactiveStyle = 'bg-gray-500 text-white hover:bg-gray-600';

    $ordersStyle = request()->routeIs('shop.orders') ? $activeStyle : $inactiveStyle;
    $stocksStyle = request()->routeIs('shop.consigned-products') ? $activeStyle : $inactiveStyle;
    $paymentPlansStyle = request()->routeIs('shop.payment-plans') ? $activeStyle : $inactiveStyle;
    $subusersStyle = request()->routeIs('shop.subusers') ? $activeStyle : $inactiveStyle;
    $settingsStyle = request()->routeIs('shop.settings') ? $activeStyle : $inactiveStyle;
@endphp
<div class="flex flex-col bg-[#f2f2f2] rounded-md p-4 gap-5">
    <div class="w-full flex flex-col lg:flex-row flex-wrap justify-between items-center gap-4">
        <div class="text-2xl font-bold">
            {{ __('web.hello') }}, Kuralkan {{ $user->erp_user_id }} - {{ $user->full_name }}
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="text-white font-bold bg-red-500 rounded-md py-2 px-4 cursor-pointer hover:bg-red-600 transition-colors">
                <i class="fa-solid fa-power-off"></i> {{ __('web.logout') }}
            </button>
        </form>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 w-full">
        <a href="{{ route('shop.orders') }}"
            class="flex items-center justify-center border-2 px-2 py-3 gap-3 rounded-md transition-colors text-xl {{ $ordersStyle }}">
            <i class="fa-solid fa-cart-shopping"></i>
            <div class="font-bold">{{ __('web.my-orders') }}</div>
        </a>
        <a href="{{ config('app.consigned_enabled') ? route('shop.consigned-products') : '#' }}"
            @class([
                'flex items-center justify-center border-2 px-2 py-3 gap-3 rounded-md transition-colors text-xl',
                $stocksStyle => true,
                'cursor-not-allowed' => !config('app.consigned_enabled'),
            ])>
            <i class="fa-solid fa-shop"></i>
            <div class="font-bold">{{ __('web.consigned-products') }}</div>
        </a>
        <a href="{{ route('shop.payment-plans') }}"
            class="flex items-center justify-center border-2 px-2 py-3 gap-3 rounded-md transition-colors text-xl  {{ $paymentPlansStyle }}">
            <i class="fa-solid fa-list"></i>
            <div class="font-bold">{{ __('web.payment-plans') }}</div>
        </a>
        <a href="#"
            class="flex items-center justify-center border-2 px-2 py-3 gap-3 rounded-md transition-colors text-xl  {{ $subusersStyle }}">
            <i class="fa-solid fa-users"></i>
            <div class="font-bold">{{ __('web.subusers') }}</div>
        </a>
        <a href="{{ route('shop.settings') }}"
            class="flex items-center justify-center border-2 px-2 py-3 gap-3 rounded-md transition-colors text-xl  {{ $settingsStyle }}">
            <i class="fa-solid fa-wrench"></i>
            <div class="font-bold">{{ __('web.settings') }}</div>
        </a>
    </div>
</div>
