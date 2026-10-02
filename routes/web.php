<?php

use App\Http\Controllers\Public\ContentController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PlanController;
use App\Http\Controllers\Public\InquiryController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CompletedProjectController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginLogController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ClientProjectController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Portal\AuthController as PortalAuthController;
use App\Http\Controllers\Portal\PasswordController as PortalPasswordController;
use App\Http\Controllers\Portal\ProjectController as PortalProjectController;
use App\Http\Controllers\Portal\ReceiptController as PortalReceiptController;
use App\Http\Controllers\ProtectedFileController;
use Illuminate\Support\Facades\Route;

// ── Public routes ────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
Route::get('/plans/{housePlan}', [PlanController::class, 'show'])->name('plans.show');

Route::get('/about', [ContentController::class, 'about'])->name('about');
Route::get('/completed-projects', [ContentController::class, 'projects'])->name('projects.index');

Route::get('/blog', [ContentController::class, 'blog'])->name('blog.index');
Route::get('/blog/{slug}', [ContentController::class, 'blogPost'])->name('blog.show');

Route::get('/inquire', [InquiryController::class, 'create'])->name('inquire');
Route::post('/inquire', [InquiryController::class, 'store'])->name('inquire.store');
Route::get('/inquire/success', [InquiryController::class, 'success'])->name('inquire.success');


// ── Client portal ────────────────────────────────────────────
// Confirmed clients only. Uses the separate "customer" guard, so these
// routes and the admin panel can never share a login.
Route::get('/portal/login', [PortalAuthController::class, 'showLogin'])->name('portal.login');
Route::post('/portal/login', [PortalAuthController::class, 'login'])->middleware('throttle:20,1');
Route::post('/portal/logout', [PortalAuthController::class, 'logout'])->name('portal.logout');

Route::middleware(\App\Http\Middleware\CustomerAuth::class)->group(function () {
    Route::get('/portal', [PortalProjectController::class, 'index'])->name('portal.index');
    Route::get('/portal/projects/{project}', [PortalProjectController::class, 'show'])->name('portal.project');
    Route::post('/portal/instalments/{instalment}/receipts', [PortalReceiptController::class, 'store'])->name('portal.receipt');
    Route::get('/portal/password', [PortalPasswordController::class, 'edit'])->name('portal.password');
    Route::put('/portal/password', [PortalPasswordController::class, 'update']);
});

// Private files (receipts, site photos). No middleware here on purpose:
// both clients and admins use these, and the controller checks each request.
Route::get('/files/receipts/{receipt}', [ProtectedFileController::class, 'receipt'])->name('files.receipt');
Route::get('/files/photos/{photo}', [ProtectedFileController::class, 'photo'])->name('files.photo');

// ── Admin auth routes (no middleware) ────────────────────────
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// ── Admin protected routes ───────────────────────────────────
Route::middleware(\App\Http\Middleware\AdminAuth::class)->group(function () {

    Route::get('/admin', fn() => redirect('/admin/dashboard'));
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Clients and construction progress — all admins
    Route::get('/admin/clients', [ClientController::class, 'index'])->name('admin.clients.index');
    Route::get('/admin/clients/create', [ClientController::class, 'create'])->name('admin.clients.create');
    Route::post('/admin/clients', [ClientController::class, 'store'])->name('admin.clients.store');
    Route::get('/admin/clients/{client}', [ClientController::class, 'show'])->name('admin.clients.show');
    Route::put('/admin/clients/{client}', [ClientController::class, 'update'])->name('admin.clients.update');
    Route::post('/admin/clients/{client}/projects', [ClientController::class, 'storeProject'])->name('admin.clients.projects.store');
    Route::post('/admin/clients/{client}/reset-password', [ClientController::class, 'resetPassword'])->name('admin.clients.reset');

    Route::get('/admin/client-projects/{project}', [ClientProjectController::class, 'show'])->name('admin.client-projects.show');
    Route::put('/admin/client-projects/{project}', [ClientProjectController::class, 'update'])->name('admin.client-projects.update');
    Route::post('/admin/client-projects/{project}/stages', [ClientProjectController::class, 'storeStage'])->name('admin.stages.store');
    Route::put('/admin/client-projects/{project}/stages/{stage}', [ClientProjectController::class, 'updateStage'])->name('admin.stages.update');
    Route::post('/admin/client-projects/{project}/stages/{stage}/photos', [ClientProjectController::class, 'storePhotos'])->name('admin.stages.photos');
    Route::delete('/admin/stage-photos/{photo}', [ClientProjectController::class, 'destroyPhoto'])->name('admin.photos.destroy');

    // Every admin can change their own password
    Route::get('/admin/password', [PasswordController::class, 'edit'])->name('admin.password.edit');
    Route::put('/admin/password', [PasswordController::class, 'update'])->name('admin.password.update');

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
    Route::get('/admin/settings', [SettingController::class, 'edit'])->name('admin.settings');
    Route::put('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');

    // Downloadable CSV reports
    Route::get('/admin/reports', [ReportController::class, 'page'])->name('admin.reports');
    Route::get('/admin/reports/inquiries.csv', [ReportController::class, 'inquiries'])->name('admin.reports.inquiries');

    // Admin account management, audit log and commercial reports —
    // primary admins only
    Route::middleware(\App\Http\Middleware\PrimaryAdmin::class)->group(function () {
        Route::get('/admin/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');
        Route::get('/admin/logs', [LoginLogController::class, 'index'])->name('admin.logs');
        Route::get('/admin/reports/plans.csv', [ReportController::class, 'plans'])->name('admin.reports.plans');
        Route::get('/admin/reports/summary.csv', [ReportController::class, 'summary'])->name('admin.reports.summary');
        Route::get('/admin/admins', [AdminUserController::class, 'index'])->name('admin.admins.index');
        Route::get('/admin/admins/create', [AdminUserController::class, 'create'])->name('admin.admins.create');
        Route::post('/admin/admins', [AdminUserController::class, 'store'])->name('admin.admins.store');
        Route::get('/admin/admins/{admin}/edit', [AdminUserController::class, 'edit'])->name('admin.admins.edit');
        Route::put('/admin/admins/{admin}', [AdminUserController::class, 'update'])->name('admin.admins.update');
        // Payments are the client's money — primary admins only
        Route::get('/admin/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
        Route::post('/admin/client-projects/{project}/instalments', [PaymentController::class, 'storeInstalment'])->name('admin.instalments.store');
        Route::delete('/admin/instalments/{instalment}', [PaymentController::class, 'destroyInstalment'])->name('admin.instalments.destroy');
        Route::post('/admin/receipts/{receipt}/verify', [PaymentController::class, 'verify'])->name('admin.receipts.verify');
        Route::post('/admin/receipts/{receipt}/reject', [PaymentController::class, 'reject'])->name('admin.receipts.reject');
        Route::delete('/admin/clients/{client}', [ClientController::class, 'destroy'])->name('admin.clients.destroy');

        Route::post('/admin/admins/{admin}/reset-password', [AdminUserController::class, 'resetPassword'])->name('admin.admins.reset');
        Route::delete('/admin/admins/{admin}', [AdminUserController::class, 'destroy'])->name('admin.admins.destroy');
    });

});
