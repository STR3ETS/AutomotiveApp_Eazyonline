<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutoController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\DashboardController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\RepairController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('autos', AutoController::class);
Route::get('/pipeline', [PipelineController::class, 'index'])->name('pipeline.index');
Route::post('/pipeline/move', [PipelineController::class, 'move'])->name('pipeline.move');
Route::get('/pipeline/checklist/{car}', [PipelineController::class, 'checklist'])->name('pipeline.checklist');
Route::post('/pipeline/checklist/{car}/toggle', [PipelineController::class, 'toggleChecklistItem'])->name('pipeline.checklist.toggle');
Route::post('/pipeline/checklist/{car}/add-task', [PipelineController::class, 'addTask'])->name('pipeline.checklist.add-task');

Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
Route::post('/agenda', [AgendaController::class, 'store'])->name('agenda.store');
Route::delete('/agenda/{id}', [AgendaController::class, 'destroy'])->name('agenda.destroy');


Route::get('/repairs', [RepairController::class, 'index'])->name('repairs.index');
Route::post('/repairs', [RepairController::class, 'store'])->name('repairs.store');
Route::put('/repairs/{repair}', [RepairController::class, 'update'])->name('repairs.update');
Route::delete('/repairs/{repair}', [RepairController::class, 'destroy'])->name('repairs.destroy');

Route::post('/repairs/{repair}/parts', [RepairController::class, 'addPart'])->name('repairs.parts.store');
Route::put('/parts/{part}', [RepairController::class, 'updatePart'])->name('parts.update');