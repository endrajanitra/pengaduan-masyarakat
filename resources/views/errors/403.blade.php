@extends('layouts.app')
@section('title', '403 — Akses Ditolak')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4">
    <div class="text-center max-w-md">
        <div class="text-6xl font-bold text-gray-200 mb-4">403</div>
        <h1 class="text-xl font-bold text-gray-800 mb-2">Akses Ditolak</h1>
        <p class="text-gray-500 text-sm mb-6">{{ $message ?? 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}</p>
        <a href="{{ url()->previous() }}" class="text-sm text-primary-600 hover:underline mr-4">← Kembali</a>
        <a href="{{ route('home') }}" class="text-sm bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">Ke Beranda</a>
    </div>
</div>
@endsection