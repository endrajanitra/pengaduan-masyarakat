@props(['status', 'priority' => null])

@php
    $statusConfig = [
        'draft'       => ['bg-gray-100 text-gray-600',   'Draft'],
        'submitted'   => ['bg-blue-100 text-blue-700',   'Dikirim'],
        'in_review'   => ['bg-yellow-100 text-yellow-700','Sedang Ditinjau'],
        'in_progress' => ['bg-orange-100 text-orange-700','Sedang Diproses'],
        'resolved'    => ['bg-green-100 text-green-700', 'Selesai'],
        'rejected'    => ['bg-red-100 text-red-700',     'Ditolak'],
    ];

    $priorityConfig = [
        'low'    => ['bg-gray-100 text-gray-500',   'Rendah'],
        'medium' => ['bg-blue-100 text-blue-600',   'Sedang'],
        'high'   => ['bg-orange-100 text-orange-600','Tinggi'],
        'urgent' => ['bg-red-100 text-red-600',     'Mendesak'],
    ];

    [$statusClass, $statusLabel] = $statusConfig[$status] ?? ['bg-gray-100 text-gray-600', $status];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
    {{ $statusLabel }}
</span>

@if($priority)
    @php [$priorityClass, $priorityLabel] = $priorityConfig[$priority] ?? ['bg-gray-100 text-gray-500', $priority]; @endphp
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityClass }} ml-1">
        {{ $priorityLabel }}
    </span>
@endif