@props([
    'title',
    'value',
    'icon' => null,
    'description' => null,
    'trend' => null,
    'isPositive' => true,
])

<div class="card-hover-effect overflow-hidden border-l-4 border-l-emerald-500 border-y border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-sm transition-all">
    <div class="flex items-center justify-between pb-2">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
            {{ $title }}
        </span>
        @if($icon)
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50">
                {{ $icon }}
            </div>
        @endif
    </div>
    <div class="mt-2">
        <div class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $value }}</div>
        @if($description || $trend)
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1.5 flex-wrap">
                @if($trend)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md font-bold text-[11px] {{ $isPositive ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800' }}">
                        {{ $trend }}
                    </span>
                @endif
                @if($description)
                    <span>{{ $description }}</span>
                @endif
            </p>
        @endif
    </div>
</div>
