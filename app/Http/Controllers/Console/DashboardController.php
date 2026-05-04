<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Environment;
use App\Models\User;
use App\Models\Deployment;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProjects = Project::count();
        $totalNodes = Environment::count();
        $activeNodes = Environment::where('status', 'running')->count();
        $totalPersonnel = User::count();
        $pendingCount = Deployment::whereIn('status', ['queued', 'running'])->count();
        $recentDeployments = Deployment::with(['environment.project'])->latest()->take(5)->get();
        $systemIntegrity = $totalNodes > 0 ? round(($activeNodes / $totalNodes) * 100) : 100;

        $integrityColor = 'text-red-400';
        if ($systemIntegrity >= 90) {
            $integrityColor = 'text-cyan-400';
        } else if ($systemIntegrity >= 50) {
            $integrityColor = 'text-yellow-400';
        }

        if ($pendingCount > 0) {
            $statusText = 'Deploying';
            $statusColor = 'from-yellow-500 to-orange-500 shadow-yellow-500/20';
            $iconAnimation = 'animate-pulse';
        } else if ($systemIntegrity < 100 && $totalNodes > 0) {
            $statusText = 'Degraded';
            $statusColor = 'from-red-500 to-rose-500 shadow-red-500/20';
            $iconAnimation = 'animate-pulse';
        } else {
            $statusText = 'Optimal';
            $statusColor = 'from-blue-500 to-cyan-500 shadow-blue-500/20';
            $iconAnimation = '';
        }

        return view('console.dashboard', compact(
            'totalProjects',
            'totalNodes',
            'activeNodes',
            'totalPersonnel',
            'pendingCount',
            'recentDeployments',
            'systemIntegrity',
            'integrityColor',
            'statusText',
            'statusColor',
            'iconAnimation'
        ));
    }
}
