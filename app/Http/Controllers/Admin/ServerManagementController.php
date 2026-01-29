<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\Request;
use App\Services\Infrastructure\ServerService;
use App\Services\Infrastructure\SshConnectivityService;
use App\Services\Infrastructure\SshKeyDistributorService;

class ServerManagementController extends Controller
{
    protected $serverService;

    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

    public function index()
    {
        $servers = $this->serverService->getInventory();
        return view('admin.servers.index', compact('servers'));
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name'          => 'required|string|max:255',
            'ip_address'    => 'required|ip',
            'ssh_port'      => 'required|integer|min:1|max:65535',
            'ssh_user'      => 'required|string|max:50',
            'environment'   => 'required|in:production,staging,development',
            'description'   => 'nullable|string|max:500',
        ]);

        $this->serverService->provisionServer($validate, [
            'ip'            => $request->ip(),
            'user_agent'    => $request->userAgent(),
        ]);

        return back()->with('success', 'Infrastructure Entity successfully provisioned to the core.');
    }

    public function testLink(Server $server, SshConnectivityService $sshService)
    {
        $result = $sshService->verifyNode($server);

        usleep(500000);

        return response()->json($result);
    }

    public function deployKey(Request $request, Server $server, SshKeyDistributorService $distributor)
    {
        $request->validate([
            'ssh_password' => 'required|string'
        ]);

        $result = $distributor->deploy($server, $request->ssh_password);

        if ($result['status'] === 'success') {
            return response()->json($result);
        }

        return response()->json($result, 422);
    }
}
