@extends('layouts.app')
@section('title', 'Daftar Akun')

@section('content')
<div class="py-12 px-4">
    <div class="w-full max-w-lg mx-auto">

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Buat Akun Warga</h1>
            <p class="text-gray-500 text-sm mt-1">Daftarkan diri untuk menyampaikan pengaduan</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('name') ? 'border-red-300' : 'border-gray-300' }}">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- NIK --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">NIK <span class="text-red-500">*</span></label>
                    <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" placeholder="16 digit NIK KTP"
                        class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('nik') ? 'border-red-300' : 'border-gray-300' }}">
                    @error('nik')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Email & Phone --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('email') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">No. HP <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xx"
                            class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('phone') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Alamat --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="2" placeholder="Nama jalan, nomor rumah"
                        class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('address') ? 'border-red-300' : 'border-gray-300' }}">{{ old('address') }}</textarea>
                    @error('address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- RT/RW --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">RT/RW <span class="text-red-500">*</span></label>
                    <input type="text" name="rt_rw" value="{{ old('rt_rw') }}" placeholder="001/002" maxlength="7"
                        class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('rt_rw') ? 'border-red-300' : 'border-gray-300' }}">
                    @error('rt_rw')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Password --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required
                            class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('password') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 border-gray-300">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-primary-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                    Buat Akun
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-primary-600 font-medium hover:underline">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection