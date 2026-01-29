<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'action',
        'category',
        'severity',
        'target_type',
        'target_id',
        'metadata',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function getTargetLabelAttribute()
    {
        if (!$this->metadata) return 'N/A';

        return $this->metadata['key_name']
            ?? $this->metadata['target_user']
            ?? $this->metadata['username']
            ?? $this->metadata['provisioned_email']
            ?? $this->metadata['server_name']
            ?? 'N/A';
    }

    public function getModifiedFieldsAttribute()
    {
        if (!isset($this->metadata['before']) || !isset($this->metadata['after'])) {
            return [];
        }

        $before = collect($this->metadata['before']);
        $after = collect($this->metadata['after']);

        $ignoredFields = ['updated_at', 'created_at', 'password', 'remember_token'];

        $changes = [];

        foreach ($after as $key => $newValue) {
            if (in_array($key, $ignoredFields)) continue;

            $oldValue = $before->get($key);

            $normalizedOld = ($oldValue === "" || $oldValue === null) ? null : $oldValue;
            $normalizedNew = ($newValue === "" || $newValue === null) ? null : $newValue;

            if ($normalizedOld != $normalizedNew) {
                $changes[$key] = [
                    'from' => $oldValue ?? 'NULL',
                    'to'   => $newValue ?? 'NULL'
                ];
            }
        }

        return $changes;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
