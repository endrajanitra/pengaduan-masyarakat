@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Masuk ke Akun Anda</h1>
            <p class="text-gray-500 text-sm mt-1">Sistem Pengaduan {{ $siteName }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent
                               {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-gray-300' }}">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                    </div>
                    <input type="password" name="password" required
                        class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent
                               {{ $errors->has('password') ? 'border-red-300 bg-red-50' : 'border-gray-300' }}">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-primary-600 rounded border-gray-300">
                    <label for="remember" class="text-sm text-gray-600">Ingat saya</label>
                </div>

                <button type="submit"
                    class="w-full bg-primary-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-primary-700 transition focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-primary-600 font-medium hover:underline">Daftar sekarang</a>
        </p>

    </div>
</div>
@endsection