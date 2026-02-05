<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Orchestration\ProjectController;
use App\Http\Controllers\Integrations\GiteaWebhookController;

Route::post('/webhooks/gitea', [GiteaWebhookController::class, 'handle']);

// API untuk CLI Tool atau Integrasi lain
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/projects', [ProjectController::class, 'index']);
    Route::post('/projects/onboard', [ProjectController::class, 'store']);
    Route::post('/environments/{environment}/assign-server', [ProjectController::class, 'assignServer']);
    Route::post('/environments/{environment}/deploy', [ProjectController::class, 'deploy']);
});
