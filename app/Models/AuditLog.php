<?php

namespace App\Models;

use App\Enums\AuditSeverity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasUuids, Prunable;

    protected $fillable = [
        'user_id',
        'action',
        'category',
        'severity',
        'target_type',
        'target_id',
        'metadata',
        'correlation_id',
        'ip_address',
        'user_agent'
    ];

    protected function casts(): array
    {
        return [
            'severity' => AuditSeverity::class,
            'metadata' => AsArrayObject::class,
        ];
    }

    protected function changes(): Attribute
    {
        return Attribute::make(
            get: function () {
                $before = $this->metadata['before'] ?? [];
                $after = $this->metadata['after'] ?? [];
                return array_diff_assoc($after, $before);
            }
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    public function prunable()
    {
        return static::where('created_at', '<=', now()->subMonths(6));
    }
}
