@extends('layouts.app')
@section('title', 'Notifikasi')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Notifikasi</h1>
        @if($notifications->isNotEmpty())
            <form method="POST" action="{{ route('warga.notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="text-xs text-gray-500 hover:text-primary-600 transition">
                    Tandai semua dibaca
                </button>
            </form>
        @endif
    </div>

    @if($notifications->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 py-16 text-center">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-gray-400 text-sm">Tidak ada notifikasi.</p>
        </div>
    @else
        <div class="space-y-2">
            @foreach($notifications as $notif)
                <div class="bg-white rounded-xl border px-5 py-4 transition
                    {{ $notif->is_read ? 'border-gray-200' : 'border-primary-200 bg-primary-50' }}">
                    <div class="flex items-start gap-3">
                        {{-- Dot unread --}}
                        <div class="mt-1.5 flex-shrink-0">
                            @if(!$notif->is_read)
                                <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                            @else
                                <div class="w-2 h-2 rounded-full bg-gray-200"></div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900">{{ $notif->title }}</div>
                            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $notif->message }}</p>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-xs text-gray-400">{{ $notif->created_at->diffForHumans() }}</span>
                                @if($notif->complaint)
                                    <a href="{{ route('warga.complaints.show', $notif->complaint) }}"
                                        class="text-xs text-primary-600 hover:underline">
                                        Lihat pengaduan →
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
@endsection