@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-primary-600 to-primary-800 rounded-2xl shadow-lg mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Selamat Datang</h1>
            <p class="text-gray-500 text-sm mt-1.5">Masuk ke Sistem Pengaduan {{ $siteName }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="nama@email.com"
                        class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-400 transition-all
                               {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-semibold text-gray-700">Password</label>
                    </div>
                    <input type="password" name="password" required
                        placeholder="••••••••"
                        class="w-full px-4 py-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-400 transition-all
                               {{ $errors->has('password') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember"
                        class="h-4 w-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                    <label for="remember" class="text-sm text-gray-600 cursor-pointer">Ingat saya</label>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white py-3 rounded-xl text-sm font-semibold hover:from-primary-700 hover:to-primary-800 transition-all shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Masuk ke Akun
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-primary-600 font-semibold hover:text-primary-700 hover:underline">Daftar sekarang</a>
        </p>

    </div>
</div>
@endsection
