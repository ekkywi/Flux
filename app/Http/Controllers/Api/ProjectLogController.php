<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProjectEnvironment;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\JsonResponse;

class ProjectLogController extends Controller
{
    public function fetch(ProjectEnvironment $environment): JsonResponse
    {
        $logKey = "deployment:logs:{$environment->id}";

        $rawLogs = Redis::lrange($logKey, 0, -1);

        $logs = array_map(function ($log) {
            return json_decode($log, true);
        }, $rawLogs);

        return response()->json([
            'status'    => 'success',
            'logs'      => $logs
        ]);
    }
}
