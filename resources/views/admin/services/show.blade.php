@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto pb-16 px-1 sm:px-0">

    <!-- Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <div class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Dashboard</a>
                <span>&gt;</span>
                <a href="{{ route('admin.services.index') }}" class="hover:text-emerald-600 transition-colors">Our Services</a>
                <span>&gt;</span>
                <span class="text-slate-800 dark:text-slate-200 font-semibold truncate max-w-xs">{{ $service->title }}</span>
            </div>
            <div class="flex items-center gap-3 pt-0.5">
                <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    @if($service->icon)
                        <span class="text-2xl">{{ $service->icon }}</span>
                    @endif
                    <span>{{ $service->title }}</span>
                </h1>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $service->status === 'active' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                    <span class="h-2 w-2 rounded-full {{ $service->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                    {{ ucfirst($service->status) }}
                </span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
            <a href="{{ route('admin.services.edit', $service->id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white transition-all shadow-md shadow-emerald-600/20">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                <span>Edit Service</span>
            </a>

            <form method="POST" action="{{ route('admin.services.toggle-status', $service->id) }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl transition-all cursor-pointer">
                    <span>Toggle Status</span>
                </button>
            </form>

            <form method="POST" action="{{ route('admin.services.destroy', $service->id) }}" onsubmit="return confirm('Are you sure you want to delete this service? This action cannot be undone.');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2.5 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-800 rounded-xl transition-all cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Delete</span>
                </button>
            </form>

            <a href="{{ route('admin.services.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                ← Back
            </a>
        </div>
    </div>

    <!-- Alert / Feedback -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
    @endif

    <!-- Main Content Layout (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Media & Content -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Service Image & Header Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-3.5">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>🖼️</span> Service Media
                    </h3>
                    <span class="text-xs font-mono text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800">
                        /services/{{ $service->slug }}
                    </span>
                </div>

                @php
                    $imgSrc = null;
                    if (!empty($service->image)) {
                        if (str_starts_with($service->image, 'data:image') || str_starts_with($service->image, 'http')) {
                            $imgSrc = $service->image;
                        } else {
                            $imgSrc = asset($service->image);
                        }
                    }
                @endphp

                @if($imgSrc)
                    <div class="relative rounded-2xl overflow-hidden bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 group">
                        <img src="{{ $imgSrc }}" alt="{{ $service->title }}" class="w-full max-h-96 object-contain mx-auto transition-transform duration-300 group-hover:scale-[1.01]">
                    </div>
                @else
                    <div class="rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800 p-8 flex flex-col items-center justify-center text-center gap-3 bg-slate-50/50 dark:bg-slate-950/40">
                        <div class="h-16 w-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-3xl text-slate-400">
                            {{ $service->icon ?: '🛠️' }}
                        </div>
                        <div class="space-y-0.5">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">No Custom Image Uploaded</span>
                            <span class="text-[11px] text-slate-400">You can upload a banner/photo by editing this service.</span>
                        </div>
                        <a href="{{ route('admin.services.edit', $service->id) }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                            + Upload Image
                        </a>
                    </div>
                @endif

                <!-- Short Summary / Overview -->
                @if($service->short_description)
                    <div class="rounded-2xl p-4 bg-slate-50 dark:bg-slate-950/70 border border-slate-200/80 dark:border-slate-800/80 space-y-1">
                        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Short Summary</h4>
                        <p class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-medium">
                            {{ $service->short_description }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Full Description Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800/80 pb-3 flex items-center gap-2">
                    <span>📝</span> Full Service Details
                </h3>

                @if($service->description)
                    <div class="prose dark:prose-invert max-w-none text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed space-y-3">
                        {!! $service->description !!}
                    </div>
                @else
                    <div class="text-center py-8 text-slate-400 text-xs italic">
                        No detailed description provided for this service.
                    </div>
                @endif
            </div>

        </div>

        <!-- Right 1 Col: Summary & Metadata Sidebar -->
        <div class="space-y-6">

            <!-- Overview Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-5 shadow-sm space-y-4 text-xs">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                    <span>ℹ️</span> Service Overview
                </h3>

                <div class="space-y-3">
                    <div class="flex items-center justify-between py-1 border-b border-slate-50 dark:border-slate-800/50">
                        <span class="text-slate-400 font-medium">Status</span>
                        <span class="inline-flex items-center gap-1.5 font-bold {{ $service->status === 'active' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $service->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                            {{ ucfirst($service->status) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-1 border-b border-slate-50 dark:border-slate-800/50">
                        <span class="text-slate-400 font-medium">Icon</span>
                        <span class="text-base font-bold">{{ $service->icon ?: 'None' }}</span>
                    </div>

                    <div class="flex items-center justify-between py-1 border-b border-slate-50 dark:border-slate-800/50">
                        <span class="text-slate-400 font-medium">Service ID</span>
                        <span class="font-mono font-bold text-slate-800 dark:text-slate-200">#{{ $service->id }}</span>
                    </div>

                    <div class="space-y-1 py-1">
                        <span class="text-slate-400 font-medium block">Slug URL</span>
                        <div class="bg-slate-50 dark:bg-slate-950 p-2 rounded-xl border border-slate-100 dark:border-slate-800 text-[11px] font-mono text-slate-700 dark:text-slate-300 break-all select-all">
                            {{ $service->slug }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audit Timestamps Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-5 shadow-sm space-y-4 text-xs">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                    <span>🕒</span> Timestamps
                </h3>

                <div class="space-y-3">
                    <div class="space-y-0.5">
                        <span class="text-slate-400 text-[11px] block font-medium">Created On:</span>
                        <div class="font-mono font-semibold text-slate-800 dark:text-slate-200">
                            {{ $service->created_at ? $service->created_at->format('M d, Y · h:i A') : '—' }}
                        </div>
                        <div class="text-[10px] text-slate-400">
                            ({{ $service->created_at ? $service->created_at->diffForHumans() : '' }})
                        </div>
                    </div>

                    <div class="space-y-0.5 pt-2 border-t border-slate-50 dark:border-slate-800/50">
                        <span class="text-slate-400 text-[11px] block font-medium">Last Modified:</span>
                        <div class="font-mono font-semibold text-slate-800 dark:text-slate-200">
                            {{ $service->updated_at ? $service->updated_at->format('M d, Y · h:i A') : '—' }}
                        </div>
                        <div class="text-[10px] text-slate-400">
                            ({{ $service->updated_at ? $service->updated_at->diffForHumans() : '' }})
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Action Card -->
            <div class="bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 rounded-2xl sm:rounded-3xl p-4 sm:p-5 space-y-2.5 text-xs">
                <span class="font-bold text-emerald-900 dark:text-emerald-200 block text-xs">Quick Actions</span>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('admin.services.create') }}" class="w-full text-center py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-xs transition-colors">
                        + Add Another Service
                    </a>
                    <a href="{{ route('admin.services.index') }}" class="w-full text-center py-2 px-3 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 font-semibold rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 transition-colors">
                        View All Services
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
