@if ($paginator->hasPages())
    @php($elements = $elements ?? [])
    <nav class="tms-pager" role="navigation" aria-label="Navigasi halaman">
        @if ($paginator->onFirstPage())
            <span class="tms-page-link is-disabled" aria-disabled="true" aria-label="Halaman sebelumnya">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m14.5 6-6 6 6 6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="tms-page-link" rel="prev"
                aria-label="Halaman sebelumnya">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m14.5 6-6 6 6 6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="tms-page-ellipsis" aria-hidden="true">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="tms-page-link is-current" aria-current="page"
                            aria-label="Halaman {{ $page }}">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="tms-page-link"
                            aria-label="Buka halaman {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="tms-page-link" rel="next"
                aria-label="Halaman berikutnya">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m9.5 6 6 6-6 6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </a>
        @else
            <span class="tms-page-link is-disabled" aria-disabled="true" aria-label="Halaman berikutnya">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m9.5 6 6 6-6 6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </span>
        @endif
    </nav>
@endif
