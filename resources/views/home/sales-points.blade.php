<x-app-layout>
    @section('title')
        {{ __('web.sales-points') }}
    @endsection
    <div class="poppins flex px-[20px] py-[20px] w-full bg-[#f5f5f5] flex-col">
        <div class="flex flex-wrap bg-[#f5f5f5] justify-center mb-[20px] w-full">
            <form id="city-form" method="GET" action="{{ route('salesPoints') }}"
                class="flex flex-col lg:flex-row gap-3 justify-center w-full lg:w-auto">
                <select name="city" id="city"
                    class="min-w-full lg:min-w-[250px] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" @if (request()->city == $city->id) selected @endif>
                            {{ $city->currentTranslation->city_name }}</option>
                    @endforeach
                </select>
                <select name="district" id="district"
                    class="min-w-full lg:min-w-[250px] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">{{ __('web.all') }}</option>
                    @foreach ($districts as $district)
                        <option value="{{ $district->id }}" @if (request()->district == $district->id) selected @endif>
                            {{ $district->currentTranslation->district_name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="bg-[#f5f5f5] flex flex-wrap flex-row items-center justify-around gap-4">
            @foreach ($salesPoints as $service)
                <x-bladewind.card
                    class="hover:shadow-gray-500 w-full sm:w-[48%] md:w-[48%] lg:w-[31%] transition-shadow border-none rounded-md">
                    <x-slot name="header">
                        <div class="bg-[#0e60ae] rounded-t-md py-2 px-4 text-lg font-bold text-white shadow-lg">
                            {{ $service->full_name }}</div>
                    </x-slot>
                    <div class="flex flex-col gap-2 py-2 px-4 min-h-[130px] justify-around">
                        <div class="flex flex-row gap-2">
                            <span class="min-w-[80px] font-bold text-gray-700">{{ __('web.address') }}:</span>
                            <span>{{ $service->address ?? '-' }}</span>
                        </div>
                        <div class="flex flex-row gap-2">
                            <span class="min-w-[80px] font-bold text-gray-700">{{ __('web.district') }}:</span>
                            <span>{{ $service->district->city->currentTranslation->city_name }} /
                                {{ $service->district->currentTranslation->district_name }}</span>
                        </div>
                        <div class="flex flex-row gap-2">
                            <span class="min-w-[80px] font-bold text-gray-700">{{ __('web.phone') }}:</span>
                            <a href="tel:{{ $service->phone }}"
                                class="font-bold text-[#0e60ae]">{{ $service->phone }}</a>
                        </div>
                    </div>
                    <x-slot name="footer" class="rounded-md">
                        @if ($service->getMapsUrl())
                            <div class="rounded-md shadow-top">
                                <iframe class="rounded-md" src="{{ $service->getMapsUrl() }}" width="100%"
                                    height="350" style="border: 0;" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        @endif
                    </x-slot>
                </x-bladewind.card>
            @endforeach
        </div>
    </div>
    @push('js')
        <script>
            $("#city").on('change', function() {
                if (showLoader) showLoader();
                $("#city-form").submit();
                $("#district").val('');
            });

            $("#district").on('change', function() {
                if (showLoader) showLoader();
                $("#city-form").submit();
            });
        </script>
    @endpush
</x-app-layout>
