<?php

namespace App\Observers;

use App\Models\Environment;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;
use App\Enums\AuditSeverity;

class EnvironmentObserver
{
    public function created(Environment $environment): void
    {
        $severity = $environment->type === 'production'
            ? AuditSeverity::WARNING
            : AuditSeverity::INFO;

        AuditLogger::log(new AuditLogData(
            action: 'NODE_PROVISIONED',
            category: 'infrastructure',
            severity: $severity,
            target_type: Environment::class,
            target_id: $environment->id,
            metadata: [
                'project_id'        => $environment->project_id,
                'project_name'      => $environment->project->name ?? 'Unknown',
                'env_name'          => $environment->name,
                'type'              => $environment->type,
                'branch_target'     => $environment->branch,
            ]
        ));
    }

    public function deleted(Environment $environment): void
    {
        $severity = $environment->type === 'production'
            ? AuditSeverity::CRITICAL
            : AuditSeverity::WARNING;

        AuditLogger::log(new AuditLogData(
            action: 'NODE_TEARDOWN',
            category: 'infrastructure',
            severity: $severity,
            target_type: Environment::class,
            target_id: $environment->id,
            metadata: [
                'project_name'  => $environment->project->name ?? 'Deleted Project',
                'env_name'      => $environment->name,
                'type'          => $environment->type,
            ]
        ));
    }
}
