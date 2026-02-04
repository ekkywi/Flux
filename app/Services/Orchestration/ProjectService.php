<?php

namespace App\Services\Orchestration;

use App\Models\Project;
use App\Models\User;
use App\Models\ProjectEnvironment;
use App\Services\Stacks\StackFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class ProjectService
{
    public function onboardProject(array $data, User $creator): Project
    {
        $stack = StackFactory::make($data['stack_type'] ?? 'laravel');

        if (!$stack->validateRepository($data['repository_url'])) {
            throw new Exception("Repository is invalid or not a project " . ucfirst($data['stack_type']));
        }

        return DB::transaction(function () use ($data, $creator, $stack) {
            $project = Project::create([
                'name'              => $data['name'],
                'slug'              => Str::slug($data['name']),
                'repository_url'    => $data['repository_url'],
                'stack_type'        => $data['stack_type'] ?? 'laravel',
                'stack_options'     => $data['stack_options'] ?? [],
                'description'       => $data['description'] ?? null,
            ]);

            $analysis = $stack->analyzeRisk($project->repository_url, $project->stack_options);

            $project->update([
                'onboarding_status' => $analysis['has_docker'] ? 'ready' : 'needs_injection'
            ]);

            $project->members()->create([
                'user_id'   => $creator->id,
                'is_owner'  => true,
            ]);

            $this->initializeEnvironment($project);

            return $project;
        });
    }

    protected function initializeEnvironment(Project $project): void
    {
        $envs = ['development', 'staging', 'production'];

        foreach ($envs as $index => $env) {
            $lastPort = ProjectEnvironment::max('assigned_port') ?? 8999;
            $newPort = $lastPort + 1;

            $project->environment()->create([
                'name'          => $env,
                'assigned_port' => $newPort,
                'env_vars'      => [
                    'APP_NAME'          => $project->name,
                    'APP_ENV'           => $env,
                    'APP_KEY'           => 'base64:' . base64_encode(random_bytes(32)),
                    'CONTAINER_NAME'    => "flux_{$project->slug}_{$env}",
                ],
            ]);
        }
    }
}
