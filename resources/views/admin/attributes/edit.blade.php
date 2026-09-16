@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 pb-16">

    @php
        $rawValues = old('values', $attribute->values);
        if (is_string($rawValues)) {
            $decoded = json_decode($rawValues, true);
            $existingValues = is_array($decoded) ? $decoded : (array_filter(explode(',', $rawValues)) ?: []);
        } elseif (is_array($rawValues)) {
            $existingValues = $rawValues;
        } else {
            $existingValues = [];
        }
        $existingValues = array_values(array_filter(array_map(function($v) {
            return is_string($v) ? trim($v) : $v;
        }, (array) $existingValues)));
    @endphp

    <!-- Top Breadcrumbs & Page Header -->
    <div class="space-y-1">
        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <a href="{{ route('admin.attributes.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Attributes</a>
            <span>&gt;</span>
            <span class="text-slate-800 dark:text-slate-200 font-semibold">Edit Attribute</span>
        </div>
        <div class="flex items-center justify-between pt-1">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Attribute: {{ $attribute->name }}</h1>
            <a href="{{ route('admin.attributes.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to Attributes
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-medium space-y-1">
            <div class="font-bold flex items-center gap-2">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                Please fix the following errors:
            </div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 shadow-sm">
        <form method="POST" action="{{ route('admin.attributes.update', $attribute->id) }}" class="space-y-5" x-data="attributeEditForm()" @submit="beforeSubmit()">
            @csrf
            @method('PUT')

            <!-- Attribute Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Attribute Name *</label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name', $attribute->name) }}" 
                    required 
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors"
                >
            </div>

            <!-- Attribute Values -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Values *</label>
                <div class="flex gap-2 mb-2.5">
                    <input 
                        type="text" 
                        x-model="tagInput" 
                        @keydown.enter.prevent="addTag()" 
                        placeholder="Type value and click Add" 
                        class="flex-1 px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                    >
                    <button 
                        type="button" 
                        @click="addTag()" 
                        class="px-4 py-2.5 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-sm"
                    >
                        Add Value
                    </button>
                </div>

                <!-- Hidden inputs for form submit -->
                <template x-for="(val, idx) in values" :key="idx">
                    <input type="hidden" name="values[]" :value="val">
                </template>

                <!-- Value tags display -->
                <div class="flex flex-wrap gap-2 p-3.5 bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-2xl min-h-[56px] items-center">
                    <template x-if="values.length === 0">
                        <span class="text-xs text-slate-400 italic">No values configured. Type a value above and click Add.</span>
                    </template>
                    <template x-for="(val, idx) in values" :key="idx">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs font-semibold text-emerald-700 dark:text-emerald-300 shadow-sm">
                            <span x-text="val"></span>
                            <button type="button" @click="removeTag(idx)" class="text-emerald-500 hover:text-rose-500 font-bold ml-0.5 cursor-pointer text-sm leading-none">&times;</button>
                        </span>
                    </template>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                <a 
                    href="{{ route('admin.attributes.index') }}" 
                    class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-md shadow-rose-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer"
                >
                    Update Attribute
                </button>
            </div>
        </form>
    </div>

</div>

<script>
function attributeEditForm() {
    return {
        tagInput: '',
        values: @json($existingValues),
        addTag() {
            const val = this.tagInput.trim();
            if (val && !this.values.includes(val)) {
                this.values.push(val);
                this.tagInput = '';
            }
        },
        removeTag(idx) {
            this.values.splice(idx, 1);
        },
        beforeSubmit() {
            const val = this.tagInput.trim();
            if (val && !this.values.includes(val)) {
                this.values.push(val);
                this.tagInput = '';
            }
        }
    };
}
</script>
@endsection
