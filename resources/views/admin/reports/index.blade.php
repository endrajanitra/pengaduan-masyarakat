@extends('layouts.admin')
@section('title', 'Laporan')
@section('page-title', 'Laporan & Statistik')
@section('breadcrumb', 'Admin / Laporan')

@section('content')

{{-- Filter Tahun --}}
<form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-center gap-3 mb-6">
    <label class="text-sm text-gray-600">Tahun:</label>
    <select name="year" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        @foreach($availableYears as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">Tampilkan</button>
</form>

{{-- Ringkasan --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @php
        $summaryCards = [
            ['label' => 'Total Pengaduan', 'value' => $summary['total'],         'sub' => 'Tahun ' . $year,              'color' => 'border-gray-200 bg-white'],
            ['label' => 'Diselesaikan',    'value' => $summary['resolved'],       'sub' => $summary['resolve_rate'].'% dari total', 'color' => 'border-green-200 bg-green-50'],
            ['label' => 'Rata-rata Rating','value' => ($summary['avg_rating'] ?: '-') . ($summary['avg_rating'] ? ' ⭐' : ''), 'sub' => $summary['total_ratings'].' penilaian', 'color' => 'border-amber-200 bg-amber-50'],
            ['label' => 'Masih Terbuka',   'value' => $summary['open'],           'sub' => 'Perlu ditindaklanjuti',       'color' => 'border-orange-200 bg-orange-50'],
        ];
    @endphp
    @foreach($summaryCards as $card)
        <div class="rounded-xl border {{ $card['color'] }} p-5">
            <div class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</div>
            <div class="text-sm font-medium text-gray-700 mt-0.5">{{ $card['label'] }}</div>
            <div class="text-xs text-gray-400 mt-0.5">{{ $card['sub'] }}</div>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Tren Bulanan --}}
    <div class="col-span-2 bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Tren Pengaduan Bulanan {{ $year }}</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-500">
                        <th class="text-left py-2 pr-3 font-medium">Bulan</th>
                        <th class="text-center py-2 px-3 font-medium">Total</th>
                        <th class="text-center py-2 px-3 font-medium">Selesai</th>
                        <th class="text-center py-2 px-3 font-medium">Ditolak</th>
                        <th class="text-left py-2 pl-3 font-medium">Bar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php
                        $months = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
                        $maxTotal = $monthlyTrend->max('total') ?: 1;
                    @endphp
                    @for($m = 1; $m <= 12; $m++)
                        @php $row = $monthlyTrend->get($m); @endphp
                        <tr class="{{ $row ? '' : 'opacity-40' }}">
                            <td class="py-2 pr-3 text-gray-600 font-medium">{{ $months[$m] }}</td>
                            <td class="py-2 px-3 text-center font-semibold text-gray-900">{{ $row->total ?? 0 }}</td>
                            <td class="py-2 px-3 text-center text-green-600">{{ $row->resolved ?? 0 }}</td>
                            <td class="py-2 px-3 text-center text-red-400">{{ $row->rejected ?? 0 }}</td>
                            <td class="py-2 pl-3">
                                @if($row && $row->total > 0)
                                    <div class="h-2 bg-primary-200 rounded-full" style="width: {{ round(($row->total / $maxTotal) * 100) }}%; min-width: 4px;"></div>
                                @endif
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    {{-- Kolom Kanan --}}
    <div class="space-y-5">

        {{-- Per Kategori --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Per Kategori</h2>
            <div class="space-y-3">
                @foreach($categoryBreakdown as $cat)
                    @if($cat->total_count > 0)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-gray-700 truncate">{{ $cat->name }}</span>
                                <span class="font-semibold text-gray-900 ml-2">{{ $cat->total_count }}</span>
                            </div>
                            <div class="h-1.5 bg-gray-100 rounded-full">
                                @php $pct = $summary['total'] > 0 ? round(($cat->total_count / $summary['total']) * 100) : 0; @endphp
                                <div class="h-1.5 bg-primary-400 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Distribusi Rating --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Distribusi Rating</h2>
            @php $maxRating = $ratingDistribution->max() ?: 1; @endphp
            <div class="space-y-2">
                @for($s = 5; $s >= 1; $s--)
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-amber-400 w-12">{{ str_repeat('★', $s) }}</span>
                        <div class="flex-1 h-2 bg-gray-100 rounded-full">
                            @php $cnt = $ratingDistribution->get($s, 0); @endphp
                            <div class="h-2 bg-amber-300 rounded-full" style="width: {{ $maxRating > 0 ? round(($cnt / $maxRating) * 100) : 0 }}%"></div>
                        </div>
                        <span class="w-6 text-right text-gray-500">{{ $cnt }}</span>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Waktu Penyelesaian --}}
        @if($avgResolutionDays)
            <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                <div class="text-3xl font-bold text-primary-600">{{ round($avgResolutionDays) }}</div>
                <div class="text-sm text-gray-600 mt-0.5">hari rata-rata penyelesaian</div>
            </div>
        @endif

    </div>
</div>

@endsection