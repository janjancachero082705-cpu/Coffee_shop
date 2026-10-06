@if ($paginator->hasPages())
    <nav class="pag-nav" role="navigation" aria-label="Pagination">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="pag-arrow pag-disabled">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pag-arrow" rel="prev">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
            </a>
        @endif

        {{-- 3 Visible Numbers with rolling window --}}
        @php
            $current = $paginator->currentPage();
            $last = $paginator->lastPage();
            
            // Determine ang 3 numbers to show
            if ($last <= 3) {
                $start = 1;
                $end = $last;
            } elseif ($current <= 2) {
                $start = 1;
                $end = 3;
            } elseif ($current >= $last - 1) {
                $start = $last - 2;
                $end = $last;
            } else {
                $start = $current - 1;
                $end = $current + 1;
            }
        @endphp

        <div class="pag-numbers" data-current="{{ $current }}">
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $current)
                    <span class="pag-num pag-active">{{ $page }}</span>
                @else
                    <a href="{{ $paginator->url($page) }}" class="pag-num">{{ $page }}</a>
                @endif
            @endfor
        </div>

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pag-arrow" rel="next">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </a>
        @else
            <span class="pag-arrow pag-disabled">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </span>
        @endif
    </nav>
@endif