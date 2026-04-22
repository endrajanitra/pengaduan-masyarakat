@extends('layouts.admin')
@section('title', 'Pengaturan Situs')
@section('page-title', 'Pengaturan Situs')
@section('breadcrumb', 'Admin / Pengaturan')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100 pb-2">Identitas Desa</div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Desa <span class="text-red-500">*</span></label>
                    <input type="text" name="desa_name" value="{{ old('desa_name', $settings->get('desa_name')?->value) }}" required
                        class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('desa_name') ? 'border-red-300' : 'border-gray-300' }}">
                    @error('desa_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kepala Desa <span class="text-red-500">*</span></label>
                    <input type="text" name="kepala_desa" value="{{ old('kepala_desa', $settings->get('kepala_desa')?->value) }}" required
                        class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('kepala_desa') ? 'border-red-300' : 'border-gray-300' }}">
                    @error('kepala_desa')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kecamatan <span class="text-red-500">*</span></label>
                    <input type="text" name="desa_kecamatan" value="{{ old('desa_kecamatan', $settings->get('desa_kecamatan')?->value) }}" required
                        class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kabupaten <span class="text-red-500">*</span></label>
                    <input type="text" name="desa_kabupaten" value="{{ old('desa_kabupaten', $settings->get('desa_kabupaten')?->value) }}" required
                        class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Provinsi <span class="text-red-500">*</span></label>
                    <input type="text" name="desa_provinsi" value="{{ old('desa_provinsi', $settings->get('desa_provinsi')?->value) }}" required
                        class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 border-gray-300">
                </div>
            </div>

            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100 pb-2 pt-2">Kontak & Informasi</div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Kantor <span class="text-red-500">*</span></label>
                <textarea name="alamat_kantor" rows="2" required
                    class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('alamat_kantor', $settings->get('alamat_kantor')?->value) }}</textarea>
                @error('alamat_kantor')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Telepon Kantor</label>
                    <input type="text" name="phone_kantor" value="{{ old('phone_kantor', $settings->get('phone_kantor')?->value) }}"
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Kantor</label>
                    <input type="email" name="email_kantor" value="{{ old('email_kantor', $settings->get('email_kantor')?->value) }}"
                        class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('email_kantor')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Aplikasi</label>
                <textarea name="app_description" rows="2"
                    class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('app_description', $settings->get('app_description')?->value) }}</textarea>
            </div>

            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100 pb-2 pt-2">Logo Desa</div>

            <div class="flex items-center gap-5">
                @if($siteLogo)
                    <img src="{{ $siteLogo }}" alt="Logo" class="h-16 w-16 object-contain rounded-lg border border-gray-200">
                @else
                    <div class="h-16 w-16 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs">No Logo</div>
                @endif
                <div class="flex-1">
                    <input type="file" name="logo" accept=".png,.jpg,.jpeg"
                        class="w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    <p class="text-xs text-gray-400 mt-1">PNG/JPG, maksimal 2 MB. Kosongkan jika tidak ingin mengubah logo.</p>
                    @error('logo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="bg-primary-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection