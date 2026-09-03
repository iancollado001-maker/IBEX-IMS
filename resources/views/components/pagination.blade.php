@if ($paginator->hasPages())
<nav>
    <ul class="pagination mb-0" style="gap:4px;display:flex;list-style:none;padding:0;margin:0;">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <li style="opacity:0.4;">
                <span class="btn-ibex btn-ibex-ghost btn-ibex-sm"><i class="bi bi-chevron-left"></i></span>
            </li>
        @else
            <li>
                <a href="{{ $paginator->previousPageUrl() }}" class="btn-ibex btn-ibex-ghost btn-ibex-sm">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li><span class="btn-ibex btn-ibex-ghost btn-ibex-sm">{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li>
                            <span class="btn-ibex btn-ibex-primary btn-ibex-sm">{{ $page }}</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $url }}" class="btn-ibex btn-ibex-ghost btn-ibex-sm">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <li>
                <a href="{{ $paginator->nextPageUrl() }}" class="btn-ibex btn-ibex-ghost btn-ibex-sm">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        @else
            <li style="opacity:0.4;">
                <span class="btn-ibex btn-ibex-ghost btn-ibex-sm"><i class="bi bi-chevron-right"></i></span>
            </li>
        @endif
    </ul>
</nav>
@endif
