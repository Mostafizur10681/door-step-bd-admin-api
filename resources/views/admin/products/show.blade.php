@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Top Bar & Breadcrumbs -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <x-breadcrumbs :items="[
                ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['label' => 'Products', 'url' => route('admin.products.index')],
                ['label' => $product->name]
            ]" />
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white mt-1">{{ $product->name }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 transition-all">
                Edit Product
            </a>
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-xs font-bold rounded-xl">
                Back to List
            </a>
        </div>
    </div>

    <!-- Product Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Images & Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Image Gallery -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Product Images</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @if($product->image)
                        <div class="h-36 rounded-xl overflow-hidden border-2 border-primary bg-gray-50 flex items-center justify-center relative">
                            <img src="{{ str_starts_with($product->image, 'data:image') || str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}" class="h-full w-full object-cover">
                            <span class="absolute top-1.5 left-1.5 bg-primary text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">Primary</span>
                        </div>
                    @endif
                    @foreach($product->images as $img)
                        @if($img->image_path !== $product->image)
                            <div class="h-36 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50">
                                <img src="{{ str_starts_with($img->image_path, 'data:image') || str_starts_with($img->image_path, 'http') ? $img->image_path : asset('storage/' . $img->image_path) }}" class="h-full w-full object-cover">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Product Description</h3>
                @if($product->short_description)
                    <div>
                        <h4 class="text-xs font-bold text-gray-500 mb-1">Short Description</h4>
                        <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">{{ $product->short_description }}</p>
                    </div>
                @endif
                <div>
                    <h4 class="text-xs font-bold text-gray-500 mb-1">Full Description</h4>
                    <div class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                        {{ $product->description ?: 'No detailed description provided.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Summary Stats & Attributes -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-3.5 text-xs">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">Overview</h3>

                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Price</span>
                    <span class="font-bold text-gray-900 dark:text-white text-base">৳{{ number_format($product->price, 2) }}</span>
                </div>

                @if($product->sale_price)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Sale Price</span>
                        <span class="font-bold text-emerald-600">৳{{ number_format($product->sale_price, 2) }}</span>
                    </div>
                @endif

                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Stock Quantity</span>
                    <span class="font-bold {{ $product->stock > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $product->stock }} {{ $product->unit ?: 'pcs' }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-500">SKU</span>
                    <span class="font-mono font-bold">{{ $product->sku ?: 'N/A' }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Category</span>
                    <span class="font-semibold">{{ $product->category->name ?? 'Uncategorized' }}</span>
                </div>

                @if($product->subCategory)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Sub Category</span>
                        <span class="font-semibold">{{ $product->subCategory->name }}</span>
                    </div>
                @endif

                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Status</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $product->status === 'active' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-700' }}">
                        {{ ucfirst($product->status) }}
                    </span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
