<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\Request;
use App\Actions\Admin\ProvisionServerAction;
use App\Actions\Admin\UpdateServerAction;
use App\Actions\Admin\DecommissionServerAction;
use App\Actions\Admin\RestoreServerAction;
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

    public function store(Request $request, ProvisionServerAction $provisionServer)
    {
        $validate = $request->validate([
            'name'          => 'required|string|max:255',
            'ip_address'    => 'required|ip',
            'ssh_port'      => 'required|integer|min:1|max:65535',
            'ssh_user'      => 'required|string|max:50',
            'environment'   => 'required|in:production,staging,development',
            'description'   => 'nullable|string|max:500',
        ]);

        $provisionServer->execute($validate, auth()->id());

        return back()->with('success', 'Server Entity successfully provisioned to the core.');
    }

    public function update(Request $request, Server $server, UpdateServerAction $updateServer)
    {
        $validate = $request->validate([
            'name'          => 'required|string|max:255',
            'ip_address'    => 'required|ip',
            'ssh_port'      => 'required|integer|min:1|max:65535',
            'ssh_user'      => 'required|string|max:50',
            'environment'   => 'required|in:production,staging,development',
            'description'   => 'nullable|string|max:500',
        ]);

        $updateServer->execute($server, $validate, auth()->id());

        return back()->with('success', 'Infrastructure Entity configuration has been recalibrated.');
    }

    public function destroy(Server $server, DecommissionServerAction $decommissionServer)
    {
        $decommissionServer->execute($server, auth()->id());

        return back()->with('success', 'Server Entity has been decommissioned from the active grid.');
    }

    public function archived()
    {
        $servers = Server::onlyTrashed()->latest()->paginate(10);
        return view('admin.servers.archived', compact('servers'));
    }

    public function restore($id, RestoreServerAction $restoreServer)
    {
        $restoreServer->execute($id, auth()->id());

        return back()->with('success', 'Server Entity has been reintegrated into the active grid.');
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
