<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Category;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Halaman publik utama — bisa diakses tanpa login.
     * Menampilkan statistik desa dan daftar pengaduan publik terbaru.
     */
    public function publicHome(): View
    {
        $stats = [
            'total'       => Complaint::public()->count(),
            'resolved'    => Complaint::public()->byStatus('resolved')->count(),
            'in_progress' => Complaint::public()->byStatus('in_progress')->count(),
            'submitted'   => Complaint::public()->byStatus('submitted')->count(),
        ];

        $latestComplaints = Complaint::public()
            ->with(['category', 'user'])
            ->whereNotIn('status', ['draft'])
            ->latest()
            ->take(6)
            ->get();

        $categories = Category::active()->withCount('complaints')->get();

        return view('public.home', compact('stats', 'latestComplaints', 'categories'));
    }

    /**
     * Halaman daftar semua pengaduan publik dengan filter dan pencarian.
     */
    public function publicComplaints(Request $request): View
    {
        $query = Complaint::public()
            ->with(['category', 'user'])
            ->whereNotIn('status', ['draft']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('complaint_number', 'like', "%{$request->search}%");
            });
        }

        $complaints = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::active()->get();

        return view('public.complaints', compact('complaints', 'categories'));
    }

    /**
     * Halaman detail pengaduan publik — tanpa login.
     */
    public function publicShow(string $complaintNumber): View
    {
        $complaint = Complaint::public()
            ->where('complaint_number', $complaintNumber)
            ->with([
                'category',
                'attachments',
                'publicComments.user',
                'complaintLogs.changedBy',
                'rating',
            ])
            ->firstOrFail();

        return view('public.complaint-detail', compact('complaint'));
    }

    /**
     * Dashboard warga — ringkasan pengaduan miliknya sendiri.
     */
    public function dashboard(): View
    {
        $user = auth()->user();

        $stats = [
            'total'       => $user->complaints()->count(),
            'submitted'   => $user->complaints()->byStatus('submitted')->count(),
            'in_progress' => $user->complaints()->byStatus('in_progress')->count(),
            'resolved'    => $user->complaints()->byStatus('resolved')->count(),
        ];

        $recentComplaints = $user->complaints()
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        $unreadCount = $user->unreadNotificationsCount();

        return view('warga.dashboard', compact('stats', 'recentComplaints', 'unreadCount'));
    }
}