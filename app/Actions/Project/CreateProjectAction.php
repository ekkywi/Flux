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

            $project = Project::create([
                'name'              => $data['name'],
                'repository_url'    => $data['repository_url'],
                'description'       => $data['description'] ?? null,
                'default_branch'    => $data['branch'],
                'status'            => ProjectStatus::ACTIVE->value,
            ]);

            $project->members()->attach($creator->id, ['role' => 'owner']);

            $project->environments()->create([
                'name'      => 'Development',
                'branch'    => $data['branch'],
                'type'      => 'development',
            ]);

            return $project;
        });
    }
}
