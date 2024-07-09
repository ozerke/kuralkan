@extends('adminlte::page')

@section('content_header')
    <h1>
        {{ __('app.users') }}</h1>
@stop

@php
    $heads = [
        __('app.user-id'),
        __('app.user-type'),
        __('app.erp-username'),
        __('app.name-surname'),
        __('app.email'),
        __('app.city'),
        __('app.created-at'),
        __('app.user-active'),
        ['label' => __('app.action'), 'no-export' => true, 'width' => 10],
    ];

    $config = [
        'paging' => false,
        'searching' => false,
        'columns' => [
            ['orderable' => false],
            ['orderable' => false],
            ['orderable' => false],
            ['orderable' => false],
            ['orderable' => false],
            ['orderable' => false],
            ['orderable' => false],
            ['orderable' => false],
            ['orderable' => false],
            ['orderable' => false],
        ],
    ];
@endphp

@section('content')
    @include('layouts.messages')
    <form action="{{ route('users.index') }}" method="get" id="filter-form">
        <div class="mb-4">
            <x-adminlte-select id='type-filter' name="filter" label-class="text-dark" igroup-size="md"
                fgroup-class="col-12 col-md-3 mb-0 p-0" label="{{ __('app.filter-by-user-type') }}">
                <option value="" @if (request()->filter == null) selected @endif>{{ __('app.all-types') }}</option>
                <option value="customer" @if (request()->filter == 'customer') selected @endif>{{ __('app.customer') }}</option>
                <option value="shop" @if (request()->filter == 'shop') selected @endif>{{ __('app.shop') }}</option>
                <option value="service" @if (request()->filter == 'service') selected @endif>{{ __('app.service') }}</option>
                <option value="shop-service" @if (request()->filter == 'shop-service') selected @endif>{{ __('app.shop-service') }}
                </option>
            </x-adminlte-select>

            <div class="col-12 p-0 mt-2 mb-2">
                @include('layouts.searchbar')
            </div>
        </div>
    </form>

    <x-adminlte-datatable id="users-table" :heads="$heads" :config="$config" beautify striped hoverable head-theme="dark">
        @foreach ($users as $user)
            <tr style="white-space:nowrap;width:100%;">
                <td>{{ $user->id }}</td>

                <td>{{ $user->getUserType() }}</td>
                <td>{{ $user->getErpName() }}</td>
                <td>{{ $user->full_name }}</td>
                {{-- <td>{{ optional($user->registeredBy)->getErpName() ?? '-'}}</td> --}}
                <td>{{ $user->email }}</td>
                <td>{{ $user->district->city->currentTranslation->city_name ?? '-' }}</td>
                <td>{{ $user->created_at }}</td>
                <td><b>
                        @if ($user->user_active === 'Y')
                            <span class="badge badge-success text-md">{{ __('app.yes') }}</span>
                        @else
                            <span class="badge badge-danger text-md">{{ __('app.no') }}</span>
                        @endif
                    </b></td>
                <td style="text-align: left">
                    <a class="btn btn-xs btn-default text-primary mx-1" title="Edit User"
                        href="{{ route('users.edit', $user->id) }}">
                        <i class="fa fa-lg fa-fw fa-eye"></i>
                    </a>
                    <a class="btn btn-xs btn-default text-primary mx-1" title="Orders By User"
                        href="{{ route('users.orders', $user->id) }}">
                        <i class="fa fa-lg fa-fw fa-shopping-cart"></i>
                    </a>
                    <a class="btn btn-xs btn-default text-primary mx-1" title="User Payments"
                        href="{{ route('users.payments', ['id' => $user->id, 'with_bonds' => true]) }}">
                        <i class="fa fa-lg fa-fw fa-money-bill"></i>
                    </a>
                    <a class="btn btn-xs btn-default text-primary mx-1" title="User Payment Plans" href="#">
                        <i class="fa fa-lg fa-fw fa-wallet"></i>
                    </a>
                    @if ($user->hasRole(App\Models\User::ROLES['shop']) || $user->hasRole(App\Models\User::ROLES['shop-service']))
                        <a class="btn btn-xs btn-default text-primary mx-1" title="Shop Stocks"
                            href="{{ route('users.shop-stocks', $user->id) }}">
                            <i class=" fa fa-lg fa-fw fa-store"></i>
                        </a>
                    @endif
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>

    {{ $users->appends(request()->query())->onEachSide(0)->links() }}

@stop

@section('js')
    <script>
        $("#type-filter").on('change', function() {
            $("#filter-form").submit();
        });
    </script>
@endsection
