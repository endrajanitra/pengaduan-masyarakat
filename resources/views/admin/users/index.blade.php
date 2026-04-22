@extends('layouts.admin')
@section('title', 'Pengguna')
@section('page-title', 'Manajemen Pengguna')
@section('breadcrumb', 'Admin / Pengguna')

@section('content')

<div class="flex items-center justify-between mb-5">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, NIK..."
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 w-64">
        <select name="role" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="">Semua role</option>
            <option value="warga"       {{ request('role')=='warga'       ? 'selected':'' }}>Warga</option>
            <option value="admin_desa"  {{ request('role')=='admin_desa'  ? 'selected':'' }}>Admin Desa</option>
            <option value="kepala_desa" {{ request('role')=='kepala_desa' ? 'selected':'' }}>Kepala Desa</option>
            <option value="super_admin" {{ request('role')=='super_admin' ? 'selected':'' }}>Super Admin</option>
        </select>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">Cari</button>
        @if(request()->hasAny(['search','role']))
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-400 hover:text-gray-600 py-2">Reset</a>
        @endif
    </form>
    <a href="{{ route('admin.users.create') }}"
        class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Staf
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                <th class="text-left px-5 py-3 font-medium">Pengguna</th>
                <th class="text-left px-4 py-3 font-medium">NIK</th>
                <th class="text-left px-4 py-3 font-medium">Role</th>
                <th class="text-center px-4 py-3 font-medium">Status</th>
                <th class="text-left px-4 py-3 font-medium">Terdaftar</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3.5">
                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                        <div class="text-xs text-gray-400">{{ $user->email }}</div>
                    </td>
                    <td class="px-4 py-3.5 text-xs text-gray-500 font-mono">{{ $user->nik ?? '-' }}</td>
                    <td class="px-4 py-3.5">
                        @php
                            $roleColors = [
                                'warga'       => 'bg-gray-100 text-gray-600',
                                'admin_desa'  => 'bg-blue-100 text-blue-700',
                                'kepala_desa' => 'bg-green-100 text-green-700',
                                'super_admin' => 'bg-purple-100 text-purple-700',
                            ];
                        @endphp
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ str_replace('_', ' ', ucfirst($user->role)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @if($user->is_active)
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Aktif</span>
                        @else
                            <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-xs text-gray-400">{{ $user->created_at->translatedFormat('d M Y') }}</td>
                    <td class="px-4 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-xs text-primary-600 hover:underline">Edit</a>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs {{ $user->is_active ? 'text-red-400 hover:text-red-600' : 'text-green-500 hover:text-green-700' }} transition">
                                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">Tidak ada pengguna ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if($users->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $users->links() }}</div>
    @endif
</div>
@endsection