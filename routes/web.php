<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Warga\DashboardController as WargaDashboardController;
use App\Http\Controllers\Warga\ComplaintController as WargaComplaintController;
use App\Http\Controllers\Warga\RatingController as WargaRatingController;
use App\Http\Controllers\Warga\NotificationController as WargaNotificationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SiteSettingController as AdminSiteSettingController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ====================================================================
// GRUP 1 — PUBLIK (tanpa login)
// Halaman yang bisa diakses siapa saja
// ====================================================================

Route::get('/', [WargaDashboardController::class, 'publicHome'])->name('home');

Route::get('/pengaduan', [WargaDashboardController::class, 'publicComplaints'])
    ->name('public.complaints');

Route::get('/pengaduan/{complaintNumber}', [WargaDashboardController::class, 'publicShow'])
    ->name('public.complaints.show')
    ->where('complaintNumber', 'ADU-[0-9]{4}-[0-9]{4}');

// ====================================================================
// GRUP 2 — AUTENTIKASI
// Hanya bisa diakses jika BELUM login (middleware 'guest')
// ====================================================================

Route::middleware('guest')->group(function () {

    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register']);

    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login']);

});

// Logout tetap bisa diakses user yang login
Route::post('/keluar', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ====================================================================
// GRUP 3 — VERIFIKASI EMAIL
// Laravel built-in email verification routes
// ====================================================================

Route::middleware('auth')->group(function () {
 
    // Halaman notice setelah daftar
    Route::get('/email/verify', function () {
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('warga.dashboard');
        }
        return view('auth.verify-email');
    })->name('verification.notice');
 
    // Handler klik link verifikasi di email
    // fulfill() otomatis memicu event Verified
    // → listener ActivateUserAfterVerification menangkap event ini
    // → is_active diset true di sana
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill(); // ← event Verified dijalankan di sini
 
        return redirect()->route('warga.dashboard')
            ->with('success', 'Email berhasil diverifikasi. Selamat datang!');
    })->middleware('signed')->name('verification.verify');
 
    // Kirim ulang email verifikasi
    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('warga.dashboard');
        }
 
        try {
            $request->user()->sendEmailVerificationNotification();
            return back()->with('resent', true);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Kirim ulang verifikasi gagal: ' . $e->getMessage());
            return back()->withErrors([
                'email' => 'Gagal mengirim email. Pastikan konfigurasi SMTP di .env sudah benar.',
            ]);
        }
 
    })->middleware('throttle:6,1')->name('verification.send');
 
});
 
 


// ====================================================================
// GRUP 4 — WARGA
// Harus login + email terverifikasi + role warga
// ====================================================================

Route::middleware(['auth', 'verified', 'role:warga'])
    ->prefix('warga')
    ->name('warga.')
    ->group(function () {

        // Dashboard warga
        Route::get('/dashboard', [WargaDashboardController::class, 'dashboard'])
            ->name('dashboard');

        // ── Pengaduan ────────────────────────────────────────────────
        Route::prefix('pengaduan')->name('complaints.')->group(function () {

            Route::get('/', [WargaComplaintController::class, 'index'])
                ->name('index');

            Route::get('/buat', [WargaComplaintController::class, 'create'])
                ->name('create');

            Route::post('/', [WargaComplaintController::class, 'store'])
                ->name('store');

            Route::get('/{complaint}', [WargaComplaintController::class, 'show'])
                ->name('show');

            Route::get('/{complaint}/edit', [WargaComplaintController::class, 'edit'])
                ->name('edit');

            Route::put('/{complaint}', [WargaComplaintController::class, 'update'])
                ->name('update');

            Route::delete('/{complaint}', [WargaComplaintController::class, 'destroy'])
                ->name('destroy');

            // Rating — hanya POST setelah pengaduan resolved
            Route::post('/{complaint}/rating', [WargaRatingController::class, 'store'])
                ->name('rating.store');

        });

        // ── Notifikasi ───────────────────────────────────────────────
        Route::prefix('notifikasi')->name('notifications.')->group(function () {

            Route::get('/', [WargaNotificationController::class, 'index'])
                ->name('index');

            Route::post('/tandai-semua', [WargaNotificationController::class, 'markAllRead'])
                ->name('mark-all-read');

            // AJAX endpoint — tandai satu notifikasi sudah dibaca
            Route::patch('/{notification}/baca', [WargaNotificationController::class, 'markRead'])
                ->name('mark-read');

            // AJAX endpoint — ambil jumlah notifikasi belum dibaca (untuk badge navbar)
            Route::get('/jumlah-belum-dibaca', [WargaNotificationController::class, 'unreadCount'])
                ->name('unread-count');

        });

    });

// ====================================================================
// GRUP 5 — ADMIN (semua staf desa)
// Harus login + verified + role: admin_desa, kepala_desa, atau super_admin
// ====================================================================

Route::middleware(['auth', 'verified', 'role:admin_desa,kepala_desa,super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // ── Pengaduan (admin) ────────────────────────────────────────
        Route::prefix('pengaduan')->name('complaints.')->group(function () {

            Route::get('/', [AdminComplaintController::class, 'index'])
                ->name('index');

            Route::get('/{complaint}', [AdminComplaintController::class, 'show'])
                ->name('show');

            // Ubah status pengaduan (in_review, in_progress, dll)
            Route::patch('/{complaint}/status', [AdminComplaintController::class, 'updateStatus'])
                ->name('update-status');

            // Ubah prioritas pengaduan
            Route::patch('/{complaint}/prioritas', [AdminComplaintController::class, 'updatePriority'])
                ->name('update-priority');

            // Selesaikan pengaduan (tulis balasan resmi + resolved)
            Route::post('/{complaint}/selesaikan', [AdminComplaintController::class, 'resolve'])
                ->name('resolve');

            // Tolak pengaduan (tulis alasan + rejected)
            Route::post('/{complaint}/tolak', [AdminComplaintController::class, 'reject'])
                ->name('reject');

            // ── Komentar pada pengaduan ──────────────────────────────
            Route::prefix('/{complaint}/komentar')->name('comments.')->group(function () {

                Route::post('/', [AdminCommentController::class, 'store'])
                    ->name('store');

                Route::delete('/{comment}', [AdminCommentController::class, 'destroy'])
                    ->name('destroy');

            });

        });

        // ── Laporan & Statistik (kepala_desa + super_admin) ──────────
        Route::get('/laporan', [AdminReportController::class, 'index'])
            ->name('reports.index')
            ->middleware('role:kepala_desa,super_admin');

        // ── Kategori ─────────────────────────────────────────────────
        Route::prefix('kategori')->name('categories.')->group(function () {

            Route::get('/', [AdminCategoryController::class, 'index'])
                ->name('index');

            // Hanya super_admin bisa tambah/edit/toggle kategori
            Route::middleware('role:super_admin')->group(function () {

                Route::get('/buat', [AdminCategoryController::class, 'create'])
                    ->name('create');

                Route::post('/', [AdminCategoryController::class, 'store'])
                    ->name('store');

                Route::get('/{category}/edit', [AdminCategoryController::class, 'edit'])
                    ->name('edit');

                Route::put('/{category}', [AdminCategoryController::class, 'update'])
                    ->name('update');

                Route::patch('/{category}/toggle', [AdminCategoryController::class, 'toggleActive'])
                    ->name('toggle-active');

            });

        });

        // ── Manajemen Pengguna (super_admin only) ────────────────────
        Route::middleware('role:super_admin')
            ->prefix('pengguna')
            ->name('users.')
            ->group(function () {

                Route::get('/', [AdminUserController::class, 'index'])
                    ->name('index');

                Route::get('/buat', [AdminUserController::class, 'create'])
                    ->name('create');

                Route::post('/', [AdminUserController::class, 'store'])
                    ->name('store');

                Route::get('/{user}/edit', [AdminUserController::class, 'edit'])
                    ->name('edit');

                Route::put('/{user}', [AdminUserController::class, 'update'])
                    ->name('update');

                Route::patch('/{user}/toggle-aktif', [AdminUserController::class, 'toggleActive'])
                    ->name('toggle-active');

                Route::patch('/{user}/reset-password', [AdminUserController::class, 'resetPassword'])
                    ->name('reset-password');

            });

        // ── Pengaturan Situs (super_admin only) ──────────────────────
        Route::middleware('role:super_admin')
            ->prefix('pengaturan')
            ->name('settings.')
            ->group(function () {

                Route::get('/', [AdminSiteSettingController::class, 'index'])
                    ->name('index');

                Route::put('/', [AdminSiteSettingController::class, 'update'])
                    ->name('update');

            });

    });