<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class SystemLogController extends Controller
{
    public function index()
    {
        $logs = AuditLog::with('user')->latest()->paginate(15);

        return view('admin.logs.index', compact('logs'));
    }
}
