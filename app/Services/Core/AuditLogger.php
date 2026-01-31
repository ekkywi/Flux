<?php

namespace App\Services\Core;

use App\Models\AuditLog;
use App\DTOs\AuditLogData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function log(AuditLogData $data): ?AuditLog
    {
        try {
            return AuditLog::create([
                'user_id' => $data->user_id ?? Auth::id(),
                'action' => $data->action,
                'category' => $data->category,
                'severity' => $data->severity,
                'target_type' => $data->target_type,
                'target_id' => $data->target_id,
                'metadata' => $data->metadata,
                'correlation_id' => $data->correlation_id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error("Audit Log Failure: " . $e->getMessage(), [
                'action' => $data->action,
                'data' => (array) $data
            ]);

            return null;
        }
    }
}
