@extends('adminlte::page')

@section('plugins.bsCustomFileInput', true)

@section('content_header')
    <h1>{{ __('app.public-files') }}</h1>
@stop

@php
    $heads = [
        __('app.preview'),
        __('app.created-at'),
        __('app.url'),
        ['label' => __('app.action'), 'no-export' => true, 'width' => 10],
    ];

    $config = [
        'paging' => false,
        'searching' => true,
        'columns' => [null, ['orderable' => false], null, null, ['orderable' => false]],
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


            <form method="POST" action="{{ route('public-files.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <x-adminlte-input-file required name="file" igroup-size="md" fgroup-class="col-12 col-md-6"
                        placeholder="..." label="{{ __('app.upload-file') }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-blue">
                                <i class="fas fa-file"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input-file>
                </div>

                <button class="btn btn-lg btn-success my-2">
                    {{ __('app.upload') }}
                </button>
            </form>


        </div>
    </div>
    <x-adminlte-datatable id="public-files-table" :heads="$heads" :config="$config" beautify striped hoverable
        head-theme="dark">
        @foreach ($files as $file)
            <tr style="white-space:nowrap;width:100%;">

                <td class="text-center"><a href="{{ $file->file_url }}" target="_blank">URL</a></td>
                <td class="text-center">{{ $file->created_at->format('d-m-Y H:i') }}</td>
                <td class="text-center"><a target="_blank" href="{{ $file->file_url }}">{{ $file->file_url }}</a></td>
                <td class="text-center">
                    <form method="POST" action="{{ route('public-files.destroy', $file->id) }}">
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

    @if ($files->hasPages())
        <div class="mt-4">
            {{ $files->links() }}
        </div>
    @endif

@stop
