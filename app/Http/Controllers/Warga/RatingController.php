<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Simpan rating warga untuk pengaduan yang sudah selesai.
     * Satu pengaduan hanya boleh satu rating.
     */
    public function store(Request $request, Complaint $complaint): RedirectResponse
    {
        // Hanya pemilik pengaduan yang boleh memberi rating
        abort_if($complaint->user_id !== auth()->id(), 403, 'Akses ditolak.');

        // Hanya pengaduan yang sudah resolved
        abort_if(! $complaint->isResolved(), 422, 'Pengaduan belum selesai.');

        // Cegah double rating
        abort_if($complaint->hasRating(), 422, 'Pengaduan ini sudah dinilai.');

        $validated = $request->validate([
            'score'    => ['required', 'integer', 'min:1', 'max:5'],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $complaint->rating()->create([
            'user_id'  => auth()->id(),
            'score'    => $validated['score'],
            'feedback' => $validated['feedback'] ?? null,
        ]);

        return redirect()->route('warga.complaints.show', $complaint)
            ->with('success', 'Terima kasih atas penilaian Anda!');
    }
}