<?php

namespace App\Actions\Admin;

use App\Models\AuditLog;
use App\Enums\AuditSeverity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FetchAuditLogsAction
{
    public function execute(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return AuditLog::with('user')
            ->latest()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('action', 'ILIKE', "%{$search}%")
                        ->orWhere('category', 'ILIKE', "%{$search}%")
                        ->orWhere('ip_address', 'ILIKE', "%{$search}%")
                        ->orWhereRaw('metadata::text ILIKE ?', ["%{$search}%"])
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('first_name', 'ILIKE', "%{$search}%")
                                ->orWhere('username', 'ILIKE', "%{$search}%")
                                ->orWhere('email', 'ILIKE', "%{$search}%");
                        });
                });
            })
            ->when($filters['severity'] ?? null, function ($query, $severity) {
                if ($sev = AuditSeverity::tryFrom($severity)) {
                    $query->where('severity', $sev);
                }
            })
            ->when($filters['year'] ?? null, fn($q, $y) => $q->whereYear('created_at', $y))
            ->when($filters['month'] ?? null, fn($q, $m) => $q->whereMonth('created_at', $m))
            ->when($filters['day'] ?? null, fn($q, $d) => $q->whereDay('created_at', $d))
            ->paginate($perPage)
            ->withQueryString();
    }
}
