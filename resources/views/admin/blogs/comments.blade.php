@extends('layouts.admin')

@section('content')
<div class="space-y-4 sm:space-y-6 max-w-6xl mx-auto pb-16 px-1 sm:px-0">
    <div class="space-y-1">
        <div class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600">Dashboard</a>
            <span>&gt;</span>
            <a href="{{ route('admin.blogs.index') }}" class="hover:text-emerald-600">Blog Posts</a>
            <span>&gt;</span>
            <span class="text-slate-800 dark:text-slate-200 font-semibold">Comments Moderation</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Blog Comments Moderation</h1>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex justify-between">
            <span>{{ session('success') }}</span>
            <button @click="$el.parentElement.remove()" class="text-slate-400">✕</button>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-5 shadow-sm space-y-4">
        
        <div class="overflow-x-auto border border-slate-100 dark:border-slate-800 rounded-2xl">
            <table class="w-full text-left text-xs min-w-[650px]">
                <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Article</th>
                        <th class="px-4 py-3">Commenter</th>
                        <th class="px-4 py-3">Comment</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($comments as $comment)
                        <tr>
                            <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                                {{ $comment->blog->title ?? 'Deleted Article' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $comment->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $comment->email }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-300 max-w-xs">
                                {{ $comment->comment }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $comment->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ ucfirst($comment->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($comment->status === 'pending')
                                        <form method="POST" action="{{ route('admin.blogs.comments.approve', $comment->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[10px]">Approve</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.blogs.comments.destroy', $comment->id) }}" onsubmit="return confirm('Delete this comment?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 font-bold text-[10px]">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-400 italic">No blog comments recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($comments->hasPages())
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                {{ $comments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
