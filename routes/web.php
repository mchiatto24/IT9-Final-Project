<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\RoomAuditController;
use App\Http\Controllers\AssignedItemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InventoryLogController;  // Add this import

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('guests', GuestController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('bookings', BookingController::class);
    Route::resource('inventory', InventoryController::class);

    // Suppliers route protected by role check
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('suppliers', SupplierController::class);
    });

    Route::resource('room_audits', RoomAuditController::class);
    Route::resource('assigned-items', AssignedItemController::class);

    // Inventory Log routes
    Route::resource('inventory-logs', InventoryLogController::class);

    // Reports routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/flush', [ReportController::class, 'flush'])->name('reports.flush');
});

require __DIR__.'/auth.php';
