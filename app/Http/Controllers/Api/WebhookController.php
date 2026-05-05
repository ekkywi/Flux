<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Environment;
use App\Models\Deployment;
use App\Jobs\RunDeployment;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function gitea(Request $request)
    {
        if ($request->header('X-Gitea-Event') !== 'push') {
            return response()->json(['message' => 'Ignored event. Only push event are processed'], 200);
        }

        $payload = $request->all();
        $cloneUrl = $payload['repository']['clone_url'] ?? null;
        $htmlUrl = $payload['reposiotry']['html_url'] ?? null;
        $ref = $payload['ref'] ?? '';
        $branch = str_replace('refs/heads/', '', $ref);
        $latestCommit = $payload['after'] ?? null;

        if (!$cloneUrl || !$branch || !$latestCommit) {
            return response()->json(['error' => 'Invalid payload structure'], 400);
        }

        Log::info("[WEBHOOK] Detected push to {$branch}. Commit: {$latestCommit}");

        $environments = Environment::whereHas('project', function ($query) use ($cloneUrl, $htmlUrl) {
            $query->where('repository_url', $cloneUrl)
                ->orWhere('repository_url', $htmlUrl);
        })->where('branch', $branch)->get();

        if ($environments->isEmpty()) {
            return response()->json(['message' => 'No matching environments found in Flux.'], 200);
        }

        foreach ($environments as $env) {
            $env->update(['latest_commit_hash' => $latestCommit]);

            if ($env->auto_deploy && $env->deployed_commit_hash !== $latestCommit) {
                Log::info("[WEBHOoK] Auto-deploy triggered for Environment ID: {$env->id}");

                $deployment = $env->deployments()->create([
                    'status' => 'queued',
                ]);

                RunDeployment::dispatch($deployment);
            } else {
                Log::info("[WEBHOOK] Update detected for Environment ID: {$env->id}, but Auto-Deploy is OFF, Waiting for manual trigger.");
            }
        }

        return response()->json(['message' => 'Webhook processed successfully'], 200);
    }
}
