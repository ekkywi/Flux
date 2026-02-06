<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\Integrations\GiteaIntegrationService;

class GiteaController extends Controller
{
    public function getBranches(Project $project, GiteaIntegrationService $service)
    {
        $branches = $service->getBranches($project->repository_url);

        return response()->json([
            'status'    => 'success',
            'branches'  => $branches
        ]);
    }
}
