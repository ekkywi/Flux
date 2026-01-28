<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\Security\KeyGeneratorService;
use Illuminate\Http\Request;

class MasterKeyController extends Controller
{
    public function index()
    {
        $masterKey = SystemSetting::where('key_name', 'master_ssh_key')->first();
        return view('security.master-key.index', compact('masterKey'));
    }

    public function store(Request $request, KeyGeneratorService $service)
    {
        $service->generateMasterKey([
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Security Protocol: Master Key has been successfully rotated.');
    }
}
