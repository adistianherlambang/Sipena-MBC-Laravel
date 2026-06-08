<?php

use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\SuperAdmin\SettingController;
use App\Http\Controllers\SuperAdmin\UserController;
use Illuminate\Support\Facades\Route;

// Public client routes
Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::post('/submit-pengaduan', [LandingPageController::class, 'submitComplaint'])->name('complaint.submit');
Route::get('/tracking', [LandingPageController::class, 'tracking'])->name('complaint.tracking');
Route::get('/tiket', [LandingPageController::class, 'tiket'])->name('complaint.tiket');

// Auth routes
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Protected backend routes
Route::middleware(['auth'])->group(function () {

    // Admin & Super Admin shared routes (under /admin prefix)
    Route::middleware(['role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pengaduan', [ComplaintController::class, 'index'])->name('complaint.index');
        Route::get('/detail-pengaduan/{id}', [ComplaintController::class, 'show'])->name('complaint.show');
        Route::post('/detail-pengaduan/{id}/status', [ComplaintController::class, 'updateStatus'])->name('complaint.update_status');
        Route::post('/detail-pengaduan/{id}/response', [ComplaintController::class, 'addResponse'])->name('complaint.add_response');
        Route::post('/detail-pengaduan/{id}/escalate', [ComplaintController::class, 'escalate'])->name('complaint.escalate');

        Route::get('/laporan', [ReportController::class, 'index'])->name('report.index');
        Route::get('/export-csv', [ReportController::class, 'exportCsv'])->name('report.export_csv');
        Route::get('/export-word', [ReportController::class, 'exportWord'])->name('report.export_word');
        Route::get('/cetak-pdf', [ReportController::class, 'cetakPdf'])->name('report.cetak_pdf');
    });

    // Super Admin exclusive routes (under /superadmin prefix)
    Route::middleware(['role:super_admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pengaduan', [ComplaintController::class, 'index'])->name('complaint.index');
        Route::get('/detail-pengaduan/{id}', [ComplaintController::class, 'show'])->name('complaint.show');
        Route::post('/hapus-pengaduan', [ComplaintController::class, 'destroy'])->name('complaint.destroy');

        Route::get('/laporan', [ReportController::class, 'index'])->name('report.index');
        Route::get('/export-csv', [ReportController::class, 'exportCsv'])->name('report.export_csv');
        Route::get('/export-word', [ReportController::class, 'exportWord'])->name('report.export_word');
        Route::get('/cetak-pdf', [ReportController::class, 'cetakPdf'])->name('report.cetak_pdf');

        // User Management CRUD
        Route::get('/user', [UserController::class, 'index'])->name('user.index');
        Route::get('/user/tambah', [UserController::class, 'create'])->name('user.create');
        Route::post('/user/tambah', [UserController::class, 'store'])->name('user.store');
        Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::post('/user/edit/{id}', [UserController::class, 'update'])->name('user.update');
        Route::post('/user/hapus', [UserController::class, 'destroy'])->name('user.destroy');

        // Landing Settings
        Route::get('/pengaturan', [SettingController::class, 'index'])->name('setting.index');
        Route::post('/pengaturan', [SettingController::class, 'update'])->name('setting.update');
    });
});
