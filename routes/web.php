<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutoController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\DashboardController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesReadyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ActiveSalesController;
use App\Http\Controllers\ReportsController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('autos', AutoController::class);
Route::get('/pipeline', [PipelineController::class, 'index'])->name('pipeline.index');
Route::post('/pipeline/move', [PipelineController::class, 'move'])->name('pipeline.move');
Route::get('/pipeline/checklist/{car}', [PipelineController::class, 'showChecklist'])->name('pipeline.checklist');
Route::put('/pipeline/checklist/{checklist}', [PipelineController::class, 'updateChecklistItem'])->name('pipeline.checklist.update');

Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
Route::post('/agenda', [AgendaController::class, 'store'])->name('agenda.store');
Route::delete('/agenda/{id}', [AgendaController::class, 'destroy'])->name('agenda.destroy');


Route::get('/repairs', [RepairController::class, 'index'])->name('repairs.index');
Route::get('/repairs/create', [RepairController::class, 'create'])->name('repairs.create');
Route::post('/repairs', [RepairController::class, 'store'])->name('repairs.store');
Route::get('/repairs/analytics', [RepairController::class, 'analytics'])->name('repairs.analytics');
Route::get('/repairs/{repair}', [RepairController::class, 'show'])->name('repairs.show');
Route::get('/repairs/{repair}/edit', [RepairController::class, 'edit'])->name('repairs.edit');
Route::put('/repairs/{repair}', [RepairController::class, 'update'])->name('repairs.update');
Route::delete('/repairs/{repair}', [RepairController::class, 'destroy'])->name('repairs.destroy');

// Parts management routes
Route::get('/repairs/{repair}/parts', [RepairController::class, 'partsIndex'])->name('repairs.parts.index');
Route::post('/repairs/{repair}/parts', [RepairController::class, 'storePart'])->name('repairs.parts.store');
Route::put('/parts/{part}', [RepairController::class, 'updatePart'])->name('parts.update');
Route::delete('/parts/{part}', [RepairController::class, 'destroyPart'])->name('parts.destroy');

Route::resource('sales', SalesController::class);
Route::post('/sales/{sale}/deliver', [SalesController::class, 'markAsDelivered'])->name('sales.deliver');
Route::post('/sales/{sale}/cancel', [SalesController::class, 'cancel'])->name('sales.cancel');

Route::get('/verkoop-klaar', [SalesReadyController::class, 'index'])->name('sales-ready.index');

Route::resource('customers', CustomerController::class);

Route::get('/actieve-verkoop', [ActiveSalesController::class, 'index'])->name('active-sales.index');

Route::get('/rapportage', [ReportsController::class, 'index'])->name('reports.index');