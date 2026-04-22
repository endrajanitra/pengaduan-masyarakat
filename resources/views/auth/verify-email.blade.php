@extends('layouts.app')
@section('title', 'Verifikasi Email')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Verifikasi Email Anda</h1>
            <p class="text-gray-500 text-sm">
                Link verifikasi dikirim ke<br>
                <strong class="text-gray-800">{{ auth()->user()->email }}</strong>
            </p>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm mb-5 flex items-start gap-2">
                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-xl text-sm mb-5 flex items-start gap-2">
                <svg class="w-4 h-4 text-yellow-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if(session('resent'))
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-xl text-sm mb-5">
                Link verifikasi baru berhasil dikirim. Cek inbox Anda.
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">

            {{-- Instruksi --}}
            <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-600 space-y-2">
                <p class="font-medium text-gray-700">Tidak menemukan emailnya?</p>
                <ul class="space-y-1 text-xs list-disc list-inside text-gray-500">
                    <li>Cek folder <strong>Spam</strong> atau <strong>Promosi</strong></li>
                    <li>Pastikan email yang didaftarkan benar</li>
                    <li>Tunggu beberapa menit — pengiriman bisa sedikit lambat</li>
                    <li>Klik tombol di bawah untuk kirim ulang</li>
                </ul>
            </div>

            {{-- Kirim Ulang --}}
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="w-full bg-primary-600 text-white py-2.5 rounded-xl text-sm font-medium hover:bg-primary-700 transition focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-sm text-gray-400 hover:text-red-500 transition py-1">
                    Keluar dan gunakan email lain
                </button>
            </form>

        </div>

        {{-- Info email yang terdaftar --}}
        <p class="text-center text-xs text-gray-400 mt-4">
            Akun terdaftar dengan: <span class="font-mono">{{ auth()->user()->email }}</span>
        </p>

    </div>
</div>
@endsection