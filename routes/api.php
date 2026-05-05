<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebhookController;

Route::post('/webhooks/gitea', [WebhookController::class, 'gitea']);
