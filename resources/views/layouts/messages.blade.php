<div>
    @if($errors->any())
    @foreach($errors->all() as $error )
    <div class="alert text-center my-sm alert-danger m-t-15" data-cy="alert">
        <strong>{{__('web.error')}}! </strong>{{ $error }}
    </div>
    @endforeach
    @endif

    @if(Session::get('error'))
    <div class="alert text-center my-sm alert-danger m-t-15" data-cy="alert">
        <strong>{{__('web.error')}}! </strong> {{ Session::get('error') }}
    </div>
    @endif

    @if(Session::get('success'))
    <div class="alert text-center alert-success my-sm m-t-15" data-cy="alert">
        <strong>{{__('web.success')}}! </strong> {{ Session::get('success') }}
    </div>
    @endif

    <div id="error" class="alert text-center my-sm alert-danger m-t-15" style="display:none;">
        <strong>{{__('web.error')}}! </strong>
    </div>


</div>
