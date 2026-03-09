<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

// --- CONSOLE CONTROLLERS (User Area) ---
use App\Http\Controllers\Console\DashboardController;
use App\Http\Controllers\Console\ProjectController;
use App\Http\Controllers\Console\DeploymentController;

// --- ADMIN CONTROLLERS (SysAdmin Area) ---
use App\Http\Controllers\Admin\UserApprovalController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SystemLogController;
use App\Http\Controllers\Admin\ServerManagementController;
use App\Http\Controllers\Admin\ColdStorageController;
use App\Http\Controllers\Admin\EnvironmentController;
use App\Http\Controllers\Admin\ProjectMemberController;

// --- SECURITY CONTROLLERS ---
use App\Http\Controllers\Security\MasterKeyController;

// --- OTHER IMPORT ---
use App\Models\Server; // <--- TAMBAHKAN INI

// ====================================================
// GUEST ONLY (Login/Register)
// ====================================================
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

// ====================================================
// AUTH REQUIRED (Global)
// ====================================================
Route::middleware('auth')->group(function () {

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::prefix('internal')->name('internal.')->group(function () {
        Route::get('/servers-list', function () {
            return \App\Models\Server::where('status', 'active')
                ->select('id', 'name', 'ip_address', 'description')
                ->get();
        })->name('servers-list');
    });

    // ====================================================
    // 1. CONSOLE AREA (For Normal Users: Dev, QA, Manager)
    // URL Prefix: /console/.....
    // Route Name: console.xxxxx
    // ====================================================
    Route::prefix('console')->name('console.')->group(function () {

        // Dashboard Utama
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Project Management
        Route::resource('projects', ProjectController::class);

        // Fetch Git Branch
        Route::post('/projects/fetch-branches', [ProjectController::class, 'fetchBranches'])->name('projects.fetch-branches');

        // Project Environment
        Route::prefix('projects/{project}/environments')->name('projects.environments.')->group(function () {
            Route::post('/', [EnvironmentController::class, 'store'])->name('store');
            Route::patch('/{environment}', [EnvironmentController::class, 'update'])->name('update');
            Route::delete('/{environment}', [EnvironmentController::class, 'destroy'])->name('destroy');
            Route::post('/{environment}/start', [EnvironmentController::class, 'start'])->name('start');
            Route::post('/{environment}/stop', [EnvironmentController::class, 'stop'])->name('stop');

            // Deployment
            Route::post('/{environment}/deploy', [DeploymentController::class, 'store'])->name('deploy');
            Route::get('/{environment}/logs', [DeploymentController::class, 'logs'])->name('logs');
        });
    });

    // ====================================================
    // 2. PROJECT MEMBER MANAGEMENT (Shared Access)
    // Akses: Owner (User Biasa) & SysAdmin
    // URL Prefix: /projects/{project}/members
    // Route Name: projects.members.xxxxx
    // ====================================================
    Route::prefix('projects/{project}/members')->name('projects.members.')->group(function () {
        Route::get('/search', [ProjectMemberController::class, 'search'])->name('search');
        Route::post('/', [ProjectMemberController::class, 'store'])->name('store');
        Route::patch('/{user}', [ProjectMemberController::class, 'update'])->name('update');
        Route::delete('/{user}', [ProjectMemberController::class, 'destroy'])->name('destroy');
    });

    // ====================================================
    // 3. ADMIN AREA (System Administrator Only)
    // URL Prefix: /admin/.....
    // Route Name: admin.xxxxx
    // ====================================================
    Route::middleware(['role:System Administrator'])->prefix('admin')->name('admin.')->group(function () {

        // Access Pipeline
        Route::prefix('approvals')->name('approvals.')->group(function () {
            Route::get('/', [UserApprovalController::class, 'index'])->name('index');
            Route::post('/{approval}/approve', [UserApprovalController::class, 'approve'])->name('approve');
            Route::post('/{approval}/reject', [UserApprovalController::class, 'reject'])->name('reject');
        });

        // Identity Management
        Route::controller(UserManagementController::class)->group(function () {
            Route::get('/users', 'index')->name('users.index');
            Route::post('/users', 'store')->name('users.store');

            Route::get('/users/{user}/edit', 'edit')->name('users.edit');
            Route::patch('/users/{user}', 'update')->name('users.update');
            Route::delete('/users/{user}', 'destroy')->name('users.destroy');
            Route::get('/users/archived', 'archived')->name('users.archived');
            Route::patch('/users/{id}/restore', 'restore')->name('users.restore');
        });

        // System Logs
        Route::get('/logs', [SystemLogController::class, 'index'])->name('logs.index');

        // Security Settings
        Route::prefix('security')->name('security.')->group(function () {
            Route::prefix('master-key')->name('master-key.')->group(function () {
                Route::get('/', [MasterKeyController::class, 'index'])->name('index');
                Route::post('/rotate', [MasterKeyController::class, 'store'])->name('rotate');
            });
        });

        // Cold Storage
        Route::prefix('cold-storage')->name('cold-storage.')->group(function () {
            Route::get('/{type}', [ColdStorageController::class, 'index'])->name('index');
            Route::get('/{type}/{filename}/download', [ColdStorageController::class, 'download'])->name('download');
            Route::post('/{type}/{filename}/restore', [ColdStorageController::class, 'restore'])->name('restore');
        });

        // Server Inventory
        Route::controller(ServerManagementController::class)->group(function () {
            Route::get('/servers', 'index')->name('servers.index');
            Route::post('/servers', 'store')->name('servers.store');
            Route::get('/servers/archived', 'archived')->name('servers.archived');
            Route::get('/servers/{server}/test-link', 'testLink')->name('servers.test-link');
            Route::post('/servers/{server}/deploy-key', 'deployKey')->name('servers.deploy-key');
            Route::put('/servers/{server}', 'update')->name('servers.update');
            Route::delete('/servers/{server}', 'destroy')->name('servers.destroy');
            Route::patch('/servers/{id}/restore', 'restore')->name('servers.restore');
        });
    });

    // Redirect root
    Route::get('/', function () {
        return Auth::check() ? redirect()->route('console.dashboard') : redirect()->route('login');
    });
});
