@props(['status', 'priority' => null])

@php
    $statusConfig = [
        'draft'       => ['bg-gray-100 text-gray-600 ring-1 ring-gray-200',         'Draft'],
        'submitted'   => ['bg-blue-50 text-blue-700 ring-1 ring-blue-200',           'Dikirim'],
        'in_review'   => ['bg-yellow-50 text-yellow-700 ring-1 ring-yellow-200',     'Sedang Ditinjau'],
        'in_progress' => ['bg-orange-50 text-orange-700 ring-1 ring-orange-200',     'Sedang Diproses'],
        'resolved'    => ['bg-green-50 text-green-700 ring-1 ring-green-200',        'Selesai'],
        'rejected'    => ['bg-red-50 text-red-700 ring-1 ring-red-200',              'Ditolak'],
    ];
    $priorityConfig = [
        'low'    => ['bg-gray-100 text-gray-500 ring-1 ring-gray-200',          'Rendah'],
        'medium' => ['bg-blue-50 text-blue-600 ring-1 ring-blue-200',           'Sedang'],
        'high'   => ['bg-orange-50 text-orange-600 ring-1 ring-orange-200',     'Tinggi'],
        'urgent' => ['bg-red-50 text-red-600 ring-1 ring-red-200',              'Mendesak'],
    ];
    [$statusClass, $statusLabel] = $statusConfig[$status] ?? ['bg-gray-100 text-gray-600', $status];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
    {{ $statusLabel }}
</span>

@if($priority)
    @php [$priorityClass, $priorityLabel] = $priorityConfig[$priority] ?? ['bg-gray-100 text-gray-500', $priority]; @endphp
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $priorityClass }} ml-1">
        {{ $priorityLabel }}
    </span>
@endif
