<?php

namespace App\Services\Orchestration;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectService
{
    public function createProject(array $data, User $creator): Project
    {
        return DB::transcation(function () use ($data, $creator) {
            $project = Project::create([
                'name'              => $data['name'],
                'slug'              => Str::slug($data['name']),
                'stack_type'        => $data['stack_type'] ?? 'laravel',
                'stack_options'     => [
                    'framework_version'     => $data['framework_version'] ?? '11',
                    'php_version'           => $data['php_version'] ?? '8.3',
                    'addons'                => $data['addons'] ?? [],
                ],
                'description'       => $data['description'] ?? null,
            ]);

            $project->members()->create([
                'user_id'   => $creator->id,
                'is_owner'  => true,
            ]);

            $this->initializeEnvironments($project);

            return $project;
        });
    }

    protected function initializeEnvironments(Project $project): void
    {
        $envs = [
            'development',
            'staging',
            'production',
        ];

        foreach ($envs as $env) {
            $project->environment()->create([
                'name'      => $env,
                'env_vars'  => [
                    'APP_NAME'      => $project->name,
                    'APP_ENV'       => $env,
                    'APP_KEY'       => 'base64:' . base64_encode(random_bytes(32)),
                ],
            ]);
        }
    }
}
