<?php

namespace App\Observers;

use App\Models\ProjectMember;
use App\Models\Project;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use App\Enums\AuditSeverity;
use Illuminate\Support\Facades\Auth;

class ProjectMemberObserver
{
    public function created(ProjectMember $member): void
    {
        $actor = Auth::user();

        AuditLogger::log(new AuditLogData(
            action: 'MEMBER_ADDED',
            category: 'access_control',
            severity: AuditSeverity::INFO,
            target_type: Project::class,
            target_id: $member->project_id,
            metadata: [
                'project_name'  => $member->project->name ?? 'Unknown',
                'member_email'  => $member->user->email ?? 'Unknown',
                'assigned_role' => $member->role,
                'added_by'      => $actor ? "{$actor->name} ({$actor->email})" : 'System',
            ]
        ));
    }

    public function updated(ProjectMember $member): void
    {
        if ($member->isDirty('role')) {
            $actor = Auth::user();

            AuditLogger::log(new AuditLogData(
                action: 'MEMBER_ROLE_UPDATED',
                category: 'access_control',
                severity: AuditSeverity::WARNING,
                target_type: Project::class,
                target_id: $member->project_id,
                metadata: [
                    'project_name'  => $member->project->name ?? 'Unknown',
                    'member_email'  => $member->user->email ?? 'Unknown',
                    'changes'       => [
                        'role'      => [
                            'from'  => $member->getOriginal('role'),
                            'to'    => $member->role
                        ]
                    ],
                    'updated_by'    => $actor ? "{$actor->name} ({$actor->email})" : 'System',
                ]
            ));
        }
    }

    public function deleted(ProjectMember $member): void
    {
        $actor = Auth::user();

        AuditLogger::log(new AuditLogData(
            action: 'MEMBER_REMOVED',
            category: 'access_control',
            severity: AuditSeverity::CRITICAL,
            target_type: Project::class,
            target_id: $member->project_id,
            metadata: [
                'project_name'      => $member->project->name ?? 'Unknown Project',
                'member_email'      => $member->user->email ?? 'Unknown User',
                'removed_role'      => $member->role,
                'removed_by'        => $actor ? "{$actor->name} ({$actor->email})" : 'System',
            ]
        ));
    }
}
