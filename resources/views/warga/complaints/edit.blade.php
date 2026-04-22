@extends('layouts.app')
@section('title', 'Edit Pengaduan')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

    <div class="mb-6">
        <a href="{{ route('warga.complaints.show', $complaint) }}" class="text-sm text-gray-500 hover:text-primary-600">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Pengaduan</h1>
        <p class="text-xs text-gray-400 mt-1 font-mono">{{ $complaint->complaint_number }}</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm">
        <form method="POST" action="{{ route('warga.complaints.update', $complaint) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Kategori --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                <select name="category_id" required
                    class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('category_id') ? 'border-red-300' : 'border-gray-300' }}">
                    <option value="">-- Pilih kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $complaint->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Judul --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $complaint->title) }}" required
                    class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('title') ? 'border-red-300' : 'border-gray-300' }}">
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Lokasi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Lokasi Kejadian <span class="text-red-500">*</span></label>
                <input type="text" name="location" value="{{ old('location', $complaint->location) }}" required
                    class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('location') ? 'border-red-300' : 'border-gray-300' }}">
                @error('location')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" required
                    class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('description') ? 'border-red-300' : 'border-gray-300' }}">{{ old('description', $complaint->description) }}</textarea>
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Opsi Privasi --}}
            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                <div class="text-sm font-medium text-gray-700">Opsi Privasi</div>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_anonymous" value="1" {{ old('is_anonymous', $complaint->is_anonymous) ? 'checked' : '' }}
                        class="mt-0.5 h-4 w-4 text-primary-600 rounded border-gray-300">
                    <div>
                        <div class="text-sm font-medium text-gray-700">Kirim secara anonim</div>
                        <div class="text-xs text-gray-500">Identitas Anda tidak akan ditampilkan kepada publik.</div>
                    </div>
                </label>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_public" value="1" {{ old('is_public', $complaint->is_public) ? 'checked' : '' }}
                        class="mt-0.5 h-4 w-4 text-primary-600 rounded border-gray-300">
                    <div>
                        <div class="text-sm font-medium text-gray-700">Tampilkan di halaman publik</div>
                    </div>
                </label>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('warga.complaints.show', $complaint) }}"
                    class="flex-1 text-center py-2.5 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 bg-primary-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection