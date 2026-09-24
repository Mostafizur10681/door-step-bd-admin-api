@extends('layouts.admin')

@section('content')
<div class="space-y-4 sm:space-y-6 max-w-7xl mx-auto pb-16 px-1 sm:px-0">

    <!-- Header & Breadcrumbs -->
    <div class="space-y-1">
        <div class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <span class="text-slate-800 dark:text-slate-200 font-semibold">Our Services</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
            <div class="flex items-center gap-3">
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Our Services</h1>
                <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                    {{ $services->total() }} {{ Str::plural('Service', $services->total()) }}
                </span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.services.create') }}" class="inline-flex items-center gap-1.5 text-xs font-bold px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white transition-all shadow-md shadow-emerald-600/20">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Add New Service</span>
                </a>
            </div>
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

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-4 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Services</span>
            <div class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-4 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Active</span>
            <div class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['active']) }}</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-4 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Inactive</span>
            <div class="text-xl sm:text-2xl font-extrabold text-amber-600 dark:text-amber-400">{{ number_format($stats['inactive']) }}</div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-sm space-y-4">
        
        <!-- Filter Toolbar -->
        <form method="GET" action="{{ route('admin.services.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 sm:w-80">
                <svg class="absolute left-3.5 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search services by title or description..." class="w-full pl-9 pr-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500">
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.services.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-800 rounded-xl transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Services Table -->
        <div class="overflow-x-auto border border-slate-100 dark:border-slate-800 rounded-2xl">
            <table class="w-full text-left text-xs min-w-[700px]">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-4">Service</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Created</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($services as $service)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if($service->image)
                                        <img src="{{ $service->image }}" alt="{{ $service->title }}" class="w-12 h-12 object-cover rounded-xl border border-slate-200 dark:border-slate-800 shrink-0 shadow-xs">
                                    @elseif($service->icon)
                                        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-xl shrink-0 font-bold">
                                            {{ $service->icon }}
                                        </div>
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-lg shrink-0">
                                            🛠️
                                        </div>
                                    @endif
                                    <div class="space-y-0.5 max-w-md">
                                        <a href="{{ route('admin.services.show', $service->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 transition-colors line-clamp-1">
                                            {{ $service->title }}
                                        </a>
                                        @if($service->short_description)
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1">{{ $service->short_description }}</p>
                                        @endif
                                        <span class="block text-[10px] text-slate-400 font-mono">/services/{{ $service->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <form method="POST" action="{{ route('admin.services.toggle-status', $service->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold cursor-pointer transition-all {{ $service->status === 'active' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-200' }}" title="Click to toggle status">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $service->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                        {{ ucfirst($service->status) }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-4 font-mono text-slate-500 text-[11px]">
                                {{ $service->created_at ? $service->created_at->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.services.show', $service->id) }}" class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center transition-all" title="View Service Details">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.services.edit', $service->id) }}" class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 flex items-center justify-center transition-all" title="Edit Service">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.services.destroy', $service->id) }}" onsubmit="return confirm('Are you sure you want to delete this service?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 hover:bg-rose-50 hover:text-rose-600 flex items-center justify-center transition-all cursor-pointer" title="Delete Service">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-slate-400 italic">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="h-12 w-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-2xl">🛠️</div>
                                    <p class="font-semibold text-slate-600 dark:text-slate-300">No services created yet</p>
                                    <a href="{{ route('admin.services.create') }}" class="text-xs text-emerald-600 font-bold hover:underline">+ Add the first service</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($services->hasPages())
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                {{ $services->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
