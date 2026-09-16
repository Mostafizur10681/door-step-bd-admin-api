@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 pb-24 px-2 sm:px-4" x-data="{
    showCurrentPassword: false,
    showNewPassword: false,
    showConfirmPassword: false
}">

    <!-- Header & Breadcrumbs -->
    <div>
        <x-breadcrumbs :items="[
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Settings', 'url' => route('admin.settings.index')],
            ['label' => 'Admin Profile']
        ]" />
        <div class="flex items-center gap-3 mt-2">
            <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xl shadow-inner">
                👤
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Administrator Profile</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Update your administrator account credentials, contact phone, and security password.</p>
            </div>
        </div>
    </div>

    <!-- Success Notification -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 text-base font-bold">&times;</button>
        </div>
    @endif

    <!-- Error Notification -->
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-full bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 dark:text-rose-400 hover:text-rose-900 text-base font-bold">&times;</button>
        </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs font-medium space-y-1.5 shadow-xs">
            <div class="font-bold flex items-center gap-2">
                <span>⚠️</span>
                <span>Please fix the following validation issues:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700 dark:text-rose-300 pl-2">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Profile Form Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('admin.settings.profile.update') }}" class="space-y-6">
            @csrf

            <!-- Section 1: Account Information -->
            <div class="space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-800 pb-2">
                    Account Identity
                </h3>

                <!-- Full Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            👤
                        </div>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required placeholder="e.g. Administrator" class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition shadow-inner">
                    </div>
                </div>

                <!-- Email Address -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            ✉️
                        </div>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="admin@example.com" class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition shadow-inner">
                    </div>
                </div>

                <!-- Phone Number -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">Phone Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            📞
                        </div>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="01700000000" class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition shadow-inner">
                    </div>
                </div>
            </div>

            <!-- Section 2: Password Update -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-4">
                <div>
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        Change Security Password
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Leave blank if you do not wish to change your current password.</p>
                </div>

                <!-- Current Password -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">Current Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            🔒
                        </div>
                        <input :type="showCurrentPassword ? 'text' : 'password'" name="current_password" placeholder="Enter current password" class="w-full pl-10 pr-10 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition shadow-inner">
                        <button type="button" @click="showCurrentPassword = !showCurrentPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <span x-text="showCurrentPassword ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            🔑
                        </div>
                        <input :type="showNewPassword ? 'text' : 'password'" name="password" placeholder="Minimum 6 characters" class="w-full pl-10 pr-10 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition shadow-inner">
                        <button type="button" @click="showNewPassword = !showNewPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <span x-text="showNewPassword ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">Confirm New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            🛡️
                        </div>
                        <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" placeholder="Re-type new password" class="w-full pl-10 pr-10 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 focus:border-emerald-500 transition shadow-inner">
                        <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <span x-text="showConfirmPassword ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="pt-4 flex items-center justify-end border-t border-slate-100 dark:border-slate-800">
                <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white text-xs font-bold rounded-2xl shadow-lg shadow-emerald-600/20 active:scale-[0.98] transition">
                    Save Profile Updates
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
