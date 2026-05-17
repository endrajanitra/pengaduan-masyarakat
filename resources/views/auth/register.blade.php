@extends('layouts.app')
@section('title', 'Daftar Akun')

@section('content')
<div class="py-12 px-4">
    <div class="w-full max-w-lg mx-auto">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-primary-600 to-primary-800 rounded-2xl shadow-lg mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Buat Akun Warga</h1>
            <p class="text-gray-500 text-sm mt-1.5">Daftarkan diri untuk menyampaikan pengaduan</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="Masukkan nama lengkap sesuai KTP"
                        class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all {{ $errors->has('name') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
                    @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- NIK --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">NIK <span class="text-red-500">*</span></label>
                    <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" placeholder="16 digit NIK sesuai KTP"
                        class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all {{ $errors->has('nik') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
                    @error('nik')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Email & Phone --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com"
                            class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
                        @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">No. HP <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xx-xxxx-xxxx"
                            class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all {{ $errors->has('phone') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
                        @error('phone')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Alamat --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="2" placeholder="Nama jalan, nomor rumah, kelurahan..."
                        class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all resize-none {{ $errors->has('address') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">{{ old('address') }}</textarea>
                    @error('address')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- RT/RW --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">RT/RW <span class="text-red-500">*</span></label>
                    <input type="text" name="rt_rw" value="{{ old('rt_rw') }}" placeholder="001/002" maxlength="7"
                        class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all {{ $errors->has('rt_rw') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
                    @error('rt_rw')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Password --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required placeholder="Min. 8 karakter"
                            class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all {{ $errors->has('password') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
                        @error('password')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi password"
                            class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all border-gray-200 bg-gray-50 hover:border-gray-300">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white py-3 rounded-xl text-sm font-semibold hover:from-primary-700 hover:to-primary-800 transition-all shadow-md hover:shadow-lg">
                    Buat Akun Sekarang
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:underline">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection
