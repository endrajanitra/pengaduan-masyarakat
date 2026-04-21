<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Category;
use App\Models\User;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard utama admin — statistik pengaduan dan data kinerja.
     */
    public function index(): View
    {
        // Statistik utama
        $stats = [
            'total'       => Complaint::count(),
            'submitted'   => Complaint::byStatus('submitted')->count(),
            'in_review'   => Complaint::byStatus('in_review')->count(),
            'in_progress' => Complaint::byStatus('in_progress')->count(),
            'resolved'    => Complaint::byStatus('resolved')->count(),
            'rejected'    => Complaint::byStatus('rejected')->count(),
        ];

        // Pengaduan terbaru yang perlu ditangani (submitted + in_review)
        $pendingComplaints = Complaint::with(['user', 'category'])
            ->whereIn('status', ['submitted', 'in_review'])
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->latest()
            ->take(8)
            ->get();

        // Pengaduan urgent yang belum selesai
        $urgentCount = Complaint::urgent()->count();

        // Tren pengaduan per bulan (6 bulan terakhir)
        $monthlyTrend = Complaint::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Distribusi per kategori
        $categoryStats = Category::withCount('complaints')
            ->orderBy('complaints_count', 'desc')
            ->get();

        // Rata-rata rating kepuasan
        $avgRating = Rating::avg('score');

        // Total warga terdaftar
        $totalWarga = User::active()->byRole('warga')->count();

        return view('admin.dashboard', compact(
            'stats',
            'pendingComplaints',
            'urgentCount',
            'monthlyTrend',
            'categoryStats',
            'avgRating',
            'totalWarga'
        ));
    }
}