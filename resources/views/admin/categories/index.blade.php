@extends('layouts.admin')
@section('title', 'Kategori')
@section('page-title', 'Kategori Pengaduan')
@section('breadcrumb', 'Admin / Kategori')

@section('content')

<div class="flex justify-end mb-5">
    @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('admin.categories.create') }}"
            class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kategori
        </a>
    @endif
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                <th class="text-left px-5 py-3 font-medium">Kategori</th>
                <th class="text-left px-4 py-3 font-medium">Deskripsi</th>
                <th class="text-center px-4 py-3 font-medium">Urutan</th>
                <th class="text-center px-4 py-3 font-medium">Total Pengaduan</th>
                <th class="text-center px-4 py-3 font-medium">Status</th>
                @if(auth()->user()->isSuperAdmin())
                    <th class="px-4 py-3"></th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($categories as $category)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3.5">
                        <div class="font-medium text-gray-900">{{ $category->name }}</div>
                        <div class="text-xs text-gray-400 font-mono">{{ $category->slug }}</div>
                    </td>
                    <td class="px-4 py-3.5 text-xs text-gray-500 max-w-xs truncate">{{ $category->description ?? '-' }}</td>
                    <td class="px-4 py-3.5 text-center text-xs text-gray-500">{{ $category->sort_order }}</td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="font-semibold text-gray-800">{{ $category->complaints_count }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @if($category->is_active)
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Aktif</span>
                        @else
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Nonaktif</span>
                        @endif
                    </td>
                    @if(auth()->user()->isSuperAdmin())
                        <td class="px-4 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                    class="text-xs text-primary-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.categories.toggle-active', $category) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs {{ $category->is_active ? 'text-red-400 hover:text-red-600' : 'text-green-500 hover:text-green-700' }} transition">
                                        {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">Belum ada kategori.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection