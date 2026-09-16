@extends('layouts.admin')

@section('content')
<!-- Quill.js Rich Text Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor {
        min-height: 250px;
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
    .dark .ql-stroke {
        stroke: #94a3b8 !important;
    }
    .dark .ql-fill {
        fill: #94a3b8 !important;
    }
    .dark .ql-picker {
        color: #94a3b8 !important;
    }
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

<div class="space-y-4 sm:space-y-6 max-w-5xl mx-auto pb-16 px-1 sm:px-0" x-data="blogEditForm()">
    <div class="space-y-1">
        <div class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Dashboard</a>
            <span>&gt;</span>
            <a href="{{ route('admin.blogs.index') }}" class="hover:text-emerald-600 transition-colors">Blog Posts</a>
            <span>&gt;</span>
            <span class="text-slate-800 dark:text-slate-200 font-semibold">Edit Article</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Edit Blog Post: {{ $blog->title }}</h1>
    </div>

    <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" @submit="syncQuillContent()" class="space-y-4 sm:space-y-6">
        @csrf
        @method('PUT')

        <!-- Main Details Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm space-y-4">
            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">Article Main Details</h3>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Article Title *</label>
                <input type="text" name="title" value="{{ old('title', $blog->title) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 font-semibold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Category</label>
                    <select name="blog_category_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white font-semibold">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $blog->blog_category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Author Name</label>
                    <input type="text" name="author_name" value="{{ old('author_name', $blog->author_name) }}" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white font-semibold">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Status</label>
                    <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white font-semibold">
                        <option value="published" {{ $blog->status === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ $blog->status === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
            </div>

            <!-- Local PC Base64 Image Uploader -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Featured Image (Select File from PC - Converted to Base64)</label>
                <input type="hidden" name="image" x-model="imageBase64">
                
                <div class="border-2 border-dashed border-slate-200 dark:border-slate-800 hover:border-emerald-500 rounded-2xl p-4 transition-colors">
                    <template x-if="!imageBase64">
                        <div class="flex flex-col items-center justify-center gap-2 py-4 cursor-pointer" @click="$refs.fileInput.click()">
                            <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div class="text-center">
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Click to choose image file</span>
                                <span class="text-xs text-slate-500 block">PNG, JPG, WEBP or GIF from your local computer</span>
                            </div>
                        </div>
                    </template>

                    <template x-if="imageBase64">
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <img :src="imageBase64" class="h-32 w-auto object-cover rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                            <div class="space-y-2 text-center sm:text-left">
                                <div class="text-xs font-bold text-slate-800 dark:text-slate-200">Featured Image Loaded</div>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="$refs.fileInput.click()" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200">Change Image</button>
                                    <button type="button" @click="imageBase64 = ''" class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-600 text-xs font-semibold hover:bg-rose-100">Remove</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <input type="file" x-ref="fileInput" accept="image/*" @change="handleImageFile($event)" class="hidden">
            </div>

            <!-- Short Excerpt -->
            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Short Summary / Excerpt</label>
                <textarea name="short_description" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">{{ old('short_description', $blog->short_description) }}</textarea>
            </div>

            <!-- Rich Text Editor for Content -->
            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Full Article Content (Rich Text Editor - Text Size, Colors, Links) *</label>
                
                <div id="quill-editor" class="bg-white dark:bg-slate-950">{!! old('content', $blog->content) !!}</div>
                <textarea name="content" id="blog-content-input" class="hidden" required>{!! old('content', $blog->content) !!}</textarea>
            </div>

            <div class="pt-2 flex items-center gap-2">
                <input type="checkbox" name="featured" id="featured" value="1" {{ $blog->featured ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <label for="featured" class="text-xs font-bold text-slate-700 dark:text-slate-300">Mark as Featured Story</label>
            </div>
        </div>

        <!-- SEO Metadata Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-sm space-y-4">
            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">SEO & Meta Configuration</h3>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $blog->meta_title) }}" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">{{ old('meta_description', $blog->meta_description) }}</textarea>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Meta Keywords</label>
                    <textarea name="meta_keywords" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white">{{ old('meta_keywords', $blog->meta_keywords) }}</textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5 sm:gap-3">
                <a href="{{ route('admin.blogs.index') }}" class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">Cancel</a>
                <button type="submit" class="w-full sm:w-auto text-center px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20">Update Article</button>
            </div>
        </div>
    </form>
</div>

<!-- Quill.js JS -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
let quillEditor;

function blogEditForm() {
    return {
        imageBase64: @json(old('image', $blog->image ?? '')),
        handleImageFile(event) {
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
            if (quillEditor) {
                document.getElementById('blog-content-input').value = quillEditor.root.innerHTML;
            }
        }
    };
}

document.addEventListener('DOMContentLoaded', function () {
    quillEditor = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Write your full blog article content here...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'color': [] }, { 'background': [] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'align': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image', 'video', 'blockquote', 'code-block'],
                ['clean']
            ]
        }
    });

    quillEditor.on('text-change', function() {
        document.getElementById('blog-content-input').value = quillEditor.root.innerHTML;
    });
});
</script>
@endsection
