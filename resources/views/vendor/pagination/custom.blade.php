@if ($paginator->hasPages())
<div class="pagination-wrapper">

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span class="page-btn disabled">‹</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="page-btn">‹</a>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)

        {{-- "..." --}}
        @if (is_string($element))
            <span class="page-dots">{{ $element }}</span>
        @endif

        {{-- Page Numbers --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="page-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @endif
            @endforeach
        @endif

    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="page-btn">›</a>
    @else
        <span class="page-btn disabled">›</span>
    @endif

</div>
@endif
