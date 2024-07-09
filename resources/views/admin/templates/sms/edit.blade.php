@extends('adminlte::page')

@section('content_header')
    <h1>{{ __('app.edit-sms-template') }}</h1>
@stop

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <style>
        .cm-placeholder {
            background-color: #ffeb3b;
            color: #000;
            border-radius: 2px;
            padding: 2px;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/matchbrackets.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editor = CodeMirror.fromTextArea(document.getElementById("content"), {
                mode: "text/plain",
                lineNumbers: false,
                theme: "default",
                lineWrapping: true,
                matchBrackets: false,
            });

            editor.setSize('auto', '100%');

            function markPlaceholders() {
                const regex = /\{\{\s*\$[\w]+\s*\}\}/g;
                const text = editor.getValue();
                let match;

                while ((match = regex.exec(text)) !== null) {
                    const from = editor.posFromIndex(match.index);
                    const to = editor.posFromIndex(match.index + match[0].length);
                    editor.markText(from, to, {
                        className: 'cm-placeholder'
                    });
                }
            }

            markPlaceholders();

            editor.on('change', function() {
                editor.getAllMarks().forEach(mark => mark.clear());
                markPlaceholders();
            });
        });
    </script>
@endpush

@section('content')
    @include('layouts.messages')
    <x-adminlte-alert theme="info" title="{{ $template['title'] }} ({{ $key }})">
        <p class="text-lg">{{ __('app.parameters') }}:
            @foreach ($template['parameters'] as $param)
                <code class="mx-2 text-white bg-secondary p-1 rounded mb-4">{{ $param }}</code>
            @endforeach
        </p>
    </x-adminlte-alert>

    <form method="POST" action="{{ route('templates.sms.update', $key) }}">
        @method('patch')
        @csrf

        <div class="row bg-white p-2">
            <textarea id="content" class="col-12" required name="content" placeholder="...">
                    {!! $content !!}
                </textarea>
        </div>


        <div class="w-full d-flex justify-content-end">
            <button class="btn btn-md btn-success my-4">
                {{ __('app.save') }}
            </button>
        </div>
    </form>


@stop