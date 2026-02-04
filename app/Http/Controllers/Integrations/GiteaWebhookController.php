<?php

namespace App\Http\Controllers\Integrations;

use App\Http\Controllers\Controller;
use App\Services\Orchestration\DeploymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GiteaWebhookController extends Controller
{
    public function __construct(
        protected DeploymentService $deploymentService
    ) {}

    public function handle(Request $request)
    {
        $secret = $request->header('X-Gitea-Token');
        if ($secret !== config('services.gitea.webhook_token')) {
            Log::warning("Unauthorized Webhook Attempt from IP: " . $request->ip());
            return response()->json(['message' => 'Invalid Secret'], 403);
        }

        $event = $request->header('X-Gitea-Event');
        Log::info("Gitea Webhook Received: {$event}");

        if ($event === 'push') {
            try {
                $this->deploymentService->handlePushEvent($request->all());
            } catch (\Exception $e) {
                Log::error("Dployment Error: " . $e->getMessage());
                return response()->json(['message' => 'Deployment Failed'], 500);
            }
        }

        return response()->json(['status' => 'processed'], 202);
    }
}
