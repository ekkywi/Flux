<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Environment;
use App\Models\Deployment;
use App\Jobs\RunDeployment;
use Illuminate\Support\Facades\Auth;

class DeploymentController extends Controller
{
    public function store(Request $request, Environment $environment)
    {
        $isDeploying = $environment->deployments()
            ->whereIn('status', ['queued', 'running'])
            ->exist();

        if ($isDeploying) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'A deployment is already in progress for this node.'
            ], 422);
        }

        $deployment = Deployment::create([
            'environment_id'    => $environment->id,
            'user_id'           => Auth::id(),
            'status'            => 'queued',
        ]);

        RunDeployment::dispatch($deployment);

        return response()->json([
            'status'            => 'success',
            'message'           => 'Deployment has been queued successfully.',
            'deployment_id'     => $deployment->id
        ]);
    }
}
