@extends('layouts.app')
@section('title', 'Verifikasi Email')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Cek Email Anda</h1>
            <p class="text-gray-500 text-sm leading-relaxed">
                Kami mengirimkan link aktivasi ke<br>
                <strong class="text-gray-800 text-base">{{ auth()->user()->email }}</strong>
            </p>
        </div>

        {{-- Success: email terkirim --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm mb-5 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Berhasil kirim ulang --}}
        @if(session('resent'))
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-xl text-sm mb-5 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Link verifikasi baru telah dikirim. Cek inbox kamu.
            </div>
        @endif

        {{-- Error SMTP --}}
        @if($errors->has('email'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm mb-5 flex items-start gap-2">
                <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ $errors->first('email') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-5">

            {{-- Instruksi --}}
            <div class="space-y-3 text-sm text-gray-600">
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">1</div>
                    <p>Buka aplikasi email kamu dan cari pesan dari <strong>Pengaduan Desa Wangisagara</strong></p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">2</div>
                    <p>Klik tombol <strong>"Verifikasi Alamat Email"</strong> di dalam email tersebut</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">3</div>
                    <p>Akun Anda akan langsung aktif dan bisa digunakan</p>
                </div>
            </div>

            <div class="h-px bg-gray-100"></div>

            {{-- Tips --}}
            <div class="bg-amber-50 border border-amber-100 rounded-xl p-3.5">
                <p class="text-xs font-semibold text-amber-700 mb-1.5">Email tidak masuk?</p>
                <ul class="text-xs text-amber-600 space-y-1 list-disc list-inside">
                    <li>Cek folder <strong>Spam</strong> atau <strong>Promosi</strong></li>
                    <li>Tunggu 1–2 menit, kadang sedikit lambat</li>
                    <li>Pastikan ejaan email yang didaftarkan benar</li>
                </ul>
            </div>

            {{-- Tombol Kirim Ulang --}}
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="w-full border border-primary-300 text-primary-600 bg-primary-50 py-2.5 rounded-xl text-sm font-medium hover:bg-primary-100 transition">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-sm text-gray-400 hover:text-red-500 transition py-1">
                    Keluar dan daftar dengan email lain
                </button>
            </form>

        </div>

    </div>
</div>
@endsection