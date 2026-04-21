@extends('layouts.admin')
@section('title', $complaint->complaint_number)
@section('page-title', 'Detail Pengaduan')
@section('breadcrumb', 'Admin / Pengaduan / ' . $complaint->complaint_number)

@section('content')
<div class="grid grid-cols-3 gap-6">

    {{-- Kolom Kiri: Detail Utama --}}
    <div class="col-span-2 space-y-5">

        {{-- Header --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <x-status-badge :status="$complaint->status" :priority="$complaint->priority"/>
                <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full">{{ $complaint->category->name }}</span>
                @if($complaint->is_anonymous)
                    <span class="text-xs bg-purple-100 text-purple-700 px-2.5 py-0.5 rounded-full">Anonim</span>
                @endif
            </div>
            <h1 class="text-lg font-bold text-gray-900 mb-1">{{ $complaint->title }}</h1>
            <div class="text-xs text-gray-400 mb-4">
                #{{ $complaint->complaint_number }} &bull; {{ $complaint->created_at->translatedFormat('d F Y, H:i') }} &bull; Lokasi: {{ $complaint->location }}
            </div>
            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $complaint->description }}</p>

            @if($complaint->attachments->isNotEmpty())
                <div class="mt-5 pt-5 border-t border-gray-100">
                    <div class="text-xs font-semibold text-gray-500 mb-2">Lampiran</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($complaint->attachments as $att)
                            <a href="{{ $att->url }}" target="_blank"
                                class="text-xs text-primary-600 bg-primary-50 border border-primary-100 px-3 py-1.5 rounded-lg hover:bg-primary-100 transition flex items-center gap-1.5">
                                📎 {{ $att->file_name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($complaint->admin_response)
                <div class="mt-5 pt-5 border-t border-gray-100">
                    <div class="text-xs font-semibold text-gray-500 mb-2">Tanggapan Resmi</div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-900">
                        {{ $complaint->admin_response }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Komentar --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Komentar & Catatan Internal</h2>

            @forelse($complaint->comments as $comment)
                <div class="flex gap-3 mb-4 pb-4 border-b border-gray-100 last:border-0 last:mb-0 last:pb-0">
                    <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold
                        {{ $comment->is_internal ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-medium text-gray-800">{{ $comment->user->name ?? '-' }}</span>
                            @if($comment->is_internal)
                                <span class="text-xs bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded">Internal</span>
                            @else
                                <span class="text-xs bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded">Publik</span>
                            @endif
                            <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>

                            @if($comment->user_id === auth()->id() || auth()->user()->isSuperAdmin())
                                <form method="POST" action="{{ route('admin.complaints.comments.destroy', [$complaint, $comment]) }}" class="ml-auto">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-gray-300 hover:text-red-400 transition"
                                        onclick="return confirm('Hapus komentar ini?')">Hapus</button>
                                </form>
                            @endif
                        </div>
                        <p class="text-sm text-gray-700">{{ $comment->body }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400">Belum ada komentar.</p>
            @endforelse

            {{-- Form Komentar Baru --}}
            @if(! $complaint->isClosed())
                <form method="POST" action="{{ route('admin.complaints.comments.store', $complaint) }}" class="mt-5 pt-5 border-t border-gray-100 space-y-3">
                    @csrf
                    <textarea name="body" rows="3" placeholder="Tulis komentar atau catatan..."
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                            <input type="checkbox" name="is_internal" value="1" class="h-3.5 w-3.5 rounded border-gray-300">
                            Catatan internal (tidak terlihat warga)
                        </label>
                        <button type="submit" class="bg-primary-600 text-white px-4 py-1.5 rounded-lg text-xs font-medium hover:bg-primary-700 transition">
                            Kirim
                        </button>
                    </div>
                </form>
            @endif
        </div>

    </div>

    {{-- Kolom Kanan: Aksi & Info --}}
    <div class="space-y-5">

        {{-- Info Pelapor --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Pelapor</h3>
            <div class="text-sm font-medium text-gray-900">{{ $complaint->reporter_name }}</div>
            @if(auth()->user()->isStaff() && $complaint->user)
                <div class="text-xs text-gray-500 mt-1">{{ $complaint->user->phone }}</div>
                <div class="text-xs text-gray-500">RT/RW {{ $complaint->user->rt_rw }}</div>
            @endif
            <div class="text-xs text-gray-400 mt-2">Dikirim {{ $complaint->created_at->diffForHumans() }}</div>
        </div>

        {{-- Aksi Ubah Status --}}
        @if(! $complaint->isClosed() && count($allowedTransitions) > 0)
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Ubah Status</h3>
                <form method="POST" action="{{ route('admin.complaints.update-status', $complaint) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @foreach($allowedTransitions as $s)
                            <option value="{{ $s }}">{{ \App\Models\Complaint::STATUS_LABELS[$s] ?? $s }}</option>
                        @endforeach
                    </select>
                    <textarea name="notes" rows="2" placeholder="Catatan perubahan (opsional)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                    <button type="submit" class="w-full bg-primary-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        @endif

        {{-- Selesaikan --}}
        @if($complaint->status === 'in_progress')
            <div class="bg-white rounded-xl border border-green-200 p-5">
                <h3 class="text-xs font-semibold text-green-700 uppercase tracking-wide mb-3">Selesaikan Pengaduan</h3>
                <form method="POST" action="{{ route('admin.complaints.resolve', $complaint) }}" class="space-y-3">
                    @csrf
                    <textarea name="admin_response" rows="4" required placeholder="Jelaskan tindakan yang sudah dilakukan..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition">
                        Tandai Selesai
                    </button>
                </form>
            </div>
        @endif

        {{-- Tolak --}}
        @if(in_array($complaint->status, ['submitted', 'in_review', 'in_progress']))
            <div class="bg-white rounded-xl border border-red-200 p-5">
                <h3 class="text-xs font-semibold text-red-600 uppercase tracking-wide mb-3">Tolak Pengaduan</h3>
                <form method="POST" action="{{ route('admin.complaints.reject', $complaint) }}" class="space-y-3">
                    @csrf
                    <textarea name="admin_response" rows="3" required placeholder="Jelaskan alasan penolakan secara jelas..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400"></textarea>
                    <button type="submit"
                        onclick="return confirm('Yakin ingin menolak pengaduan ini?')"
                        class="w-full bg-red-500 text-white py-2 rounded-lg text-sm font-medium hover:bg-red-600 transition">
                        Tolak Pengaduan
                    </button>
                </form>
            </div>
        @endif

        {{-- Riwayat Log --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Riwayat Status</h3>
            <div class="space-y-2">
                @foreach($complaint->complaintLogs as $log)
                    <div class="text-xs">
                        <div class="font-medium text-gray-700">{{ $log->status_transition }}</div>
                        <div class="text-gray-400">{{ $log->changedBy->name ?? 'Sistem' }} &bull; {{ $log->created_at->translatedFormat('d M, H:i') }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Rating --}}
        @if($complaint->rating)
            <div class="bg-green-50 border border-green-200 rounded-xl p-5 text-center">
                <div class="text-xs font-semibold text-green-700 uppercase tracking-wide mb-2">Rating Warga</div>
                <div class="text-2xl font-bold text-green-800">{{ $complaint->rating->score }}/5</div>
                <div class="text-xs text-green-600">{{ $complaint->rating->score_label }}</div>
                @if($complaint->rating->feedback)
                    <p class="text-xs text-green-700 mt-2 italic">"{{ $complaint->rating->feedback }}"</p>
                @endif
            </div>
        @endif

    </div>
</div>
@endsection