@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Top Bar & Breadcrumbs -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <x-breadcrumbs :items="[
                ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['label' => 'Staff & Administrators']
            ]" />
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white mt-1">Staff & Administrators</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Manage internal admin roles, access privileges, and pending approvals.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 transition-all cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                <span>Add Admin / Staff</span>
            </a>
        </div>
    </div>

    <!-- Pending Admin Approvals Card (if any) -->
    @if($pendingAdmins->count() > 0)
        <div class="bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/60 rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="h-2.5 w-2.5 rounded-full bg-amber-500 animate-ping"></span>
                <h3 class="text-sm font-bold text-amber-900 dark:text-amber-300">Pending Admin Sign-Up Approvals ({{ $pendingAdmins->count() }})</h3>
            </div>
            <div class="divide-y divide-amber-200/60 dark:divide-amber-900/40">
                @foreach($pendingAdmins as $pAdmin)
                    <div class="py-3 flex items-center justify-between gap-4 text-xs">
                        <div>
                            <span class="font-bold text-gray-900 dark:text-white block">{{ $pAdmin->name }}</span>
                            <span class="text-gray-500 font-mono text-[11px]">{{ $pAdmin->email }} • {{ $pAdmin->phone }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('admin.users.approve', $pAdmin->id) }}">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs shadow-sm">
                                    Approve
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.reject', $pAdmin->id) }}">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs shadow-sm">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Admins Table -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">User</th>
                        <th class="px-5 py-3.5">Contact</th>
                        <th class="px-5 py-3.5">Department</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Joined</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($admins as $admin)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-primary text-white font-bold flex items-center justify-center text-sm shrink-0">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-gray-900 dark:text-white text-sm block">{{ $admin->name }}</span>
                                        <span class="text-[10px] text-primary font-bold uppercase">{{ $admin->role }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <span class="text-gray-800 dark:text-gray-200 font-mono block">{{ $admin->email }}</span>
                                <span class="text-gray-500 font-mono text-[11px]">{{ $admin->phone }}</span>
                            </td>

                            <td class="px-5 py-4 font-semibold text-gray-700 dark:text-gray-300">
                                {{ $admin->adminProfile->department ?? 'General Admin' }}
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $admin->status === 'active' ? 'bg-green-50 text-green-700 border-green-200' : ($admin->status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200') }}">
                                    {{ ucfirst($admin->status) }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-gray-500 font-mono text-[11px]">
                                {{ $admin->created_at ? $admin->created_at->format('M d, Y') : 'N/A' }}
                            </td>

                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($admin->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.toggle-block', $admin->id) }}">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-lg {{ $admin->status === 'blocked' ? 'text-green-600 hover:bg-green-50' : 'text-amber-600 hover:bg-amber-50' }}" title="{{ $admin->status === 'blocked' ? 'Unblock' : 'Block' }}">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.users.destroy', $admin->id) }}" onsubmit="return confirm('Delete administrator?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-500 hover:text-rose-600 rounded-lg" title="Delete">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400">No administrator accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($admins->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $admins->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
