@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-16" x-data="{ modalOpen: false, activeReview: null }">

    <!-- Top Breadcrumbs & Page Header -->
    <div class="space-y-1">
        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <span class="text-slate-800 dark:text-slate-200 font-semibold">Reviews</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Product Reviews & Ratings</h1>
                <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80">
                    {{ $reviews->total() }} Reviews
                </span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reviews.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-2xl shadow-lg shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>Add Review</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert / Validation Feedback -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
    @endif

    <!-- Top Metrics Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Reviews -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Reviews</span>
                <div class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['total'] ?? 0) }}</div>
        </div>

        <!-- 2. Approved Reviews -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Approved</span>
                <div class="h-8 w-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['approved'] ?? 0) }}</div>
        </div>

        <!-- 3. Pending Reviews -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Pending Review</span>
                <div class="h-8 w-8 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xs">
                    {{ $stats['pending'] ?? 0 }}
                </div>
            </div>
            <div class="text-2xl font-extrabold text-amber-600 dark:text-amber-400">{{ number_format($stats['pending'] ?? 0) }}</div>
        </div>

        <!-- 4. Average Rating -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Avg Rating</span>
                <div class="h-8 w-8 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center font-bold text-sm">
                    ★
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 dark:text-white flex items-baseline gap-1">
                <span>{{ $stats['avg_rating'] ?? '5.0' }}</span>
                <span class="text-xs font-normal text-slate-400">/ 5.0</span>
            </div>
        </div>
    </div>

    <!-- Main Container Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4">
        
        <!-- Filter Toolbar -->
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            
            <!-- Left: Entries selector -->
            <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                <span>Showing</span>
                <select 
                    name="per_page" 
                    onchange="this.form.submit()" 
                    class="px-2.5 py-1.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 cursor-pointer"
                >
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 100) == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span>entries</span>
            </div>

            <!-- Right: Search, Rating, Status & Submit -->
            <div class="flex flex-wrap items-center gap-2.5 flex-1 lg:justify-end">
                
                <!-- Search Input -->
                <div class="relative flex-1 sm:w-64 sm:flex-initial">
                    <svg class="absolute left-3.5 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search reviewer, product, comment..." 
                        class="w-full pl-9 pr-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                    >
                </div>

                <!-- Rating Filter -->
                <select 
                    name="rating" 
                    onchange="this.form.submit()" 
                    class="px-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 cursor-pointer"
                >
                    <option value="">All Ratings</option>
                    <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5 Stars</option>
                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ 4 Stars</option>
                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>⭐⭐⭐ 3 Stars</option>
                    <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>⭐⭐ 2 Stars</option>
                    <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>⭐ 1 Star</option>
                </select>

                <!-- Status Filter -->
                <select 
                    name="status" 
                    onchange="this.form.submit()" 
                    class="px-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-emerald-500 cursor-pointer"
                >
                    <option value="">All Statuses</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>

                <!-- Filter button -->
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition-all cursor-pointer"
                >
                    Filter
                </button>

                <!-- Reset button -->
                @if(request()->hasAny(['search', 'rating', 'status', 'per_page']))
                    <a 
                        href="{{ route('admin.reviews.index') }}" 
                        class="px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-xl transition-all"
                        title="Reset Filters"
                    >
                        Reset
                    </a>
                @endif

            </div>

        </form>

        <!-- 1. DESKTOP TABLE VIEW (Visible on lg and above) -->
        <div class="hidden lg:block overflow-x-auto border border-slate-100 dark:border-slate-800/80 rounded-2xl">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-4">Reviewer</th>
                        <th class="px-5 py-4">Product</th>
                        <th class="px-5 py-4">Rating</th>
                        <th class="px-5 py-4">Feedback Comment</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Date</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($reviews as $review)
                        @php
                            $isApproved = ($review->status === 'approved');
                            $reviewerName = ($review->user && !empty($review->user->name)) ? $review->user->name : (!empty($review->author_name) && $review->author_name !== 'Verified Customer' ? $review->author_name : 'Guest Customer');
                            $pImg = $review->product ? ($review->product->image ?: ($review->product->images->first()->image_path ?? null)) : null;
                            $pImgUrl = $pImg ? (str_starts_with($pImg, 'http') || str_starts_with($pImg, 'data:') ? $pImg : asset('storage/' . $pImg)) : null;
                        @endphp
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors">
                            
                            <!-- Reviewer Avatar & Name -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-extrabold flex items-center justify-center text-sm shrink-0 border border-emerald-200 dark:border-emerald-800/80 shadow-sm">
                                        {{ strtoupper(substr($reviewerName, 0, 1)) }}
                                    </div>
                                    <div class="space-y-0.5 min-w-0">
                                        <span class="font-bold text-slate-900 dark:text-white text-xs block truncate">{{ $reviewerName }}</span>
                                        @if($review->author_designation)
                                            <span class="text-[11px] text-slate-400 block truncate">{{ $review->author_designation }}</span>
                                        @else
                                            <span class="text-[10px] font-medium text-slate-400 block">{{ $review->user ? 'Registered User' : 'Guest Customer' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Product Info -->
                            <td class="px-5 py-4 max-w-xs">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-9 w-9 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shrink-0 overflow-hidden flex items-center justify-center">
                                        @if($pImgUrl)
                                            <img src="{{ $pImgUrl }}" alt="Product" class="h-full w-full object-cover">
                                        @else
                                            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        @if($review->product)
                                            <a href="{{ route('admin.products.edit', $review->product->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 transition-colors block truncate text-xs">
                                                {{ $review->product->name }}
                                            </a>
                                        @else
                                            <span class="text-slate-400 text-xs italic">General Store Review</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Rating Stars -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-1 text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-3.5 w-3.5 {{ $i <= $review->rating ? 'fill-current' : 'text-slate-200 dark:text-slate-700' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                    <span class="text-[10px] font-bold text-slate-500 ml-1">({{ $review->rating }}/5)</span>
                                </div>
                            </td>

                            <!-- Feedback Comment -->
                            <td class="px-5 py-4 max-w-sm">
                                <p class="text-slate-700 dark:text-slate-300 line-clamp-2 text-xs leading-relaxed">{{ $review->comment }}</p>
                            </td>

                            <!-- Status Badge -->
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold {{ $isApproved ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80' : 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $isApproved ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    {{ ucfirst($review->status) }}
                                </span>
                            </td>

                            <!-- Created Date -->
                            <td class="px-5 py-4 text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                {{ $review->created_at ? $review->created_at->format('M d, Y') : '—' }}
                            </td>

                                     <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                     
                                    <!-- View Details Modal Trigger -->
                                    <button 
                                        type="button"
                                        @click="activeReview = {
                                            id: {{ $review->id }},
                                            reviewerName: '{{ addslashes($reviewerName) }}',
                                            reviewerEmail: '{{ addslashes($review->user->email ?? '') }}',
                                            isRegistered: {{ $review->user ? 'true' : 'false' }},
                                            authorDesignation: '{{ addslashes($review->author_designation ?? '') }}',
                                            productName: '{{ addslashes($review->product->name ?? 'Deleted / General Product') }}',
                                            productImg: '{{ $pImgUrl }}',
                                            productUrl: '{{ $review->product ? route('admin.products.edit', $review->product->id) : '#' }}',
                                            rating: {{ $review->rating }},
                                            comment: @js($review->comment),
                                            status: '{{ $review->status }}',
                                            isApproved: {{ $isApproved ? 'true' : 'false' }},
                                            date: '{{ $review->created_at ? $review->created_at->format('M d, Y h:i A') : 'N/A' }}',
                                            toggleUrl: '{{ route('admin.reviews.toggle-status', $review->id) }}',
                                            deleteUrl: '{{ route('admin.reviews.destroy', $review->id) }}',
                                            showUrl: '{{ route('admin.reviews.show', $review->id) }}'
                                        }; modalOpen = true;"
                                        class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-950/60 dark:hover:text-emerald-400 flex items-center justify-center transition-all cursor-pointer"
                                        title="View Review Details"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    <!-- Toggle Approved / Pending -->
                                    <form method="POST" action="{{ route('admin.reviews.toggle-status', $review->id) }}" class="inline">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="h-8 w-8 rounded-xl {{ $isApproved ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-amber-50 hover:text-amber-600' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 hover:bg-emerald-100' }} flex items-center justify-center transition-all cursor-pointer" 
                                            title="{{ $isApproved ? 'Mark as Pending' : 'Approve Review' }}"
                                        >
                                            @if($isApproved)
                                                <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            @else
                                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Delete Review -->
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" onsubmit="return confirm('Delete this review?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/60 dark:hover:text-rose-400 flex items-center justify-center transition-all cursor-pointer"
                                            title="Delete Review"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400 italic">No reviews found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 2. MOBILE & TABLET CARDS VIEW (Visible on Mobile/Tablet < lg) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:hidden">
            @forelse($reviews as $review)
                @php
                    $isApproved = ($review->status === 'approved');
                    $reviewerName = $review->author_name ?: ($review->user->name ?? 'Verified Buyer');
                    $pImg = $review->product ? ($review->product->image ?: ($review->product->images->first()->image_path ?? null)) : null;
                    $pImgUrl = $pImg ? (str_starts_with($pImg, 'http') || str_starts_with($pImg, 'data:') ? $pImg : asset('storage/' . $pImg)) : null;
                @endphp
                <div class="bg-slate-50/70 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 space-y-4 flex flex-col justify-between shadow-sm">
                    
                    <div class="space-y-3">
                        <!-- Top Row: Reviewer Info & Status Badge -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-11 w-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-extrabold flex items-center justify-center text-base shrink-0 border border-emerald-200 dark:border-emerald-800/80 shadow-sm">
                                    {{ strtoupper(substr($reviewerName, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-slate-900 dark:text-white text-sm truncate">{{ $reviewerName }}</h3>
                                    <span class="text-[10px] text-slate-400 block">{{ $review->author_designation ?: ($review->user ? 'Registered User' : 'Guest Customer') }}</span>
                                </div>
                            </div>

                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-bold shrink-0 {{ $isApproved ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $isApproved ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                {{ ucfirst($review->status) }}
                            </span>
                        </div>

                        <!-- Product Preview Box -->
                        @if($review->product)
                            <div class="bg-white dark:bg-slate-900 p-2.5 rounded-2xl border border-slate-100 dark:border-slate-800/80 flex items-center gap-2.5 text-xs">
                                <div class="h-9 w-9 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shrink-0 overflow-hidden flex items-center justify-center">
                                    @if($pImgUrl)
                                        <img src="{{ $pImgUrl }}" alt="Product" class="h-full w-full object-cover">
                                    @else
                                        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] text-slate-400 font-semibold uppercase">Product</div>
                                    <a href="{{ route('admin.products.edit', $review->product->id) }}" class="font-bold text-slate-900 dark:text-white truncate block hover:text-emerald-600">
                                        {{ $review->product->name }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Rating Stars -->
                        <div class="flex items-center gap-1.5">
                            <div class="flex items-center gap-0.5 text-amber-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="h-4 w-4 {{ $i <= $review->rating ? 'fill-current' : 'text-slate-200 dark:text-slate-700' }}" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">({{ $review->rating }}/5)</span>
                        </div>

                        <!-- Feedback Comment -->
                        <p class="text-slate-700 dark:text-slate-300 text-xs leading-relaxed bg-white dark:bg-slate-900 p-3 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                            {{ $review->comment }}
                        </p>
                    </div>

                    <!-- Footer: Date & Actions -->
                    <div class="pt-3 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-2">
                        <span class="text-[10px] text-slate-400 font-mono">
                            {{ $review->created_at ? $review->created_at->format('M d, Y') : '' }}
                        </span>

                        <div class="flex items-center gap-1.5">
                            <!-- View Button -->
                            <button 
                                type="button"
                                @click="activeReview = {
                                    id: {{ $review->id }},
                                    reviewerName: '{{ addslashes($reviewerName) }}',
                                    reviewerEmail: '{{ addslashes($review->user->email ?? '') }}',
                                    isRegistered: {{ $review->user ? 'true' : 'false' }},
                                    authorDesignation: '{{ addslashes($review->author_designation ?? '') }}',
                                    productName: '{{ addslashes($review->product->name ?? 'Deleted / General Product') }}',
                                    productImg: '{{ $pImgUrl }}',
                                    productUrl: '{{ $review->product ? route('admin.products.edit', $review->product->id) : '#' }}',
                                    rating: {{ $review->rating }},
                                    comment: @js($review->comment),
                                    status: '{{ $review->status }}',
                                    isApproved: {{ $isApproved ? 'true' : 'false' }},
                                    date: '{{ $review->created_at ? $review->created_at->format('M d, Y h:i A') : 'N/A' }}',
                                    toggleUrl: '{{ route('admin.reviews.toggle-status', $review->id) }}',
                                    deleteUrl: '{{ route('admin.reviews.destroy', $review->id) }}',
                                    showUrl: '{{ route('admin.reviews.show', $review->id) }}'
                                }; modalOpen = true;"
                                class="px-2.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-emerald-50 hover:text-emerald-600 transition-all flex items-center gap-1"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>View</span>
                            </button>
                            <!-- Toggle Status -->
                            <form method="POST" action="{{ route('admin.reviews.toggle-status', $review->id) }}" class="inline">
                                @csrf
                                <button 
                                    type="submit" 
                                    class="px-3 py-1.5 rounded-xl {{ $isApproved ? 'bg-slate-200/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300' : 'bg-emerald-600 text-white' }} text-xs font-semibold flex items-center gap-1 transition-all cursor-pointer"
                                >
                                    {{ $isApproved ? 'Mark Pending' : 'Approve' }}
                                </button>
                            </form>

                            <!-- Delete -->
                            <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" onsubmit="return confirm('Delete review?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit" 
                                    class="p-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all border border-rose-200 dark:border-rose-800"
                                    title="Delete"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400 italic">No reviews found.</div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($reviews->hasPages())
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                {{ $reviews->links() }}
            </div>
        @endif

    </div>

    <!-- VIEW REVIEW MODAL -->
    <div 
        x-cloak 
        x-show="modalOpen" 
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm transition-all"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="modalOpen = false"
    >
        <div 
            @click.away="modalOpen = false"
            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden space-y-0"
        >
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-950/50">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Customer Review Details</h3>
                        <p class="text-xs text-slate-400">Full feedback and rating summary</p>
                    </div>
                </div>
                <button @click="modalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    ✕
                </button>
            </div>

            <template x-if="activeReview">
                <div class="p-6 space-y-5 max-h-[80vh] overflow-y-auto">
                    <!-- Reviewer & Status Header -->
                    <div class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800/80">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-2xl bg-emerald-500 text-white font-black text-lg flex items-center justify-center shadow-md shadow-emerald-500/20 shrink-0" x-text="activeReview.reviewerName.charAt(0).toUpperCase()"></div>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm" x-text="activeReview.reviewerName"></h4>
                                <div class="flex items-center gap-2 text-xs text-slate-400">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-200/70 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-bold" x-text="activeReview.isRegistered ? 'Registered User' : 'Guest Customer'"></span>
                                    <span x-show="activeReview.reviewerEmail" class="font-mono" x-text="activeReview.reviewerEmail"></span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <span x-show="activeReview.isApproved" class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 inline-flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Approved
                            </span>
                            <span x-show="!activeReview.isApproved" class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 inline-flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full bg-amber-500"></span> Pending
                            </span>
                        </div>
                    </div>

                    <!-- Product & Rating Info -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800/80 space-y-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Product</span>
                            <div class="flex items-center gap-3">
                                <img :src="activeReview.productImg" class="h-10 w-10 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                <a :href="activeReview.productUrl" class="font-bold text-xs text-slate-900 dark:text-white hover:text-emerald-600 transition line-clamp-2" x-text="activeReview.productName"></a>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800/80 space-y-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Rating Score</span>
                            <div class="flex items-center gap-2">
                                <div class="flex items-center text-amber-400">
                                    <template x-for="i in 5">
                                        <svg class="h-5 w-5" :class="i <= activeReview.rating ? 'fill-current text-amber-400' : 'text-slate-200 dark:text-slate-700'" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </template>
                                </div>
                                <span class="font-extrabold text-sm text-slate-900 dark:text-white" x-text="'(' + activeReview.rating + ' / 5)'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Full Comment Text -->
                    <div class="space-y-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Customer Feedback Comment</span>
                        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800 text-slate-800 dark:text-slate-200 text-sm leading-relaxed whitespace-pre-line font-normal" x-text="activeReview.comment || 'No comment provided.'"></div>
                    </div>

                    <!-- Timestamp -->
                    <div class="text-xs text-slate-400 font-medium">
                        Submitted on: <strong class="text-slate-700 dark:text-slate-300" x-text="activeReview.date"></strong>
                    </div>
                </div>
            </template>

            <!-- Modal Footer Actions -->
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                <a :href="activeReview?.showUrl" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 underline">
                    Open Full Detail Page →
                </a>

                <div class="flex items-center gap-2">
                    <template x-if="activeReview">
                        <form :action="activeReview.toggleUrl" method="POST" class="inline">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl transition cursor-pointer" :class="activeReview.isApproved ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : 'bg-emerald-600 text-white hover:bg-emerald-700'" x-text="activeReview.isApproved ? 'Mark Pending' : 'Approve Review'"></button>
                        </form>
                    </template>

                    <button @click="modalOpen = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
