<?php

namespace App\Actions\Admin;

use App\Models\AuditLog;
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
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('first_name', 'ILIKE', "%{$search}%")
                                ->orWhere('username', 'ILIKE', "%{$search}%");
                        })
                        ->orWhere('metadata->target_user', 'ILIKE', "%{$search}%");
                });
            })
            ->when($filters['severity'] ?? null, function ($query, $severity) {
                $query->where('severity', $severity);
            })
            ->when($filters['year'] ?? null, function ($query, $year) {
                $query->whereYear('created_at', $year);
            })
            ->when($filters['month'] ?? null, function ($query, $month) {
                $query->whereMonth('created_at', $month);
            })
            ->when($filters['day'] ?? null, function ($query, $day) {
                $query->whereDay('created_at', $day);
            })
            ->paginate($perPage)
            ->withQueryString();
    }
}
