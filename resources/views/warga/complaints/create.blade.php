@extends('layouts.app')
@section('title', 'Buat Pengaduan')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-10">

    <div class="mb-7">
        <a href="{{ route('warga.complaints.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary-600 transition-colors font-medium mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Buat Pengaduan Baru</h1>
        <p class="text-sm text-gray-500 mt-1.5">Jelaskan masalah Anda dengan detail agar dapat segera ditindaklanjuti.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('warga.complaints.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                <select name="category_id" required
                    class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all {{ $errors->has('category_id') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
                    <option value="">— Pilih kategori —</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Pengaduan <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    placeholder="Contoh: Jalan rusak parah di RT 03 dekat masjid"
                    class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all {{ $errors->has('title') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
                @error('title')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi Kejadian <span class="text-red-500">*</span></label>
                <input type="text" name="location" value="{{ old('location') }}" required
                    placeholder="Contoh: RT 03 RW 01, dekat Masjid Al-Ikhlas"
                    class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all {{ $errors->has('location') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
                @error('location')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" required
                    placeholder="Jelaskan masalah secara detail: kapan terjadi, seberapa parah, dampak yang dirasakan warga..."
                    class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all resize-none {{ $errors->has('description') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Lampiran Foto/Dokumen</label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 hover:border-primary-300 transition-colors bg-gray-50/50">
                    <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                        class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-2">Maks. 5 file, masing-masing maks. 5 MB. Format: JPG, PNG, PDF, DOC.</p>
                </div>
                @error('attachments')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="bg-blue-50/60 border border-blue-100 rounded-xl p-5 space-y-3">
                <div class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Opsi Privasi
                </div>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_anonymous" value="1" {{ old('is_anonymous') ? 'checked' : '' }}
                        class="mt-0.5 h-4 w-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                    <div>
                        <div class="text-sm font-semibold text-gray-700">Kirim secara anonim</div>
                        <div class="text-xs text-gray-500 mt-0.5">Identitas Anda tidak akan ditampilkan kepada publik. Admin tetap dapat melihat identitas Anda.</div>
                    </div>
                </label>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_public" value="1" {{ old('is_public', true) ? 'checked' : '' }}
                        class="mt-0.5 h-4 w-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                    <div>
                        <div class="text-sm font-semibold text-gray-700">Tampilkan di halaman publik</div>
                        <div class="text-xs text-gray-500 mt-0.5">Pengaduan ini bisa dilihat masyarakat umum sebagai bentuk transparansi.</div>
                    </div>
                </label>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('warga.complaints.index') }}"
                    class="flex-1 text-center py-3 border border-gray-300 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 bg-gradient-to-r from-primary-600 to-primary-700 text-white py-3 rounded-xl text-sm font-semibold hover:from-primary-700 hover:to-primary-800 transition-all shadow-md">
                    Kirim Pengaduan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
