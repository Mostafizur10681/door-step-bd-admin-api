<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{
    theme: localStorage.getItem('theme') || 'light',
    setTheme(val) {
        this.theme = val;
        window.toggleDarkMode(val);
    }
}" :class="{ 'dark': theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches) }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Panel' }} — SMT Mart BD Admin Panel</title>
    
    <!-- Anti-FOUC Theme Initializer -->
    <script>
        (function() {
            try {
                const theme = localStorage.getItem('theme') || 'light';
                const isDark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght,wdth,slnt,grad,ROND@8..144,100..1000,75..125,-12..0,0,0..1&display=swap" rel="stylesheet">
    
    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v={{ filemtime(public_path('favicon-32x32.png')) }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v={{ filemtime(public_path('favicon-16x16.png')) }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v={{ filemtime(public_path('apple-touch-icon.png')) }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 flex flex-col justify-center items-center p-4 sm:p-6 relative overflow-hidden antialiased transition-colors duration-200">

    <!-- Ambient Glowing Background Accents -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-500/15 dark:bg-emerald-500/10 rounded-full blur-3xl pointer-events-none animate-float-slow"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-teal-500/15 dark:bg-teal-500/10 rounded-full blur-3xl pointer-events-none animate-float-slow" style="animation-delay: -4s;"></div>

    <!-- Top Theme Switcher -->
    <div class="absolute top-6 right-6 z-20" x-data="{ open: false }">
        <button 
            @click="open = !open" 
            type="button" 
            class="h-10 w-10 flex items-center justify-center rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all shadow-sm cursor-pointer"
            title="Switch Theme"
        >
            <template x-if="theme === 'light'">
                <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </template>
            <template x-if="theme === 'dark'">
                <svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </template>
            <template x-if="theme === 'system'">
                <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </template>
        </button>

        <div 
            x-show="open" 
            @click.outside="open = false" 
            x-cloak 
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute right-0 mt-2 w-44 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 p-1.5 z-50 text-xs"
        >
            <div class="px-2.5 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Theme Mode</div>
            <button 
                @click="setTheme('light'); open = false;" 
                class="w-full flex items-center justify-between px-2.5 py-2 rounded-xl text-left transition-colors font-medium cursor-pointer"
                :class="theme === 'light' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
            >
                <span class="flex items-center gap-2">Light</span>
                <span x-show="theme === 'light'" class="text-emerald-600 dark:text-emerald-400 font-bold">✓</span>
            </button>
            <button 
                @click="setTheme('dark'); open = false;" 
                class="w-full flex items-center justify-between px-2.5 py-2 rounded-xl text-left transition-colors font-medium cursor-pointer"
                :class="theme === 'dark' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
            >
                <span class="flex items-center gap-2">Dark</span>
                <span x-show="theme === 'dark'" class="text-emerald-600 dark:text-emerald-400 font-bold">✓</span>
            </button>
            <button 
                @click="setTheme('system'); open = false;" 
                class="w-full flex items-center justify-between px-2.5 py-2 rounded-xl text-left transition-colors font-medium cursor-pointer"
                :class="theme === 'system' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
            >
                <span class="flex items-center gap-2">System</span>
                <span x-show="theme === 'system'" class="text-emerald-600 dark:text-emerald-400 font-bold">✓</span>
            </button>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="w-full max-w-md relative z-10">
        @yield('content')
    </div>

    <!-- Footer Copyright -->
    <footer class="mt-8 text-center text-xs text-slate-400 dark:text-slate-500 relative z-10">
        &copy; {{ date('Y') }} SMT Mart BD Admin Panel. All rights reserved.
    </footer>

</body>
</html>