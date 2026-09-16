@props(['items' => []])

<nav aria-label="Breadcrumb" class="flex items-center space-x-2 text-xs text-slate-500 dark:text-slate-400">
    @foreach($items as $index => $item)
        @if(!empty($item['url']) && !$loop->last)
            <a href="{{ $item['url'] }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors font-medium">
                {{ $item['label'] }}
            </a>
        @else
            <span class="{{ $loop->last ? 'text-slate-900 dark:text-white font-bold' : '' }}">
                {{ $item['label'] }}
            </span>
        @endif

        @if(!$loop->last)
            <svg class="h-3 w-3 text-slate-400 dark:text-slate-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
        @endif
    @endforeach
</nav>
