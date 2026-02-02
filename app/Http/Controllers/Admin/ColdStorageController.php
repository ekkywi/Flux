<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ColdStorageController extends Controller
{
    public function index(): View
    {
        $files = Storage::disk('local')->files('archives/servers');

        $archives = collect($files)->map(function ($path) {
            $content = json_decode(Storage::disk('local')->get($path), true);

            return (object) [
                'filename'      => basename($path),
                'name'          => $content['identity']['name'] ?? 'Unknown',
                'ip'            => $content['identity']['ip_address'] ?? '0.0.0.0',
                'env'           => $content['identity']['environment'] ?? 'N/A',
                'pruned_at'     => $content['metadata']['prune_at'] ?? 'N/A',
                'logs_count'    => count($content['audit_trail'] ?? []),
                'raw_data'      => $content
            ];
        })->sortByDesc('pruned_at');

        return view('admin.servers.cold_storage', compact('archives'));
    }
}
