@extends('layouts.admin')

@section('content')
<div x-data="orderStatusManager()" class="space-y-6 max-w-7xl mx-auto pb-16">

    <!-- Top Breadcrumbs & Page Header -->
    <div class="space-y-1">
        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <a href="{{ route('admin.orders.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Orders</a>
            <span>&gt;</span>
            <span class="text-slate-800 dark:text-slate-200 font-semibold">Order Fulfillment Statuses</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Order Statuses</h1>
                <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80">
                    {{ $statuses->total() ?? count($statuses) }} Statuses
                </span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.orders.payment-status') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                    Payment Statuses
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

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-medium space-y-1">
            <div class="font-bold flex items-center gap-2">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                Please correct the errors:
            </div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Grid: Left Add Form (4 cols), Right Status List (8 cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT COLUMN: ADD ORDER STATUS CARD -->
        <div class="lg:col-span-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4 lg:sticky lg:top-6">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Add Order Status</h2>
                    <p class="text-[11px] text-slate-400">Configure fulfillment progression step.</p>
                </div>
                <div class="h-8 w-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs">
                    +
                </div>
            </div>

            <form method="POST" action="{{ route('admin.orders.order-status.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status Name *</label>
                    <input 
                        type="text" 
                        name="name" 
                        required 
                        placeholder="e.g. Processing, Shipped, Delivered, Cancelled" 
                        value="{{ old('name') }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Description (Optional)</label>
                    <textarea 
                        name="description" 
                        rows="2" 
                        placeholder="Brief note about this stage..." 
                        class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors resize-none"
                    >{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status Flag</label>
                    <select 
                        name="status" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer"
                    >
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-2xl shadow-lg shadow-emerald-600/20 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer flex items-center justify-center gap-1.5"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Save Order Status
                </button>
            </form>
        </div>

        <!-- RIGHT COLUMN: STATUSES LIST -->
        <div class="lg:col-span-8 space-y-4">
            
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4">
                
                <!-- Search & Filters -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="relative flex-1">
                        <svg class="absolute left-3.5 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <input 
                            type="text" 
                            x-model="searchQuery" 
                            placeholder="Filter order statuses..." 
                            class="w-full pl-9 pr-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                        >
                    </div>
                </div>

                <!-- 1. DESKTOP TABLE VIEW (Visible on lg and above) -->
                <div class="hidden lg:block overflow-x-auto border border-slate-100 dark:border-slate-800/80 rounded-2xl">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-5 py-3.5">Status Name</th>
                                <th class="px-5 py-3.5">Slug</th>
                                <th class="px-5 py-3.5">Description</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            @forelse($statuses as $item)
                                @php
                                    $isActive = ($item->status === 'active' || $item->status === '1' || $item->status === 1 || $item->status === true);
                                @endphp
                                <tr 
                                    class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors"
                                    x-show="!searchQuery || '{{ strtolower(addslashes($item->name . ' ' . $item->slug . ' ' . $item->description)) }}'.includes(searchQuery.toLowerCase())"
                                >
                                    <!-- Name & Badge -->
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="h-2 w-2 rounded-full {{ $isActive ? 'bg-emerald-500 ring-4 ring-emerald-500/20' : 'bg-slate-400' }}"></span>
                                            <span class="font-bold text-slate-900 dark:text-white text-xs">{{ $item->name }}</span>
                                        </div>
                                    </td>

                                    <!-- Slug -->
                                    <td class="px-5 py-4 font-mono text-[11px] text-slate-500 dark:text-slate-400">
                                        {{ $item->slug }}
                                    </td>

                                    <!-- Description -->
                                    <td class="px-5 py-4 text-slate-500 dark:text-slate-400 text-xs max-w-xs truncate">
                                        {{ $item->description ?: '—' }}
                                    </td>

                                    <!-- Status Pill -->
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold {{ $isActive ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $isActive ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            
                                            <!-- Edit Button -->
                                            <button 
                                                type="button" 
                                                @click="openEditModal({{ json_encode($item) }})" 
                                                class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-950/60 dark:hover:text-emerald-400 flex items-center justify-center transition-all cursor-pointer"
                                                title="Edit Status"
                                            >
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            <!-- Delete Button -->
                                            <form method="POST" action="{{ route('admin.orders.order-status.destroy', $item->id) }}" onsubmit="return confirm('Are you sure you want to delete order status \'{{ addslashes($item->name) }}\'?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="h-8 w-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/60 dark:hover:text-rose-400 flex items-center justify-center transition-all cursor-pointer"
                                                    title="Delete Status"
                                                >
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-slate-400 italic">No order statuses configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- 2. MOBILE & TABLET CARDS VIEW (Visible on Mobile/Tablet < lg) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:hidden">
                    @forelse($statuses as $item)
                        @php
                            $isActive = ($item->status === 'active' || $item->status === '1' || $item->status === 1 || $item->status === true);
                        @endphp
                        <div 
                            class="bg-slate-50/70 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 space-y-3 flex flex-col justify-between shadow-sm"
                            x-show="!searchQuery || '{{ strtolower(addslashes($item->name . ' ' . $item->slug . ' ' . $item->description)) }}'.includes(searchQuery.toLowerCase())"
                        >
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full {{ $isActive ? 'bg-emerald-500 ring-4 ring-emerald-500/20' : 'bg-slate-400' }}"></span>
                                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $item->name }}</h3>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $isActive ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                                        {{ $isActive ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <div class="font-mono text-[11px] text-slate-400 bg-white dark:bg-slate-900 px-2.5 py-1 rounded-xl border border-slate-100 dark:border-slate-800 inline-block">
                                    {{ $item->slug }}
                                </div>

                                @if($item->description)
                                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed line-clamp-2">{{ $item->description }}</p>
                                @endif
                            </div>

                            <div class="pt-2 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-2">
                                <span class="text-[10px] text-slate-400 font-mono">{{ $item->created_at ? $item->created_at->format('M d, Y') : '' }}</span>
                                
                                <div class="flex items-center gap-2">
                                    <button 
                                        type="button" 
                                        @click="openEditModal({{ json_encode($item) }})" 
                                        class="px-3 py-1.5 rounded-xl bg-slate-200/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-emerald-600 hover:text-white text-xs font-semibold flex items-center gap-1 transition-all cursor-pointer"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('admin.orders.order-status.destroy', $item->id) }}" onsubmit="return confirm('Delete order status \'{{ addslashes($item->name) }}\'?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="px-3 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white text-xs font-semibold flex items-center gap-1 transition-all cursor-pointer border border-rose-200 dark:border-rose-800/80"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-slate-400 italic">No order statuses configured.</div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if(method_exists($statuses, 'hasPages') && $statuses->hasPages())
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $statuses->links() }}
                    </div>
                @endif

            </div>

        </div>

    </div>

    <!-- EDIT ORDER STATUS MODAL -->
    <div 
        x-show="isEditModalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity"
        @keydown.escape.window="isEditModalOpen = false"
    >
        <div 
            @click.away="isEditModalOpen = false" 
            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-2xl max-w-md w-full space-y-4 transform transition-all"
        >
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Edit Order Status</h3>
                </div>
                <button type="button" @click="isEditModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white font-bold text-sm">✕</button>
            </div>

            <form :action="'/admin/orders/order-status/' + editForm.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status Name *</label>
                    <input 
                        type="text" 
                        name="name" 
                        x-model="editForm.name" 
                        required 
                        placeholder="e.g. Processing, Shipped" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Description</label>
                    <textarea 
                        name="description" 
                        x-model="editForm.description" 
                        rows="2" 
                        placeholder="Description of this status..." 
                        class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors resize-none"
                    ></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status Flag</label>
                    <select 
                        name="status" 
                        x-model="editForm.status" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer"
                    >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button 
                        type="button" 
                        @click="isEditModalOpen = false" 
                        class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-all cursor-pointer"
                    >
                        Cancel
                    </button>

                    <button 
                        type="submit" 
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer"
                    >
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function orderStatusManager() {
    return {
        searchQuery: '',
        isEditModalOpen: false,
        editForm: {
            id: null,
            name: '',
            description: '',
            status: 'active'
        },

        openEditModal(item) {
            let statusVal = item.status;
            if (statusVal === 1 || statusVal === '1' || statusVal === true || statusVal === 'active') {
                statusVal = 'active';
            } else {
                statusVal = 'inactive';
            }

            this.editForm = {
                id: item.id,
                name: item.name || '',
                description: item.description || '',
                status: statusVal
            };
            this.isEditModalOpen = true;
        }
    };
}
</script>
@endsection
