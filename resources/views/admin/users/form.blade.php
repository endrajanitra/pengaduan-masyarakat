@extends('layouts.admin')
@section('title', isset($user) ? 'Edit Pengguna' : 'Tambah Staf')
@section('page-title', isset($user) ? 'Edit Pengguna' : 'Tambah Staf Desa')
@section('breadcrumb', 'Admin / Pengguna / ' . (isset($user) ? 'Edit' : 'Tambah'))

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <form method="POST"
            action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}"
            class="space-y-5">
            @csrf
            @if(isset($user)) @method('PUT') @endif

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                    class="w-full px-3.5 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('name') ? 'border-red-300' : 'border-gray-300' }}">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                    class="w-full px-3.5 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('email') ? 'border-red-300' : 'border-gray-300' }}">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                <select name="role" required
                    class="w-full px-3.5 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('role') ? 'border-red-300' : 'border-gray-300' }}">
                    @if(isset($user))
                        <option value="warga"       {{ old('role', $user->role)=='warga'       ? 'selected':'' }}>Warga</option>
                    @endif
                    <option value="admin_desa"  {{ old('role', $user->role ?? '')=='admin_desa'  ? 'selected':'' }}>Admin Desa</option>
                    <option value="kepala_desa" {{ old('role', $user->role ?? '')=='kepala_desa' ? 'selected':'' }}>Kepala Desa</option>
                    <option value="super_admin" {{ old('role', $user->role ?? '')=='super_admin' ? 'selected':'' }}>Super Admin</option>
                </select>
                @error('role')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">No. HP</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                    class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            @if(!isset($user))
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required
                            class="w-full px-3.5 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('password') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            @endif

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.users.index') }}"
                    class="flex-1 text-center py-2.5 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 bg-gradient-to-r from-primary-600 to-primary-700 text-white py-2.5 rounded-xl text-sm font-semibold hover:from-primary-700 hover:to-primary-800 transition-all shadow-sm">
                    {{ isset($user) ? 'Simpan Perubahan' : 'Tambah Staf' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection