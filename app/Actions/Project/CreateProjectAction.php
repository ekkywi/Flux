<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Enums\ProjectStatus;

class CreateProjectAction
{
    public function execute(array $data, User $creator): Project
    {
        return DB::transaction(function () use ($data, $creator) {

            $buildOptions = [];
            $stack = strtolower($data['stack']);

            if ($stack === 'laravel' || $stack === 'php') {
                $buildOptions['php_version'] = $data['php_version'] ?? '8.4';
            }

            $buildOptions['database_type'] = $data['database_type'] ?? 'sqlite';

            $project = Project::create([
                'name'              => $data['name'],
                'repository_url'    => $data['repository_url'],
                'description'       => $data['description'] ?? null,
                'default_branch'    => $data['branch'],
                'status'            => ProjectStatus::ACTIVE->value,
                'stack'             => $stack,
                'build_options'     => $buildOptions,
            ]);

            $project->members()->attach($creator->id, ['role' => 'owner']);

            return $project;
        });
    }
}
