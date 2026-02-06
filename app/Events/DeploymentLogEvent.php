<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeploymentLogEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public $envId,
        public $logData // Array: ['line' => ..., 'type' => ..., 'time' => ...]
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("environment.logs.{$this->envId}")];
    }

    public function broadcastAs(): string
    {
        return 'log.received';
    }
}
