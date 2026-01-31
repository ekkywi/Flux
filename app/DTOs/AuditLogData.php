<?php

namespace app\DTOs;

use App\Enums\AuditSeverity;

class AuditLogData
{
    public function __construct(
        public string $action,
        public string $category,
        public AuditSeverity $severity = AuditSeverity::INFO,
        public ?string $target_type = null,
        public ?string $target_id = null,
        public array $metadata = [],
        public ?string $correlation_id = null,
        public ?string $user_id = null,
    ) {}
}
