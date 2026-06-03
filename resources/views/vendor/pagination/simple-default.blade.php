@if ($paginator->hasPages())
    <nav class="modern-pagination">
        <ul class="pagination-modern">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link-modern">@lang('pagination.previous')</span>
                </li>
            @else
                <li class="page-item">
                    <a href="{{ $paginator->previousPageUrl() }}" class="page-link-modern" rel="prev">@lang('pagination.previous')</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a href="{{ $paginator->nextPageUrl() }}" class="page-link-modern" rel="next">@lang('pagination.next')</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link-modern">@lang('pagination.next')</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
