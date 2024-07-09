<x-adminlte-input label="{{ __('app.search') }}" name="search" placeholder="{{ __('app.search-placeholder') }}"
    igroup-size="md" class="rounded-0" value="{{ request()->get('search') }}">
    <x-slot name="appendSlot">
        <x-adminlte-button type="submit" theme="outline-dark" label="{{ __('app.search') }}" />
    </x-slot>
    <x-slot name="prependSlot">
        <div class="input-group-text text-dark">
            <i class="fas fa-search"></i>
        </div>
    </x-slot>
</x-adminlte-input>
