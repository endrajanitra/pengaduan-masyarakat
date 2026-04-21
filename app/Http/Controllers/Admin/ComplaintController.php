<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    // Status yang boleh dituju dari status saat ini (state machine)
    private const ALLOWED_TRANSITIONS = [
        'submitted'   => ['in_review', 'rejected'],
        'in_review'   => ['in_progress', 'rejected'],
        'in_progress' => ['resolved', 'rejected'],
        'resolved'    => [],
        'rejected'    => [],
        'draft'       => [],
    ];

    /**
     * Daftar semua pengaduan dengan filter lengkap.
     */
    public function index(Request $request): View
    {
        $query = Complaint::with(['user', 'category', 'handler']);

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('complaint_number', 'like', "%{$request->search}%");
            });
        }

        // Default: urutkan urgent/high dulu, lalu terbaru
        $complaints = $query
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::active()->get();

        return view('admin.complaints.index', compact('complaints', 'categories'));
    }

    /**
     * Detail pengaduan — semua data termasuk catatan internal.
     */
    public function show(Complaint $complaint): View
    {
        $complaint->load([
            'user',
            'category',
            'attachments',
            'comments.user',        // publik + internal
            'complaintLogs.changedBy',
            'handler',
            'rating',
        ]);

        $allowedTransitions = self::ALLOWED_TRANSITIONS[$complaint->status] ?? [];

        return view('admin.complaints.show', compact('complaint', 'allowedTransitions'));
    }

    /**
     * Update status pengaduan — validasi state machine.
     */
    public function updateStatus(Request $request, Complaint $complaint): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'notes'  => ['nullable', 'string', 'max:1000'],
        ]);

        $allowedTransitions = self::ALLOWED_TRANSITIONS[$complaint->status] ?? [];

        if (! in_array($validated['status'], $allowedTransitions)) {
            return back()->withErrors([
                'status' => "Tidak bisa mengubah status dari \"{$complaint->status_label}\" ke \"{$validated['status']}\".",
            ]);
        }

        // Update complaint (boot() di model otomatis catat log)
        $complaint->update([
            'status' => $validated['status'],
        ]);

        // Tambahkan catatan ke log jika diisi
        if (! empty($validated['notes'])) {
            $complaint->complaintLogs()->latest()->first()?->update([
                'notes' => $validated['notes'],
            ]);
        }

        // Kirim notifikasi ke warga
        $this->sendStatusNotification($complaint);

        return back()->with('success', "Status berhasil diubah menjadi \"{$complaint->status_label}\".");
    }

    /**
     * Admin menulis balasan resmi dan menutup pengaduan sebagai resolved.
     */
    public function resolve(Request $request, Complaint $complaint): RedirectResponse
    {
        abort_if($complaint->status !== Complaint::STATUS_IN_PROGRESS, 422, 'Pengaduan belum dalam proses.');

        $validated = $request->validate([
            'admin_response' => ['required', 'string', 'min:20'],
        ]);

        $complaint->update([
            'status'         => Complaint::STATUS_RESOLVED,
            'admin_response' => $validated['admin_response'],
        ]);

        // Notifikasi ke warga bahwa pengaduan selesai + minta rating
        Notification::send($complaint->user_id, Notification::TYPE_COMPLAINT_RESOLVED, $complaint, [
            'title'   => 'Pengaduan Anda telah diselesaikan',
            'message' => "Pengaduan #{$complaint->complaint_number} telah selesai ditangani. Silakan berikan penilaian Anda.",
        ]);

        Notification::send($complaint->user_id, Notification::TYPE_RATING_REQUESTED, $complaint, [
            'title'   => 'Berikan penilaian Anda',
            'message' => "Bagaimana penanganan pengaduan #{$complaint->complaint_number}? Penilaian Anda membantu kami meningkatkan layanan.",
        ]);

        return redirect()->route('admin.complaints.show', $complaint)
            ->with('success', 'Pengaduan berhasil diselesaikan dan warga telah diberitahu.');
    }

    /**
     * Admin menolak pengaduan dengan alasan.
     */
    public function reject(Request $request, Complaint $complaint): RedirectResponse
    {
        $allowedTransitions = self::ALLOWED_TRANSITIONS[$complaint->status] ?? [];
        abort_if(! in_array('rejected', $allowedTransitions), 422, 'Status pengaduan tidak bisa ditolak.');

        $validated = $request->validate([
            'admin_response' => ['required', 'string', 'min:20'],
        ]);

        $complaint->update([
            'status'         => Complaint::STATUS_REJECTED,
            'admin_response' => $validated['admin_response'],
        ]);

        Notification::send($complaint->user_id, Notification::TYPE_COMPLAINT_REJECTED, $complaint, [
            'title'   => 'Pengaduan Anda tidak dapat diproses',
            'message' => "Pengaduan #{$complaint->complaint_number} tidak dapat diproses. Silakan buka detail untuk melihat alasannya.",
        ]);

        return redirect()->route('admin.complaints.show', $complaint)
            ->with('success', 'Pengaduan berhasil ditolak dan warga telah diberitahu.');
    }

    /**
     * Update prioritas pengaduan.
     */
    public function updatePriority(Request $request, Complaint $complaint): RedirectResponse
    {
        $validated = $request->validate([
            'priority' => ['required', 'in:low,medium,high,urgent'],
        ]);

        $complaint->update(['priority' => $validated['priority']]);

        return back()->with('success', "Prioritas diubah menjadi \"{$complaint->priority_label}\".");
    }

    // ----------------------------------------------------------------
    // Private Helper
    // ----------------------------------------------------------------

    private function sendStatusNotification(Complaint $complaint): void
    {
        $messages = [
            Complaint::STATUS_IN_REVIEW   => [
                'type'    => Notification::TYPE_STATUS_UPDATED,
                'title'   => 'Pengaduan sedang ditinjau',
                'message' => "Pengaduan #{$complaint->complaint_number} sedang kami tinjau. Kami akan segera memberikan kabar.",
            ],
            Complaint::STATUS_IN_PROGRESS => [
                'type'    => Notification::TYPE_STATUS_UPDATED,
                'title'   => 'Pengaduan sedang diproses',
                'message' => "Pengaduan #{$complaint->complaint_number} sedang dalam penanganan. Pantau terus perkembangannya.",
            ],
        ];

        if (isset($messages[$complaint->status])) {
            $data = $messages[$complaint->status];
            Notification::send($complaint->user_id, $data['type'], $complaint, [
                'title'   => $data['title'],
                'message' => $data['message'],
            ]);
        }
    }
}