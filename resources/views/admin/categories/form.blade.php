@extends('layouts.admin')
@section('title', isset($category) ? 'Edit Kategori' : 'Tambah Kategori')
@section('page-title', isset($category) ? 'Edit Kategori' : 'Tambah Kategori')
@section('breadcrumb', 'Admin / Kategori / ' . (isset($category) ? 'Edit' : 'Tambah'))

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <form method="POST"
            action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
            class="space-y-5">
            @csrf
            @if(isset($category)) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required
                    class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('name') ? 'border-red-300' : 'border-gray-300' }}">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ikon</label>
                <input type="text" name="icon" value="{{ old('icon', $category->icon ?? '') }}"
                    placeholder="contoh: heroicon-o-map"
                    class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <p class="text-xs text-gray-400 mt-1">Nama class ikon Heroicons (opsional).</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3"
                    class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('description', $category->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Urutan Tampil</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0"
                    class="w-32 px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <p class="text-xs text-gray-400 mt-1">Angka lebih kecil ditampilkan lebih dulu.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.categories.index') }}"
                    class="flex-1 text-center py-2.5 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 bg-primary-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                    {{ isset($category) ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection