<?php

namespace App\Services\Infrastructure;

use App\Models\Server;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServerService
{
    public function getInventory($perPage = 10)
    {
        return Server::latest()->paginate($perPage);
    }
}
