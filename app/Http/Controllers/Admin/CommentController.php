<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Comment;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Admin menambahkan komentar ke pengaduan.
     * is_internal = true  → catatan internal (hanya terlihat staf)
     * is_internal = false → komentar publik (terlihat warga)
     */
    public function store(Request $request, Complaint $complaint): RedirectResponse
    {
        $validated = $request->validate([
            'body'        => ['required', 'string', 'min:5', 'max:2000'],
            'is_internal' => ['boolean'],
        ]);

        $comment = $complaint->comments()->create([
            'user_id'     => auth()->id(),
            'body'        => $validated['body'],
            'is_internal' => $request->boolean('is_internal'),
        ]);

        // Kirim notifikasi ke warga hanya jika komentar publik
        if (! $comment->is_internal) {
            Notification::send($complaint->user_id, Notification::TYPE_NEW_COMMENT, $complaint, [
                'title'   => 'Ada komentar baru pada pengaduan Anda',
                'message' => "Petugas menambahkan komentar pada pengaduan #{$complaint->complaint_number}. Silakan cek untuk detail.",
            ]);
        }

        return back()->with('success', $comment->is_internal
            ? 'Catatan internal berhasil ditambahkan.'
            : 'Komentar berhasil ditambahkan dan warga telah diberitahu.'
        );
    }

    /**
     * Hapus komentar — hanya penulis komentar atau super admin.
     */
    public function destroy(Complaint $complaint, Comment $comment): RedirectResponse
    {
        $user = auth()->user();

        abort_if(
            $comment->user_id !== $user->id && ! $user->isSuperAdmin(),
            403,
            'Anda tidak berhak menghapus komentar ini.'
        );

        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}