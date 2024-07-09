<div>
    @if($errors->any())
    <div class="bg-red-100 border-t border-b border-red-500 text-red-700 px-4 py-3 shadow-md inline-block text-center w-full" role="alert">
        <div class="flex items-center w-full">
            <div class="w-full">
                <p class="font-bold">{{__('web.error')}}!</p>
                @foreach($errors->all() as $error )
                <p class="text-sm">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if(Session::get('error'))
    <div class="bg-red-100 border-t border-b border-red-500 text-red-700 px-4 py-3 shadow-md inline-block text-center w-full" role="alert">
        <div class="flex items-center w-full">
            <div class="w-full">
                <p class="font-bold">{{__('web.error')}}!</p>
                <p class="text-sm">{{ Session::get('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(Session::get('success'))
    <div class="bg-green-100 border-t border-b border-green-500 text-green-700 px-4 py-3 shadow-md inline-block text-center w-full" role="alert">
        <div class="flex items-center w-full">
            <div class="w-full">
                <p class="font-bold">{{__('web.success')}}!</p>
                <p class="text-sm">{{ Session::get('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div id="error" class="bg-red-100 border-t border-b border-red-500 text-red-700 px-4 py-3 shadow-md text-center w-full hidden" role="alert">
        <div class="flex items-center w-full">
            <div class="w-full">
                <p class="font-bold">{{__('web.error')}}!</p>
                <p class="text-sm">Contact the administration.</p>
            </div>
        </div>
    </div>
</div>
