@extends('layouts.auth')

@section('content')
<div class="bg-white/95 dark:bg-slate-900/95 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden backdrop-blur-xl transition-all">
    
    <!-- Header Logo & Title -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center p-3 rounded-2xl bg-white shadow-md border border-slate-100 dark:border-slate-800 mb-3">
            <img src="{{ asset('logo.png') }}" alt="SMT Mart BD Logo" class="h-10 w-auto object-contain">
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Create Admin Account</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Submit registration for administrator review</p>
    </div>

    <!-- Mode Switcher Tabs -->
    <div class="grid grid-cols-2 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl mb-6">
        <a href="{{ route('admin.login') }}" class="py-2 text-xs font-semibold rounded-xl text-center text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all">
            Sign In
        </a>
        <a href="{{ route('admin.register') }}" class="py-2 text-xs font-bold rounded-xl text-center bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm transition-all">
            Sign Up
        </a>
    </div>

    @if($errors->any())
        <div class="mb-4 p-3 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-medium">
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Register Form -->
    <form method="POST" action="{{ route('admin.register.post') }}" class="space-y-3.5" autocomplete="off">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
            <input 
                type="text" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                placeholder="First Name"
                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500"
            >
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
            <input 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                autocomplete="off"
                required 
                placeholder="admin@example.com"
                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500"
            >
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number</label>
            <input 
                type="text" 
                name="phone" 
                value="{{ old('phone') }}" 
                required 
                placeholder="+880 1XXXXXXXXX"
                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500"
            >
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Password</label>
            <input 
                type="password" 
                name="password" 
                autocomplete="new-password"
                required 
                placeholder="Minimum 6 characters"
                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500"
            >
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Confirm Password</label>
            <input 
                type="password" 
                name="password_confirmation" 
                autocomplete="new-password"
                required 
                placeholder="Re-enter password"
                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500"
            >
        </div>

        <button 
            type="submit" 
            class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/25 transition-all duration-200 mt-2 cursor-pointer flex items-center justify-center gap-2"
        >
            <span>Submit for Approval</span>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
        </button>
    </form>
</div>
@endsection
