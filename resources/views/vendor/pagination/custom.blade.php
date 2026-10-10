@if ($paginator->hasPages())
    <nav class="custom-pagination" style="display: flex; justify-content: space-between; align-items: center; width: 100%; margin-top: 10px; flex-wrap: wrap; gap: 16px;">
        <div style="font-size: 0.85rem; color: var(--text-muted);">
            Menampilkan <span style="font-weight: 600; color: var(--ink);">{{ $paginator->firstItem() }}</span> hingga <span style="font-weight: 600; color: var(--ink);">{{ $paginator->lastItem() }}</span> dari <span style="font-weight: 600; color: var(--ink);">{{ $paginator->total() }}</span> tiket
        </div>
        
        <ul style="display: flex; list-style: none; padding: 0; margin: 0; gap: 6px;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--line); color: var(--text-muted); opacity: 0.5; cursor: not-allowed; background: var(--paper-raised);">&laquo;</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--line); color: var(--ink); text-decoration: none; background: var(--paper-raised); transition: all 0.2s;" onmouseover="this.style.background='var(--line)'" onmouseout="this.style.background='var(--paper-raised)'">&laquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li><span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; color: var(--text-muted);">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: var(--primary); color: #fff; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(59,130,246,0.2);">{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--line); color: var(--ink); text-decoration: none; background: var(--paper-raised); transition: all 0.2s;" onmouseover="this.style.background='var(--line)'" onmouseout="this.style.background='var(--paper-raised)'">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--line); color: var(--ink); text-decoration: none; background: var(--paper-raised); transition: all 0.2s;" onmouseover="this.style.background='var(--line)'" onmouseout="this.style.background='var(--paper-raised)'">&raquo;</a>
                </li>
            @else
                <li>
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--line); color: var(--text-muted); opacity: 0.5; cursor: not-allowed; background: var(--paper-raised);">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
