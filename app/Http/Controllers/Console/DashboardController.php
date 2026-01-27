<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'nodes' => 12,
            'uptime' => '99.9%',
            'deployments' => 148,
            'errors' => 0
        ];

        return view('console.dashboard', compact('user', 'stats'));
    }
}
