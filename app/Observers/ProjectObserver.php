<?php

namespace App\Observers;

use App\Models\Project;
use App\Services\Core\AuditLogger;
use Illuminate\Support\Facades\Auth;
use App\DTOs\AuditLogData;
use App\Enums\AuditSeverity;

class ProjectObserver
{
    public function created(Project $project): void
    {
        $user = Auth::user();

        $actor = $user ? "{$user->name} ({$user->email})" : 'System/CLI';

        AuditLogger::log(new AuditLogData(
            action: 'PROJECT_CREATED',
            category: 'project',
            severity: AuditSeverity::INFO,
            target_type: Project::class,
            target_id: $project->id,
            metadata: [
                'name'          => $project->name,
                'branch'        => $project->branch,
                'repository'    => $project->repository_url,
                'created_by'    => $actor,
            ]
        ));
    }

    public function updated(Project $project): void
    {
        $changes = $project->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) return;

        $original = $project->getOriginal();
        $diff = [];

        foreach ($changes as $key => $value) {
            $diff[$key] = [
                'from'  => $original[$key] ?? null,
                'to'    => $value,
            ];
        }

        AuditLogger::log(new AuditLogData(
            action: 'PROJECT_UPDATED',
            category: 'project',
            severity: AuditSeverity::INFO,
            target_type: Project::class,
            target_id: $project->id,
            metadata: [
                'changes'           => $diff,
            ]
        ));
    }

    public function deleted(Project $project): void
    {
        AuditLogger::log(new AuditLogData(
            action: 'PROJECT_TERMINATED',
            category: 'project',
            severity: AuditSeverity::CRITICAL,
            target_type: Project::class,
            target_id: $project->id,
            metadata: [
                'final_name'    => $project->name,
                'reason'        => 'User initiated termination'
            ]
        ));
    }
}
