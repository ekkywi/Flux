<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Admin\FetchAuditLogsAction;

class SystemLogController extends Controller
{
    public function index(Request $request, FetchAuditLogsAction $fetchLogs)
    {
        $logs = $fetchLogs->execute($request->only([
            'search',
            'severity',
            'year',
            'month',
            'day'
        ]));

        return view('admin.logs.index', compact('logs'));
    }
}
