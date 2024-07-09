@extends('adminlte::page')

@section('content_header')
    <h1>{{ __('app.redirects') }}</h1>
@stop

@php
    $heads = [
        __('app.created-at'),
        __('app.source-url'),
        __('app.target-url'),
        ['label' => __('app.action'), 'no-export' => true, 'width' => 10],
    ];

    $config = [
        'paging' => false,
        'searching' => true,
        'columns' => [null, null, null, ['orderable' => false]],
    ];
@endphp

@section('content')
    @include('layouts.messages')
    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#createFormCollapse" aria-expanded="false"
        aria-controls="createFormCollapse">
        {{ __('app.create') }}
    </button>
    <div class="collapse mt-2" id="createFormCollapse">
        <div class="card card-body">
            <form method="POST" action="{{ route('redirects.store') }}">
                @csrf
                <div class="row">
                    <x-adminlte-input name="source_url" label="{{ __('app.source-url') }}" placeholder="..."
                        fgroup-class="col-12 col-md-6" disable-feedback required />
                    <x-adminlte-input name="target_url" label="{{ __('app.target-url') }}" placeholder="..."
                        fgroup-class="col-12 col-md-6" disable-feedback required />
                </div>

                <button class="btn btn-lg btn-success my-2">
                    {{ __('app.save') }}
                </button>
            </form>
        </div>
    </div>
    <x-adminlte-datatable id="redirects-table" :heads="$heads" :config="$config" beautify striped hoverable
        head-theme="dark">
        @foreach ($redirects as $redirect)
            <tr style="white-space:nowrap;width:100%;">
                <td class="text-center">{{ $redirect->created_at->format('d-m-Y H:i') }}</td>
                <td class="text-center">{{ $redirect->source_url }}</td>
                <td class="text-center">{{ $redirect->target_url }}</td>
                <td class="text-center">
                    <form method="POST" action="{{ route('redirects.destroy', $redirect->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-danger text-white mx-1" title="Delete">
                            <i class="fa fa-lg fa-fw fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>

    @if ($redirects->hasPages())
        <div class="mt-4">
            {{ $redirects->links() }}
        </div>
    @endif

@stop
