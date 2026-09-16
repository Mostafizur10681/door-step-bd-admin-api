@extends('layouts.admin')

@section('content')
@php
    $isApproved = ($review->status === 'approved');
    $reviewerName = ($review->user && !empty($review->user->name)) ? $review->user->name : (!empty($review->author_name) && $review->author_name !== 'Verified Customer' ? $review->author_name : 'Guest Customer');
    $pImg = $review->product ? ($review->product->image ?: ($review->product->images->first()->image_path ?? null)) : null;
    $pImgUrl = $pImg ? (str_starts_with($pImg, 'http') || str_starts_with($pImg, 'data:') ? $pImg : asset('storage/' . $pImg)) : asset('images/placeholder.svg');
@endphp

<div class="space-y-6 max-w-5xl mx-auto pb-16">

    <!-- Top Breadcrumb & Actions Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Dashboard</a>
                <span>&gt;</span>
                <a href="{{ route('admin.reviews.index') }}" class="hover:text-emerald-600 transition-colors">Reviews</a>
                <span>&gt;</span>
                <span class="text-slate-800 dark:text-slate-200 font-semibold">Review #{{ $review->id }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Customer Review Details</h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reviews.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition">
                ← Back to List
            </a>

            <!-- Toggle Status -->
            <form method="POST" action="{{ route('admin.reviews.toggle-status', $review->id) }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 {{ $isApproved ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 hover:bg-amber-100 border border-amber-200 dark:border-amber-800' : 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-md shadow-emerald-600/20' }} text-xs font-bold rounded-xl transition cursor-pointer">
                    @if($isApproved)
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Mark as Pending</span>
                    @else
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Approve Review</span>
                    @endif
                </button>
            </form>

            <!-- Delete Review -->
            <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" onsubmit="return confirm('Are you sure you want to delete this review?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 border border-rose-200 dark:border-rose-800 text-xs font-bold rounded-xl transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    <span>Delete</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Message -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Review Details & Full Comment -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Review Feedback Box -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="space-y-1">
                        <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Overall Rating</div>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center text-amber-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="h-5 w-5 {{ $i <= $review->rating ? 'fill-current text-amber-400' : 'text-slate-200 dark:text-slate-700' }}" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-sm font-extrabold text-slate-900 dark:text-white">({{ $review->rating }} / 5)</span>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div>
                        @if($isApproved)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                Approved
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                Pending Review
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Full Feedback Comment -->
                <div class="space-y-2">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Customer Feedback Comment</h3>
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800/80 text-slate-800 dark:text-slate-200 text-sm leading-relaxed whitespace-pre-line font-normal break-words">
                        {{ $review->comment ?: 'No written comment provided.' }}
                    </div>
                </div>

                <!-- Review Date & Time -->
                <div class="text-xs text-slate-400 pt-2 flex items-center gap-2">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span>Submitted on <strong>{{ $review->created_at ? $review->created_at->format('F d, Y \a\t h:i A') : 'N/A' }}</strong></span>
                </div>
            </div>

            <!-- Product Summary Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3">Reviewed Product</h3>
                
                @if($review->product)
                    <div class="flex items-center gap-4">
                        <img src="{{ $pImgUrl }}" alt="{{ $review->product->name }}" class="h-16 w-16 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                        <div class="space-y-1 min-w-0 flex-1">
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm leading-snug truncate">{{ $review->product->name }}</h4>
                            <div class="flex items-center gap-3 text-xs text-slate-500">
                                <span>Price: <strong class="text-emerald-600">৳{{ number_format($review->product->price, 2) }}</strong></span>
                                <span>SKU: <strong class="font-mono text-slate-700 dark:text-slate-300">{{ $review->product->sku ?: '—' }}</strong></span>
                            </div>
                        </div>
                        <a href="{{ route('admin.products.edit', $review->product->id) }}" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold rounded-xl text-slate-700 dark:text-slate-200 transition shrink-0">
                            Edit Product
                        </a>
                    </div>
                @else
                    <div class="text-xs text-slate-400 italic">Product details not available (Product may have been deleted).</div>
                @endif
            </div>

        </div>

        <!-- Right Column: Reviewer Info Card -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3">Reviewer Information</h3>

                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-black flex items-center justify-center text-lg shrink-0 border border-emerald-200 dark:border-emerald-800/80 shadow-sm">
                        {{ strtoupper(substr($reviewerName, 0, 1)) }}
                    </div>
                    <div class="space-y-0.5 min-w-0">
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm truncate">{{ $reviewerName }}</h4>
                        <span class="inline-block px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                            {{ $review->user ? 'Registered User' : 'Guest Customer' }}
                        </span>
                    </div>
                </div>

                <div class="space-y-3 pt-2 text-xs border-t border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">User Email:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $review->user->email ?? 'N/A (Guest)' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">User ID:</span>
                        <span class="font-mono font-semibold text-slate-800 dark:text-slate-200">{{ $review->user_id ? '#' . $review->user_id : 'Guest' }}</span>
                    </div>
                    @if($review->author_designation)
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Designation:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $review->author_designation }}</span>
                        </div>
                    @endif
                </div>

                @if($review->user)
                    <div class="pt-2">
                        <a href="{{ route('admin.customers.show', $review->user->id) }}" class="w-full text-center block px-4 py-2 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 text-xs font-bold rounded-xl transition">
                            View Customer Profile
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
