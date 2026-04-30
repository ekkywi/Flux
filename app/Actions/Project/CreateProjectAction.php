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

            $stack = strtolower($data['stack']);

            $buildOptions = [
                'database_type'     => $data['database_type'] ?? 'sqlite',
                'database_version'  => $data['database_version'] ?? '15-alpine',
            ];

            if ($stack === 'laravel' || $stack === 'php') {
                $buildOptions['php_version'] = $data['php_version'] ?? '8.4';
            }

            // 3. Simpan ke database
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
