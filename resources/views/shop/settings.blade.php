<x-app-layout>
    @section('title')
        {{ __('web.settings') }}
    @endsection
    <div class="flex flex-col p-5 lg:p-10 text-gray-900 gap-5">
        @include('shop.menu')
        <form id="password-form" method="POST" action="{{ route('profile.password-update') }}">
            @method('post')
            @csrf
            <div class="bg-[#f2f2f2] rounded-md p-4 flex flex-col shadow-sm">
                <h1 class="uppercase font-bold text-xl text-center lg:text-left mb-5">
                    {{ __('web.password') }}</h1>

                <div class="flex flex-col lg:flex-row justify-between gap-5 mb-5">
                    <div class="w-full">
                        <x-input-label for="current_password" :value="__('app.current_password')" />
                        <x-text-input id="current_password" class="block mt-1 w-full" type="password"
                            name="current_password" required />
                        <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                    </div>
                    <div class="w-full">
                        <x-input-label for="password" :value="__('web.password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                            required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div class="w-full">
                        <x-input-label for="password_confirmation" :value="__('app.password_confirmation')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                            name="password_confirmation" required />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end w-full">
                    <button type="submit"
                        class="text-white font-bold bg-blue-500 rounded-md py-2 px-4 hover:bg-blue-600 transition-colors w-auto lg:w-auto text-center disabled:bg-gray-400 disabled:border-gray-500">
                        <i class="fa-solid fa-check"></i> {{ __('app.update') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

</x-app-layout>
