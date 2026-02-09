<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateProjectAction
{
    public function execute(array $data, User $creator): Project
    {
        return DB::transaction(function () use ($data, $creator) {

            $project = Project::create([
                'name'              => $data['name'],
                'repository_url'    => $data['repository_url'],
                'description'       => $data['description'] ?? null,
                'default_branch'    => 'main',
            ]);

            $project->members()->attach($creator->id, ['role' => 'owner']);

            $project->environments()->create([
                'name'      => 'Production',
                'branch'    => 'main',
                'type'      => 'production',
            ]);

            return $project;
        });
    }
}
