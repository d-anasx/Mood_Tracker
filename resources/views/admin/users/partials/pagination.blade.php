@if ($users->hasPages())
    <div class="pagination">
        @if ($users->onFirstPage())
            <span class="disabled" aria-disabled="true">&laquo;</span>
        @else
            <a href="#" data-page="{{ $users->currentPage() - 1 }}" class="pagination-link">&laquo;</a>
        @endif

        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
            @if ($page == $users->currentPage())
                <span class="active"><span>{{ $page }}</span></span>
            @else
                <a href="#" data-page="{{ $page }}" class="pagination-link">{{ $page }}</a>
            @endif
        @endforeach

        @if ($users->hasMorePages())
            <a href="#" data-page="{{ $users->currentPage() + 1 }}" class="pagination-link">&raquo;</a>
        @else
            <span class="disabled" aria-disabled="true">&raquo;</span>
        @endif
    </div>
@endif