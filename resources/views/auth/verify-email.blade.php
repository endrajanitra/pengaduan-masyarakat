@extends('layouts.app')
@section('title', 'Verifikasi Email')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md text-center">

        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Verifikasi Email Anda</h1>
        <p class="text-gray-500 text-sm mb-6">
            Kami telah mengirimkan link verifikasi ke <strong>{{ auth()->user()->email }}</strong>.
            Silakan cek inbox atau folder spam Anda.
        </p>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm mb-5">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm space-y-3">
            <p class="text-sm text-gray-600">Tidak menerima email?</p>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="w-full bg-primary-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-400 hover:text-red-500 transition">
                    Keluar dari akun ini
                </button>
            </form>
        </div>

    </div>
</div>
@endsection