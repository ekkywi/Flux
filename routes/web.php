<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Console\DashboardController;
use App\Http\Controllers\Admin\UserApprovalController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SystemLogController;

// --- GUEST ONLY ---
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

// --- AUTH REQUIRED ---
Route::middleware('auth')->group(function () {

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Grouping Console (Untuk semua user yang sudah aktif)
    Route::prefix('console')->name('console.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware(['auth', 'role:System Administrator'])->prefix('admin')->name('admin.')->group(function () {

        // 1. Access Pipeline (Existing)
        Route::get('/approvals', [UserApprovalController::class, 'index'])->name('approvals.index');

        // 2. Identity Management (User Directory)
        Route::controller(UserManagementController::class)->group(function () {
            Route::get('/users', 'index')->name('users.index');
            Route::post('/users', 'store')->name('users.store'); // Jalur untuk Provisioning

            // Cadangan untuk fitur edit/delete nanti
            Route::get('/users/{user}/edit', 'edit')->name('users.edit');
            Route::patch('/users/{user}', 'update')->name('users.update');
            Route::delete('/users/{user}', 'destroy')->name('users.destroy');
        });

        // 3. System Protocol (Audit & Activity Logs)
        Route::get('/logs', [SystemLogController::class, 'index'])->name('logs.index');
    });
});

// Redirect root ke login atau dashboard
Route::get('/', function () {
    return Auth::check() ? redirect()->route('console.dashboard') : redirect()->route('login');
});
