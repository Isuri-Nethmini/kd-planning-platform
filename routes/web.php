<?php

use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PlanController;
use App\Http\Controllers\Public\InquiryController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use Illuminate\Support\Facades\Route;

// ── Public routes ────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
Route::get('/plans/{housePlan}', [PlanController::class, 'show'])->name('plans.show');

Route::get('/inquire', [InquiryController::class, 'create'])->name('inquire');
Route::post('/inquire', [InquiryController::class, 'store'])->name('inquire.store');
Route::get('/inquire/success', [InquiryController::class, 'success'])->name('inquire.success');

// ── Admin auth routes (no middleware) ────────────────────────
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// ── Admin protected routes ───────────────────────────────────
Route::middleware(\App\Http\Middleware\AdminAuth::class)->group(function () {

    Route::get('/admin', fn() => redirect('/admin/dashboard'));
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/admin/plans', [AdminPlanController::class, 'index'])->name('admin.plans.index');
    Route::get('/admin/plans/create', [AdminPlanController::class, 'create'])->name('admin.plans.create');
    Route::post('/admin/plans', [AdminPlanController::class, 'store'])->name('admin.plans.store');
    Route::get('/admin/plans/{plan}/edit', [AdminPlanController::class, 'edit'])->name('admin.plans.edit');
    Route::put('/admin/plans/{plan}', [AdminPlanController::class, 'update'])->name('admin.plans.update');
    Route::delete('/admin/plans/{plan}', [AdminPlanController::class, 'destroy'])->name('admin.plans.destroy');

});
