@extends('layouts.admin')

@section('content')
<!-- Quill.js Rich Text Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor {
        min-height: 220px;
        font-size: 14px;
        background-color: transparent;
    }
    .dark .ql-toolbar {
        background-color: #0f172a;
        border-color: #1e293b !important;
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
    }
    .dark .ql-container {
        border-color: #1e293b !important;
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
        color: #f8fafc;
    }
    .dark .ql-stroke { stroke: #94a3b8 !important; }
    .dark .ql-fill { fill: #94a3b8 !important; }
    .dark .ql-picker { color: #94a3b8 !important; }
    .ql-toolbar {
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
        background-color: #f8fafc;
        border-color: #e2e8f0 !important;
    }
    .ql-container {
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
        border-color: #e2e8f0 !important;
    }
</style>

<div class="space-y-4 sm:space-y-6 max-w-4xl mx-auto pb-16 px-1 sm:px-0" x-data="serviceEditForm()">
    <!-- Header & Breadcrumbs -->
    <div class="space-y-1">
        <div class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <a href="{{ route('admin.services.index') }}" class="hover:text-emerald-600 transition-colors">Our Services</a>
            <span>&gt;</span>
            <span class="text-slate-800 dark:text-slate-200 font-semibold">Edit Service</span>
        </div>
        <div class="flex items-center justify-between">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Edit: {{ $service->title }}</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.services.show', $service->id) }}" class="text-xs font-semibold px-3 py-1.5 bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 rounded-xl hover:bg-blue-100 transition-all">
                    View Details
                </a>
                <a href="{{ route('admin.services.index') }}" class="text-xs font-semibold px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-200 transition-all">
                    ← Back
                </a>
            </div>
        </div>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs font-semibold space-y-1">
            <div class="font-bold flex items-center gap-2">
                <svg class="h-4 w-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Please resolve the following errors:
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700 dark:text-rose-300">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data" @submit="syncQuillContent()" class="space-y-4 sm:space-y-6">
        @csrf
        @method('PUT')

        <!-- Main Service Details Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm space-y-4">
            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>🛠️</span> General Information
            </h3>

            <!-- Title & Slug -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Service Title *</label>
                    <input type="text" name="title" value="{{ old('title', $service->title) }}" required placeholder="e.g. Doorstep Computer Repair" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 font-semibold">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Custom Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $service->slug) }}" placeholder="auto-generated-from-title" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white font-mono">
                </div>
            </div>

            <!-- Icon & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Service Icon (Emoji or Icon Code)</label>
                    <div class="flex items-center gap-2.5">
                        <input type="text" name="icon" x-model="selectedIcon" placeholder="e.g. 🛠️" class="w-24 px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white text-center font-bold">
                        <div class="flex flex-wrap items-center gap-1">
                            <template x-for="ico in iconPresets" :key="ico">
                                <button type="button" @click="selectedIcon = ico" class="h-7 w-7 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-100 hover:text-emerald-700 text-xs flex items-center justify-center transition-all" x-text="ico"></button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Status *</label>
                    <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white font-semibold">
                        <option value="active" {{ old('status', $service->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $service->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Single Image Upload Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm space-y-4">
            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>🖼️</span> Service Media & Image
            </h3>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Service Image</label>
                <input type="hidden" name="image" x-model="imageBase64">
                
                <div class="border-2 border-dashed border-slate-200 dark:border-slate-800 hover:border-emerald-500 rounded-2xl p-4 sm:p-6 transition-colors">
                    <template x-if="!imageBase64">
                        <div class="flex flex-col items-center justify-center gap-2 py-4 cursor-pointer" @click="$refs.imageInput.click()">
                            <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div class="text-center">
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Choose Image File</span>
                                <span class="text-[11px] text-slate-400 block mt-0.5">PNG, JPG, WEBP from your computer</span>
                            </div>
                        </div>
                    </template>

                    <template x-if="imageBase64">
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <img :src="imageBase64" class="h-28 w-auto max-w-xs object-cover rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                            <div class="space-y-2 text-center sm:text-left">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Image Selected</span>
                                <div class="flex items-center gap-2 justify-center sm:justify-start">
                                    <button type="button" @click="$refs.imageInput.click()" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200">Change Image</button>
                                    <button type="button" @click="imageBase64 = ''" class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-600 text-xs font-semibold hover:bg-rose-100">Remove</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <input type="file" name="image_file" x-ref="imageInput" accept="image/*" @change="handleFileChange($event)" class="hidden">
            </div>
        </div>

        <!-- Descriptions & Rich Text Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm space-y-4">
            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>📝</span> Service Description & Details
            </h3>

            <!-- Short Overview -->
            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Short Summary (Shown in cards & listings)</label>
                <textarea name="short_description" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white" placeholder="Quick 1-2 sentence overview of what this service offers...">{{ old('short_description', $service->short_description) }}</textarea>
            </div>

            <!-- Full Rich Text Editor -->
            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Full Service Details (Rich Text Content)</label>
                <div id="service-edit-quill-editor" class="bg-white dark:bg-slate-950"></div>
                <textarea name="description" id="service-edit-description-input" class="hidden">{!! old('description', $service->description) !!}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5 sm:gap-3">
                <a href="{{ route('admin.services.index') }}" class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">Cancel</a>
                <button type="submit" class="w-full sm:w-auto text-center px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20">
                    Update Service
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Quill.js JS -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
let serviceEditQuill;

function serviceEditForm() {
    return {
        selectedIcon: @json($service->icon ?: '🛠️'),
        iconPresets: ['🛠️', '🚀', '🚚', '⚡', '🛡️', '📦', '💻', '⚙️', '🧹', '🎨', '🌐', '🔧'],
        imageBase64: @json($service->image ?: ''),
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imageBase64 = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        syncQuillContent() {
            if (serviceEditQuill) {
                document.getElementById('service-edit-description-input').value = serviceEditQuill.root.innerHTML;
            }
        }
    };
}

document.addEventListener('DOMContentLoaded', function () {
    serviceEditQuill = new Quill('#service-edit-quill-editor', {
        theme: 'snow',
        placeholder: 'Write comprehensive service details...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, false] }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'color': [] }, { 'background': [] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'align': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image', 'blockquote', 'code-block'],
                ['clean']
            ]
        }
    });

    const initialContent = `{!! addslashes($service->description ?? '') !!}`;
    if (initialContent) {
        serviceEditQuill.root.innerHTML = initialContent;
    }

    serviceEditQuill.on('text-change', function() {
        document.getElementById('service-edit-description-input').value = serviceEditQuill.root.innerHTML;
    });
});
</script>
@endsection
