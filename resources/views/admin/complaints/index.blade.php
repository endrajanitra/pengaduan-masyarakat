@extends('layouts.admin')
@section('title', 'Daftar Pengaduan')
@section('page-title', 'Pengaduan')
@section('breadcrumb', 'Admin / Pengaduan')

@section('content')

{{-- Filter --}}
<form method="GET" action="{{ route('admin.complaints.index') }}"
    class="bg-white rounded-xl border border-gray-200 p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[160px]">
        <label class="block text-xs text-gray-500 mb-1">Cari</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul atau nomor tiket..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>
    <div class="w-36">
        <label class="block text-xs text-gray-500 mb-1">Status</label>
        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="">Semua status</option>
            @foreach(\App\Models\Complaint::STATUS_LABELS as $val => $label)
                <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-36">
        <label class="block text-xs text-gray-500 mb-1">Prioritas</label>
        <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="">Semua prioritas</option>
            @foreach(\App\Models\Complaint::PRIORITY_LABELS as $val => $label)
                <option value="{{ $val }}" {{ request('priority') == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
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
    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">Filter</button>
    @if(request()->hasAny(['search','status','priority','category']))
        <a href="{{ route('admin.complaints.index') }}" class="text-sm text-gray-400 hover:text-gray-600 py-2">Reset</a>
    @endif
</form>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                <th class="text-left px-5 py-3 font-medium">Pengaduan</th>
                <th class="text-left px-4 py-3 font-medium">Kategori</th>
                <th class="text-left px-4 py-3 font-medium">Status</th>
                <th class="text-left px-4 py-3 font-medium">Pelapor</th>
                <th class="text-left px-4 py-3 font-medium">Tanggal</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($complaints as $complaint)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3.5">
                        <div class="font-medium text-gray-900 truncate max-w-[220px]">{{ $complaint->title }}</div>
                        <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $complaint->complaint_number }}</div>
                    </td>
                    <td class="px-4 py-3.5 text-xs text-gray-500">{{ $complaint->category->name ?? '-' }}</td>
                    <td class="px-4 py-3.5">
                        <x-status-badge :status="$complaint->status" :priority="$complaint->priority"/>
                    </td>
                    <td class="px-4 py-3.5 text-xs text-gray-600">{{ $complaint->reporter_name }}</td>
                    <td class="px-4 py-3.5 text-xs text-gray-400">{{ $complaint->created_at->translatedFormat('d M Y') }}</td>
                    <td class="px-4 py-3.5 text-right">
                        <a href="{{ route('admin.complaints.show', $complaint) }}"
                            class="text-xs text-primary-600 hover:text-primary-700 font-medium">Detail →</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">
                        Tidak ada pengaduan yang sesuai filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($complaints->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $complaints->links() }}
        </div>
    @endif
</div>

@endsection