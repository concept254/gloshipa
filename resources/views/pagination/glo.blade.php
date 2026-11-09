@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex flex-wrap items-center justify-between gap-4">
        <p class="text-sm text-ink-500">
            Showing <span class="font-semibold text-ink-800">{{ $paginator->firstItem() }}</span>
            to <span class="font-semibold text-ink-800">{{ $paginator->lastItem() }}</span>
            of <span class="font-semibold text-ink-800">{{ $paginator->total() }}</span>
        </p>

        <div class="flex flex-wrap items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="rounded-lg px-3 py-1.5 text-sm text-ink-300">Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-lg bg-white px-3 py-1.5 text-sm font-medium text-ink-800 ring-1 ring-ink-200 hover:bg-ink-50">Previous</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-sm text-ink-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="rounded-lg bg-ink-900 px-3 py-1.5 text-sm font-semibold text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="rounded-lg bg-white px-3 py-1.5 text-sm font-medium text-ink-800 ring-1 ring-ink-200 hover:bg-ink-50" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-lg bg-white px-3 py-1.5 text-sm font-medium text-ink-800 ring-1 ring-ink-200 hover:bg-ink-50">Next</a>
            @else
                <span class="rounded-lg px-3 py-1.5 text-sm text-ink-300">Next</span>
            @endif
        </div>
    </nav>
@endif
