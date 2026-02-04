<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Orchestration\ProjectController;
use App\Http\Controllers\Integrations\GiteaWebhookController;

Route::post('/webhooks/gitea', [GiteaWebhookController::class, 'handle']);

// API untuk CLI Tool atau Integrasi lain
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/projects', [ProjectController::class, 'index']);
});
