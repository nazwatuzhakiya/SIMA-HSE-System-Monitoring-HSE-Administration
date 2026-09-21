<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubcontController;
use App\Http\Controllers\ManpowerController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\ApdController;
use App\Http\Controllers\KlinikController;
use App\Http\Controllers\Audit5rController;
use App\Http\Controllers\InsidenController;
use App\Http\Controllers\TindakLanjutController;
use App\Http\Controllers\AuditScheduleController;
use App\Http\Controllers\TrainingErpController;
use App\Http\Controllers\JspController;

// Entry Point: Landing Page Publik HSE
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Internal HSE Dashboard Routes & Calendar Activity
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/dashboard/schedule', [DashboardController::class, 'storeSchedule'])->name('dashboard.schedule.store');
Route::put('/dashboard/schedule/{id}', [DashboardController::class, 'updateSchedule'])->name('dashboard.schedule.update');
Route::delete('/dashboard/schedule/{id}', [DashboardController::class, 'destroySchedule'])->name('dashboard.schedule.destroy');

// Modul Monitoring Terpisah (Departemen, Subkontraktor, Proyek, & Tamu Khusus)
Route::get('/monitoring/departemen', [SubcontController::class, 'departemen'])->name('subcont.departemen');
Route::get('/monitoring/subcont', [SubcontController::class, 'subcont'])->name('subcont.subcont');
Route::get('/subcont', [SubcontController::class, 'departemen'])->name('subcont.index');
Route::get('/monitoring/proyek', [SubcontController::class, 'proyek'])->name('subcont.proyek');
Route::get('/subcont/proyek', [SubcontController::class, 'proyek']);
Route::get('/monitoring/visitor', [SubcontController::class, 'visitor'])->name('subcont.visitor');
Route::get('/subcont/visitor', [SubcontController::class, 'visitor']);
Route::post('/subcont', [SubcontController::class, 'store'])->name('subcont.store');
Route::post('/subcont/proyek', [SubcontController::class, 'storeProyek'])->name('subcont.proyek.store');
Route::post('/subcont/visitor', [SubcontController::class, 'storeVisitor'])->name('subcont.visitor.store');
Route::post('/subcont/visitor/{id}/finish', [SubcontController::class, 'finishVisitor'])->name('subcont.visitor.finish');
Route::put('/subcont/{id}', [SubcontController::class, 'update'])->name('subcont.update');
Route::delete('/subcont/{id}', [SubcontController::class, 'destroy'])->name('subcont.destroy');
Route::post('/subcont/company/update', [SubcontController::class, 'updateCompany'])->name('subcont.company.update');

// Modul Master Manpower (Pekerja Plant & Manpower Proyek ENT)
Route::get('/manpower', [ManpowerController::class, 'index'])->name('manpower.index');
Route::get('/manpower/proyek', [ManpowerController::class, 'proyek'])->name('manpower.proyek');
Route::post('/manpower', [ManpowerController::class, 'store'])->name('manpower.store');
Route::put('/manpower/{id}', [ManpowerController::class, 'update'])->name('manpower.update');
Route::delete('/manpower/{id}', [ManpowerController::class, 'destroy'])->name('manpower.destroy');

// Modul Pendataan & Distribusi APD
Route::get('/apd', [ApdController::class, 'index'])->name('apd.index');
Route::get('/apd/create', [ApdController::class, 'create'])->name('apd.create');
Route::post('/apd', [ApdController::class, 'store'])->name('apd.store');

// Modul Cetak Form Absensi Otomatis Terintegrasi Pekerja Aktif
Route::get('/absensi', [AbsensiController::class, 'setupForm'])->name('absensi.setup');
Route::get('/absensi/print', [AbsensiController::class, 'printForm'])->name('absensi.print');


