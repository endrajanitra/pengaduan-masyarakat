<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Category;
use App\Models\ComplaintLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /**
     * Daftar semua pengaduan milik warga yang sedang login.
     */
    public function index(Request $request): View
    {
        $query = auth()->user()->complaints()->with('category');

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $complaints = $query->latest()->paginate(10)->withQueryString();

        return view('warga.complaints.index', compact('complaints'));
    }

    /**
     * Form buat pengaduan baru.
     */
    public function create(): View
    {
        $categories = Category::active()->get();
        return view('warga.complaints.create', compact('categories'));
    }

    /**
     * Simpan pengaduan baru + lampiran.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'  => ['required', 'exists:categories,id'],
            'title'        => ['required', 'string', 'min:10', 'max:255'],
            'description'  => ['required', 'string', 'min:30'],
            'location'     => ['required', 'string', 'max:255'],
            'is_anonymous' => ['boolean'],
            'is_public'    => ['boolean'],
            'attachments'  => ['nullable', 'array', 'max:5'],
            'attachments.*'=> ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // max 5MB per file
        ]);

        DB::transaction(function () use ($validated, $request) {
            $complaint = auth()->user()->complaints()->create([
                'category_id'  => $validated['category_id'],
                'title'        => $validated['title'],
                'description'  => $validated['description'],
                'location'     => $validated['location'],
                'is_anonymous' => $request->boolean('is_anonymous'),
                'is_public'    => $request->boolean('is_public', true),
                'status'       => Complaint::STATUS_SUBMITTED,
            ]);

            // Simpan log pertama
            $complaint->complaintLogs()->create([
                'changed_by' => auth()->id(),
                'old_status' => null,
                'new_status' => Complaint::STATUS_SUBMITTED,
                'notes'      => 'Pengaduan pertama kali dikirim oleh warga.',
            ]);

            // Upload lampiran
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('complaints/' . $complaint->id, 'public');
                    $complaint->attachments()->create([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Notifikasi konfirmasi ke warga
            Notification::send(auth()->id(), Notification::TYPE_COMPLAINT_SUBMITTED, $complaint, [
                'title'   => 'Pengaduan berhasil dikirim',
                'message' => "Pengaduan #{$complaint->complaint_number} telah diterima. Kami akan segera meninjau laporan Anda.",
            ]);

            // Notifikasi ke semua admin desa
            User::active()->byRole('admin_desa')->each(function (User $admin) use ($complaint) {
                Notification::send($admin->id, Notification::TYPE_NEW_COMPLAINT_ADMIN, $complaint, [
                    'title'   => 'Pengaduan baru masuk',
                    'message' => "Ada pengaduan baru: \"{$complaint->title}\" dari warga. Silakan tinjau segera.",
                ]);
            });
        });

        return redirect()->route('warga.complaints.index')
            ->with('success', 'Pengaduan berhasil dikirim. Kami akan segera memprosesnya.');
    }

    /**
     * Detail pengaduan milik warga — termasuk log, komentar publik, dan rating.
     */
    public function show(Complaint $complaint): View
    {
        $this->authorizeComplaint($complaint);

        $complaint->load([
            'category',
            'attachments',
            'publicComments.user',
            'complaintLogs.changedBy',
            'rating',
            'handler',
        ]);

        return view('warga.complaints.show', compact('complaint'));
    }

    /**
     * Form edit pengaduan — hanya boleh jika masih berstatus draft atau submitted.
     */
    public function edit(Complaint $complaint): View
    {
        $this->authorizeComplaint($complaint);

        abort_if(
            ! in_array($complaint->status, [Complaint::STATUS_DRAFT, Complaint::STATUS_SUBMITTED]),
            403,
            'Pengaduan yang sudah diproses tidak dapat diedit.'
        );

        $categories = Category::active()->get();
        return view('warga.complaints.edit', compact('complaint', 'categories'));
    }

    /**
     * Update pengaduan — hanya draft / submitted.
     */
    public function update(Request $request, Complaint $complaint): RedirectResponse
    {
        $this->authorizeComplaint($complaint);

        abort_if(
            ! in_array($complaint->status, [Complaint::STATUS_DRAFT, Complaint::STATUS_SUBMITTED]),
            403,
            'Pengaduan yang sudah diproses tidak dapat diedit.'
        );

        $validated = $request->validate([
            'category_id'  => ['required', 'exists:categories,id'],
            'title'        => ['required', 'string', 'min:10', 'max:255'],
            'description'  => ['required', 'string', 'min:30'],
            'location'     => ['required', 'string', 'max:255'],
            'is_anonymous' => ['boolean'],
            'is_public'    => ['boolean'],
        ]);

        $complaint->update([
            'category_id'  => $validated['category_id'],
            'title'        => $validated['title'],
            'description'  => $validated['description'],
            'location'     => $validated['location'],
            'is_anonymous' => $request->boolean('is_anonymous'),
            'is_public'    => $request->boolean('is_public', true),
        ]);

        return redirect()->route('warga.complaints.show', $complaint)
            ->with('success', 'Pengaduan berhasil diperbarui.');
    }

    /**
     * Hapus pengaduan beserta lampirannya — hanya draft / submitted.
     */
    public function destroy(Complaint $complaint): RedirectResponse
    {
        $this->authorizeComplaint($complaint);

        abort_if(
            ! in_array($complaint->status, [Complaint::STATUS_DRAFT, Complaint::STATUS_SUBMITTED]),
            403,
            'Pengaduan yang sudah diproses tidak dapat dihapus.'
        );

        // Hapus file fisik dari storage
        foreach ($complaint->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $complaint->delete();

        return redirect()->route('warga.complaints.index')
            ->with('success', 'Pengaduan berhasil dihapus.');
    }

    // ----------------------------------------------------------------
    // Private Helper
    // ----------------------------------------------------------------

    /**
     * Pastikan complaint ini milik user yang sedang login.
     */
    private function authorizeComplaint(Complaint $complaint): void
    {
        abort_if($complaint->user_id !== auth()->id(), 403, 'Akses ditolak.');
    }
}