<?php

use App\Http\Controllers\Public\ContentController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PlanController;
use App\Http\Controllers\Public\InquiryController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CompletedProjectController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TestimonialController;
use Illuminate\Support\Facades\Route;

// ── Public routes ────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
Route::get('/plans/{housePlan}', [PlanController::class, 'show'])->name('plans.show');

Route::get('/completed-projects', [ContentController::class, 'projects'])->name('projects.index');

Route::get('/blog', [ContentController::class, 'blog'])->name('blog.index');
Route::get('/blog/{slug}', [ContentController::class, 'blogPost'])->name('blog.show');

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

    // House plans
    Route::get('/admin/plans', [AdminPlanController::class, 'index'])->name('admin.plans.index');
    Route::get('/admin/plans/create', [AdminPlanController::class, 'create'])->name('admin.plans.create');
    Route::post('/admin/plans', [AdminPlanController::class, 'store'])->name('admin.plans.store');
    Route::get('/admin/plans/{plan}/edit', [AdminPlanController::class, 'edit'])->name('admin.plans.edit');
    Route::put('/admin/plans/{plan}', [AdminPlanController::class, 'update'])->name('admin.plans.update');
    Route::delete('/admin/plans/{plan}', [AdminPlanController::class, 'destroy'])->name('admin.plans.destroy');

    // Inquiries
    Route::get('/admin/inquiries', [AdminInquiryController::class, 'index'])->name('admin.inquiries.index');
    Route::get('/admin/inquiries/{inquiry}', [AdminInquiryController::class, 'show'])->name('admin.inquiries.show');
    Route::patch('/admin/inquiries/{inquiry}/status', [AdminInquiryController::class, 'updateStatus'])->name('admin.inquiries.status');
    Route::delete('/admin/inquiries/{inquiry}', [AdminInquiryController::class, 'destroy'])->name('admin.inquiries.destroy');

    // Completed projects
    Route::get('/admin/projects', [CompletedProjectController::class, 'index'])->name('admin.projects.index');
    Route::get('/admin/projects/create', [CompletedProjectController::class, 'create'])->name('admin.projects.create');
    Route::post('/admin/projects', [CompletedProjectController::class, 'store'])->name('admin.projects.store');
    Route::get('/admin/projects/{project}/edit', [CompletedProjectController::class, 'edit'])->name('admin.projects.edit');
    Route::put('/admin/projects/{project}', [CompletedProjectController::class, 'update'])->name('admin.projects.update');
    Route::delete('/admin/projects/{project}', [CompletedProjectController::class, 'destroy'])->name('admin.projects.destroy');

    // Blog
    Route::get('/admin/blog', [BlogController::class, 'index'])->name('admin.blog.index');
    Route::get('/admin/blog/create', [BlogController::class, 'create'])->name('admin.blog.create');
    Route::post('/admin/blog', [BlogController::class, 'store'])->name('admin.blog.store');
    Route::get('/admin/blog/{post}/edit', [BlogController::class, 'edit'])->name('admin.blog.edit');
    Route::put('/admin/blog/{post}', [BlogController::class, 'update'])->name('admin.blog.update');
    Route::delete('/admin/blog/{post}', [BlogController::class, 'destroy'])->name('admin.blog.destroy');

    // Testimonials
    Route::get('/admin/testimonials', [TestimonialController::class, 'index'])->name('admin.testimonials.index');
    Route::get('/admin/testimonials/create', [TestimonialController::class, 'create'])->name('admin.testimonials.create');
    Route::post('/admin/testimonials', [TestimonialController::class, 'store'])->name('admin.testimonials.store');
    Route::get('/admin/testimonials/{testimonial}/edit', [TestimonialController::class, 'edit'])->name('admin.testimonials.edit');
    Route::put('/admin/testimonials/{testimonial}', [TestimonialController::class, 'update'])->name('admin.testimonials.update');
    Route::delete('/admin/testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->name('admin.testimonials.destroy');

    // Analytics & settings
    Route::get('/admin/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');
    Route::get('/admin/settings', [SettingController::class, 'edit'])->name('admin.settings');
    Route::put('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');

});
