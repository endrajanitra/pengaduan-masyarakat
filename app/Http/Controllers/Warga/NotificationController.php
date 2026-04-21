<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Halaman daftar semua notifikasi milik user yang login.
     */
    public function index(): View
    {
        $notifications = auth()->user()
            ->notifications()
            ->with('complaint')
            ->latest('created_at')
            ->paginate(20);

        // Tandai semua sebagai sudah dibaca saat halaman dibuka
        auth()->user()->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return view('warga.notifications.index', compact('notifications'));
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca (via AJAX).
     */
    public function markRead(Notification $notification): JsonResponse
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua notifikasi milik user sebagai sudah dibaca.
     */
    public function markAllRead(): RedirectResponse
    {
        auth()->user()->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    /**
     * Ambil jumlah notifikasi belum dibaca — untuk badge di navbar (AJAX polling).
     */
    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'count' => auth()->user()->unreadNotificationsCount(),
        ]);
    }
}