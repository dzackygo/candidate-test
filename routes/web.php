<?php

use App\Http\Controllers\CltLayerController;
use App\Http\Controllers\CltLayupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierImportConflictController;
use App\Http\Controllers\SupplierImportExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/suppliers/{supplier}/export', [SupplierImportExportController::class, 'export'])->name('suppliers.export');
    Route::post('/suppliers/{supplier}/import', [SupplierImportExportController::class, 'import'])->name('suppliers.import');
    Route::get('/suppliers/{supplier}/import-conflicts', [SupplierImportConflictController::class, 'show'])->name('suppliers.import-conflicts.show');
    Route::post('/suppliers/{supplier}/import-conflicts', [SupplierImportConflictController::class, 'resolve'])->name('suppliers.import-conflicts.resolve');
    Route::resource('suppliers', SupplierController::class);
    Route::resource('suppliers.layups', CltLayupController::class)
        ->parameters(['layups' => 'layup']);
    Route::resource('suppliers.layups.layers', CltLayerController::class)
        ->except('show')
        ->parameters(['layups' => 'layup', 'layers' => 'layer']);
});

require __DIR__.'/auth.php';
