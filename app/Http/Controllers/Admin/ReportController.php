<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Rating;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Halaman laporan lengkap — hanya kepala desa dan super admin.
     */
    public function index(Request $request): View
    {
        abort_unless(
            auth()->user()->isKepalaDesa() || auth()->user()->isSuperAdmin(),
            403,
            'Hanya kepala desa dan super admin yang bisa mengakses laporan.'
        );

        $year  = $request->input('year', now()->year);
        $month = $request->input('month'); // opsional, filter per bulan

        // ── Ringkasan total ─────────────────────────────────────────
        $summary = [
            'total'          => Complaint::whereYear('created_at', $year)->count(),
            'resolved'       => Complaint::whereYear('created_at', $year)->byStatus('resolved')->count(),
            'rejected'       => Complaint::whereYear('created_at', $year)->byStatus('rejected')->count(),
            'open'           => Complaint::whereYear('created_at', $year)->open()->count(),
            'avg_rating'     => round(Rating::whereYear('created_at', $year)->avg('score'), 1),
            'total_ratings'  => Rating::whereYear('created_at', $year)->count(),
        ];

        // Persentase penyelesaian
        $summary['resolve_rate'] = $summary['total'] > 0
            ? round(($summary['resolved'] / $summary['total']) * 100, 1)
            : 0;

        // ── Tren bulanan sepanjang tahun ────────────────────────────
        $monthlyTrend = Complaint::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved"),
                DB::raw("SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected")
            )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // ── Distribusi per kategori ─────────────────────────────────
        $categoryBreakdown = Category::withCount([
                'complaints as total_count' => fn ($q) => $q->whereYear('created_at', $year),
                'complaints as resolved_count' => fn ($q) => $q->whereYear('created_at', $year)->where('status', 'resolved'),
            ])
            ->orderBy('total_count', 'desc')
            ->get();

        // ── Rata-rata waktu penyelesaian (hari) ─────────────────────
        $avgResolutionDays = Complaint::whereYear('created_at', $year)
            ->whereNotNull('resolved_at')
            ->select(DB::raw('AVG(DATEDIFF(resolved_at, created_at)) as avg_days'))
            ->value('avg_days');

        // ── Distribusi rating ───────────────────────────────────────
        $ratingDistribution = Rating::select('score', DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', $year)
            ->groupBy('score')
            ->orderBy('score')
            ->pluck('total', 'score');

        // ── Daftar tahun yang tersedia untuk filter ─────────────────
        $availableYears = Complaint::selectRaw('YEAR(created_at) as year')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.reports.index', compact(
            'summary',
            'monthlyTrend',
            'categoryBreakdown',
            'avgResolutionDays',
            'ratingDistribution',
            'availableYears',
            'year',
        ));
    }
}