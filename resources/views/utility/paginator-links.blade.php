@if ($paginator->hasPages())
    <ul class="flex justify-center space-x-1">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="w-10 h-10 flex items-center justify-center text-gray-500 bg-gray-200 rounded cursor-not-allowed"
                aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span aria-hidden="true">&laquo;</span>
            </li>
        @else
            <li>
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="w-10 h-10 flex items-center justify-center text-gray-700 bg-white border rounded hover:bg-gray-100"
                    aria-label="@lang('pagination.previous')">&laquo;</a>
            </li>
        @endif
        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="w-10 h-10 flex items-center justify-center text-gray-700 bg-white border rounded">
                    {{ $element }}</li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="w-10 h-10 flex items-center justify-center text-white bg-blue-500 border rounded"
                            aria-current="page">
                            {{ $page }}</li>
                    @else
                        <li>
                            <a href="{{ $url }}"
                                class="w-10 h-10 flex items-center justify-center text-gray-700 bg-white border rounded hover:bg-gray-100">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li>
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="w-10 h-10 flex items-center justify-center text-gray-700 bg-white border rounded hover:bg-gray-100"
                    aria-label="@lang('pagination.next')">&raquo;</a>
            </li>
        @else
            <li class="w-10 h-10 flex items-center justify-center text-gray-500 bg-gray-200 rounded cursor-not-allowed"
                aria-disabled="true" aria-label="@lang('pagination.next')">
                <span aria-hidden="true">&raquo;</span>
            </li>
        @endif
    </ul>
@endif
