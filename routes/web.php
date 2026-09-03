<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ReportController;

Route::get('/', fn() => redirect()->route('inventory.index'));

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    // ── Inventory (Assets) ─────────────────────────────────────────
    Route::resource('inventory', AssetController::class)->except(['show', 'create', 'edit']);
    Route::get('/inventory/stats',   [AssetController::class, 'stats'])   ->name('inventory.stats');
    Route::get('/inventory/monthly', [AssetController::class, 'monthly']) ->name('inventory.monthly');
    Route::get('/inventory/search',  [AssetController::class, 'search'])  ->name('inventory.search');

    // ── Categories ─────────────────────────────────────────────────
    Route::get   ('/categories',            [CategoryController::class, 'index'])   ->name('categories.index');
    Route::post  ('/categories',            [CategoryController::class, 'store'])   ->name('categories.store');
    // jQuery AJAX sends POST with _method=DELETE, so register both verbs
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post  ('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy.post');

    // ── Brands ─────────────────────────────────────────────────────
    Route::get   ('/brands',        [BrandController::class, 'index'])   ->name('brands.index');
    Route::post  ('/brands',        [BrandController::class, 'store'])   ->name('brands.store');
    Route::delete('/brands/{brand}',[BrandController::class, 'destroy']) ->name('brands.destroy');
    Route::post  ('/brands/{brand}',[BrandController::class, 'destroy']) ->name('brands.destroy.post');

    // ── Reports ────────────────────────────────────────────────────
    Route::get('/reports',            [ReportController::class, 'index'])    ->name('reports.index');
    Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.csv');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

});
