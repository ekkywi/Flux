<?php

namespace App\Models;

use App\Enums\ApprovalType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessRequest extends Model
{
    use HasUuids;

    protected $table = 'access_requests';

    protected $fillable = [
        'user_id',
        'request_type',
        'requested_role',
        'justification',
        'metadata',
        'status',
        'rejection_reason',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'request_type' => ApprovalType::class,
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
