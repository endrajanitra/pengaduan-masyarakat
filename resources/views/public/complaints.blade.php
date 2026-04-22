@extends('layouts.app')
@section('title', 'Pengaduan Publik')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Pengaduan Masyarakat</h1>
        <p class="text-sm text-gray-500 mt-1">Seluruh pengaduan yang telah dipublikasikan oleh warga {{ $siteName }}.</p>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('public.complaints') }}" class="bg-white rounded-xl border border-gray-200 p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs text-gray-500 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul atau nomor tiket..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div class="w-44">
            <label class="block text-xs text-gray-500 mb-1">Kategori</label>
            <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Semua kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-44">
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Semua status</option>
                @foreach(\App\Models\Complaint::STATUS_LABELS as $val => $label)
                    <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">
            Filter
        </button>
        @if(request()->hasAny(['search','category','status']))
            <a href="{{ route('public.complaints') }}" class="text-sm text-gray-400 hover:text-gray-600 py-2">Reset</a>
        @endif
    </form>

    {{-- Daftar --}}
    @if($complaints->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Tidak ada pengaduan yang sesuai filter.
        </div>
    @else
        <div class="space-y-3">
            @foreach($complaints as $complaint)
                <a href="{{ route('public.complaints.show', $complaint->complaint_number) }}"
                    class="bg-white rounded-xl border border-gray-200 p-5 flex gap-4 hover:shadow-sm hover:border-primary-200 transition block">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <x-status-badge :status="$complaint->status"/>
                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $complaint->category->name ?? '-' }}</span>
                            <span class="text-xs font-mono text-gray-400">{{ $complaint->complaint_number }}</span>
                        </div>
                        <h2 class="text-sm font-semibold text-gray-900 truncate">{{ $complaint->title }}</h2>
                        <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $complaint->description }}</p>
                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                            <span>{{ $complaint->reporter_name }}</span>
                            <span>&bull;</span>
                            <span>{{ $complaint->location }}</span>
                            <span>&bull;</span>
                            <span>{{ $complaint->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 flex-shrink-0 self-center" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $complaints->links() }}
        </div>
    @endif

</div>
@endsection