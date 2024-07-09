<x-guest-layout>
    @section('title')
        {!! $authTitle ?? __('web.home-page-title') !!}
    @endsection
    @push('header-tags')
        <meta name="description" content="{{ $authDesc ?? '' }}" />
        <meta name="keywords" content="{{ $authKeywords ?? '' }}" />
    @endpush
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-[40px] lg:gap-[80px] px-[30px] lg:px-[60px] py-[40px] bg-[#f5f5f5]">
        <div class="col-span-12 border-[1px] h-fit bg-white rounded-lg">
            <h2 class="text-xl font-bold mb-[12px] uppercase bg-[#0e60ae] p-4 rounded-t-lg text-white">
                {{ __('web.password-reset') }}</h2>
            <form method="POST" action="{{ route('password.email') }}" class="p-4">
                @csrf
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                        required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="flex flex-col items-center gap-4 mt-4">
                    <button
                        class="font-bold rounded-md h-[50px] text-[#0E60AE] text-[15px] w-full inline-block align-top p-0 tracking-[0] uppercase bg-transparent border-[1px] border-[solid] border-[#0E60AE] hover:text-white hover:bg-[#0e60ae] transition-colors">{{ __('web.request-change') }}</button>
                </div>
            </form>
        </div>

    </div>
</x-guest-layout>
