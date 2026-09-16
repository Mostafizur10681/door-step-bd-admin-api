@extends('layouts.admin')

@section('content')
<div class="space-y-6 pb-16">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Dashboard</a>
                <span>&gt;</span>
                <a href="{{ route('admin.locations.divisions') }}" class="hover:text-emerald-600 transition-colors">Divisions</a>
                <span>&gt;</span>
                <a href="{{ route('admin.locations.districts') }}" class="hover:text-emerald-600 transition-colors">Districts</a>
                <span>&gt;</span>
                <span class="text-slate-800 dark:text-slate-200 font-semibold">Thanas</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Thanas & Upazilas</h1>
                <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80">
                    {{ $thanas->total() }}
                </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">Manage localized delivery hubs and coverage zones.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.locations.divisions') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all whitespace-nowrap">
                ← Divisions
            </a>
            <a href="{{ route('admin.locations.districts') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all whitespace-nowrap">
                ← Districts
            </a>
        </div>
    </div>

    <!-- Flash -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center gap-2 shadow-sm">
            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Add Form -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4 h-fit">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                <span class="h-6 w-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </span>
                Add Thana / Upazila
            </h3>
            <form method="POST" action="{{ route('admin.locations.thanas.store') }}" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">District *</label>
                    <select name="district_id" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer">
                        <option value="">Select District</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->id }}" {{ request('district_id') == $dist->id ? 'selected' : '' }}>
                                {{ $dist->name }} ({{ $dist->division->name ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Thana Name (English) *</label>
                    <input type="text" name="name" required placeholder="e.g. Dhanmondi, Gulshan"
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Thana Name (Bangla)</label>
                    <input type="text" name="bn_name" placeholder="e.g. ধানমন্ডি"
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors">
                </div>
                <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer">
                    Save Thana
                </button>
            </form>
        </div>

        <!-- List Panel -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">

            <!-- DESKTOP TABLE (md+) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800 text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-4">#</th>
                            <th class="px-5 py-4">Thana Name</th>
                            <th class="px-5 py-4">District</th>
                            <th class="px-5 py-4">Division</th>
                            <th class="px-5 py-4">Bangla</th>
                            <th class="px-5 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($thanas as $index => $thana)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors group">
                                <td class="px-5 py-4 text-slate-400 font-mono text-[11px]">{{ $thanas->firstItem() + $index }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-7 w-7 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 font-extrabold text-xs flex items-center justify-center border border-purple-200 dark:border-purple-800/80 shrink-0">
                                            {{ strtoupper(substr($thana->name, 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $thana->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 text-[11px] font-semibold">
                                        {{ $thana->district->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[11px] font-semibold">
                                        {{ $thana->district->division->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-500 dark:text-slate-400">{{ $thana->bn_name ?: '—' }}</td>
                                <td class="px-5 py-4 text-right">
                                    <form method="POST" action="{{ route('admin.locations.thanas.destroy', $thana->id) }}" onsubmit="return confirm('Delete thana \'{{ addslashes($thana->name) }}\'?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/60 dark:hover:text-rose-400 flex items-center justify-center transition-all cursor-pointer ml-auto"
                                            title="Delete Thana">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <p class="text-sm font-semibold text-slate-500">No thanas recorded.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- MOBILE / TABLET CARD VIEW (< md) -->
            <div class="md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($thanas as $thana)
                    <div class="p-4 hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <!-- Left -->
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-11 w-11 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 font-extrabold text-base flex items-center justify-center border border-purple-200 dark:border-purple-800/80 shrink-0">
                                    {{ strtoupper(substr($thana->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 space-y-1">
                                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">{{ $thana->name }}</h3>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2 py-0.5 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 text-[10px] font-semibold border border-blue-200 dark:border-blue-800/80">
                                            {{ $thana->district->name ?? 'N/A' }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-[10px] font-semibold">
                                            {{ $thana->district->division->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                    @if($thana->bn_name)
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $thana->bn_name }}</p>
                                    @endif
                                </div>
                            </div>
                            <!-- Right: delete -->
                            <form method="POST" action="{{ route('admin.locations.thanas.destroy', $thana->id) }}" onsubmit="return confirm('Delete thana?');" class="inline shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="h-8 w-8 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white flex items-center justify-center border border-rose-200 dark:border-rose-800 transition-all cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center">
                        <p class="text-sm font-semibold text-slate-500">No thanas recorded.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($thanas->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $thanas->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
