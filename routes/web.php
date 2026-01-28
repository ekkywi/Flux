<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Console\DashboardController;
use App\Http\Controllers\Admin\UserApprovalController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SystemLogController;
use App\Http\Controllers\Security\MasterKeyController;

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

        // 1. Access Pipeline (Fixed)
        Route::prefix('approvals')->name('approvals.')->group(function () {
            Route::get('/', [UserApprovalController::class, 'index'])->name('index');
            Route::post('/{approval}/approve', [UserApprovalController::class, 'approve'])->name('approve');
            Route::post('/{approval}/reject', [UserApprovalController::class, 'reject'])->name('reject');
        });

        // 2. Identity Management (User Directory)
        Route::controller(UserManagementController::class)->group(function () {
            Route::get('/users', 'index')->name('users.index');
            Route::post('/users', 'store')->name('users.store'); // Jalur untuk Provisioning (tolong di eling eling bos)

            // Cadangan untuk fitur edit/delete nanti
            Route::get('/users/{user}/edit', 'edit')->name('users.edit');
            Route::patch('/users/{user}', 'update')->name('users.update');
            Route::delete('/users/{user}', 'destroy')->name('users.destroy');
            Route::get('/users/archived', [UserManagementController::class, 'archived'])->name('users.archived');
            Route::patch('/users/{id}/restore', [UserManagementController::class, 'restore'])->name('users.restore');
        });

        // 3. System Protocol (Audit & Activity Logs)
        Route::get('/logs', [SystemLogController::class, 'index'])->name('logs.index');


        // 4. Security Settings
        Route::prefix('security')->name('security.')->group(function () {

            // Master Key Management
            Route::prefix('master-key')->name('master-key.')->group(function () {
                Route::get('/', [MasterKeyController::class, 'index'])->name('index');
                Route::post('/rotate', [MasterKeyController::class, 'store'])->name('rotate');
            });
        });
    });

    // Redirect root ke login atau dashboard
    Route::get('/', function () {
        return Auth::check() ? redirect()->route('console.dashboard') : redirect()->route('login');
    });
});
