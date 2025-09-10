<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutoController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesReadyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ActiveSalesController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\TenantTestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CarImageController;
use App\Http\Controllers\MarketplaceController;

// Authentication routes (GEEN LOGIN VEREIST)
Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Redirect root to login
Route::get('/', function() {
    if (session('authenticated')) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('auth.login');
})->name('home');

// Dynamic CSS route for companies (GEEN LOGIN VEREIST)
Route::get('/css/company/{subdomain}.css', [ThemeController::class, 'generateCSS'])->name('company.css');

// ALLE ANDERE ROUTES VEREISEN LOGIN
Route::middleware(['auth.simple', 'tenant'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Auto management
    Route::resource('autos', AutoController::class);
    
    // Pipeline
    Route::get('/pipeline', [PipelineController::class, 'index'])->name('pipeline.index');
    Route::post('/pipeline/move', [PipelineController::class, 'move'])->name('pipeline.move');
    Route::get('/pipeline/checklist/{car}', [PipelineController::class, 'showChecklist'])->name('pipeline.checklist');
    Route::put('/pipeline/checklist/{checklist}', [PipelineController::class, 'updateChecklistItem'])->name('pipeline.checklist.update');

    // Agenda
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
    Route::post('/agenda', [AgendaController::class, 'store'])->name('agenda.store');
    Route::delete('/agenda/{id}', [AgendaController::class, 'destroy'])->name('agenda.destroy');

    // Repairs
    Route::get('/repairs', [RepairController::class, 'index'])->name('repairs.index');
    Route::get('/repairs/create', [RepairController::class, 'create'])->name('repairs.create');
    Route::post('/repairs', [RepairController::class, 'store'])->name('repairs.store');
    Route::get('/repairs/analytics', [RepairController::class, 'analytics'])->name('repairs.analytics');
    Route::get('/repairs/{repair}', [RepairController::class, 'show'])->name('repairs.show');
    Route::get('/repairs/{repair}/edit', [RepairController::class, 'edit'])->name('repairs.edit');
    Route::put('/repairs/{repair}', [RepairController::class, 'update'])->name('repairs.update');
    Route::delete('/repairs/{repair}', [RepairController::class, 'destroy'])->name('repairs.destroy');

    // Parts management
    Route::get('/repairs/{repair}/parts', [RepairController::class, 'partsIndex'])->name('repairs.parts.index');
    Route::post('/repairs/{repair}/parts', [RepairController::class, 'storePart'])->name('repairs.parts.store');
    Route::put('/parts/{part}', [RepairController::class, 'updatePart'])->name('parts.update');
    Route::delete('/parts/{part}', [RepairController::class, 'destroyPart'])->name('parts.destroy');

    // Sales
    Route::resource('sales', SalesController::class);
    Route::post('/sales/{sale}/deliver', [SalesController::class, 'markAsDelivered'])->name('sales.deliver');
    Route::post('/sales/{sale}/cancel', [SalesController::class, 'cancel'])->name('sales.cancel');
    Route::get('/verkoop-klaar', [SalesReadyController::class, 'index'])->name('sales-ready.index');
    Route::get('/actieve-verkoop', [ActiveSalesController::class, 'index'])->name('active-sales.index');

    // Customers
    Route::resource('customers', CustomerController::class);

    // Employees
    Route::resource('employees', EmployeeController::class);
    Route::post('/employees/{employee}/assign-car', [EmployeeController::class, 'assignCar'])->name('employees.assign-car');
    Route::post('/employees/{employee}/assignments/{assignment}/complete', [EmployeeController::class, 'completeAssignment'])->name('employees.complete-assignment');
    Route::post('/employees/{employee}/assignments/{assignment}/cancel', [EmployeeController::class, 'cancelAssignment'])->name('employees.cancel-assignment');

    // Car Images
    Route::post('/cars/{car}/images', [CarImageController::class, 'store'])->name('cars.images.store');
    Route::delete('/images/{carImage}', [CarImageController::class, 'destroy'])->name('cars.images.destroy');
    Route::post('/images/{carImage}/make-primary', [CarImageController::class, 'makePrimary'])->name('cars.images.make-primary');
    Route::post('/cars/{car}/images/reorder', [CarImageController::class, 'updateOrder'])->name('cars.images.reorder');

    // Marketplace Publishing
    Route::get('/cars/{car}/marketplace', [MarketplaceController::class, 'show'])->name('cars.marketplace.show');
    Route::post('/cars/{car}/marketplace/preview', [MarketplaceController::class, 'preview'])->name('cars.marketplace.preview');
    Route::post('/cars/{car}/listings', [MarketplaceController::class, 'createListing'])->name('cars.listings.create');
    Route::post('/listings/{listing}/publish', [MarketplaceController::class, 'publishListing'])->name('listings.publish');
    Route::get('/listings/{listing}/preview', [MarketplaceController::class, 'previewListing'])->name('listings.preview');
    Route::delete('/listings/{listing}', [MarketplaceController::class, 'deleteListing'])->name('listings.delete');

    // Reports
    Route::get('/rapportage', [ReportsController::class, 'index'])->name('reports.index');

    // Theme settings
    Route::get('/admin/theme', [ThemeController::class, 'showThemeSettings'])->name('admin.theme');
    Route::post('/admin/theme', [ThemeController::class, 'updateThemeSettings'])->name('admin.theme.update');

    // Company Settings
    Route::get('/company-settings', [App\Http\Controllers\CompanySettingsController::class, 'index'])->name('company-settings.index');
    Route::put('/company-settings', [App\Http\Controllers\CompanySettingsController::class, 'update'])->name('company-settings.update');

    // Tenant test routes (for testing only)
    Route::get('/tenant-test', [TenantTestController::class, 'index'])->name('tenant.test');
    Route::get('/set-tenant/{company}', [TenantTestController::class, 'setTenant'])->name('tenant.set');
});

// OAuth routes (outside auth middleware)
Route::get('/oauth/marketplace/redirect', [App\Http\Controllers\MarketplaceAuthController::class, 'redirect'])
    ->name('oauth.marketplace.redirect');
Route::get('/oauth/marketplace/callback', [App\Http\Controllers\MarketplaceAuthController::class, 'callback'])
    ->name('oauth.marketplace.callback');

// API Integration routes (with tenant context)
Route::middleware(['auth.simple', App\Http\Middleware\TenantContext::class])->group(function () {
    // Listing publishing API
    Route::post('/tenants/{tenant}/listings/{id}/publish', [App\Http\Controllers\ListingPublishController::class, 'publishListing'])
        ->name('api.listings.publish');
    
    Route::get('/tenants/{tenant}/listings/{id}/status', [App\Http\Controllers\ListingPublishController::class, 'getListingStatus'])
        ->name('api.listings.status');
    
    // Analytics API
    Route::get('/tenants/{tenant}/analytics/active-users', [App\Http\Controllers\ListingPublishController::class, 'getActiveUsers'])
        ->name('api.analytics.active-users');
});