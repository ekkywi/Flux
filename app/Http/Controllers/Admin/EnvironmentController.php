<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreEnvironmentRequest;
use App\Models\Project;
use App\Models\Environment;
use App\Jobs\StopEnvironment;
use Illuminate\Support\Facades\Auth;

class EnvironmentController extends Controller
{
    public function store(StoreEnvironmentRequest $request, Project $project)
    {
        $project->environments()->create($request->validated());

        return back()->with('success', 'New environment provisioned successfully.');
    }

    public function destroy(Project $project, Environment $environment)
    {
        $this->authorize('update', $project);

        if ($environment->project_id !== $project->id) {
            abort(404);
        }

        if ($project->environments()->count() <= 1) {
            return back()->with('error', 'Cannot delete the only environment available. Terminate the project instead.');
        }

        if ($environment->type === 'production') {
            $user = Auth::user();
            $isOwner = $project->members()
                ->where('user_id', $user->id)
                ->wherePivot('role', 'owner')
                ->exists();
            $isSysAdmin = $user->role === 'System Administrator';

            if (!$isOwner && !$isSysAdmin) {
                return back()->with('error', 'Access Denied: Only the Project Owner can delete the Production environment.');
            }
        }

        $environment->delete();

        return back()->with('success', 'Environment has been de-provisioned.');
    }

    public function stop(Project $project, Environment $environment)
    {
        $environment->update(['status' => 'stopping']);

        StopEnvironment::dispatch($environment);

        return back()->with('success', 'Stop signal send to servers. Environment is shutting down.');
    }
}
