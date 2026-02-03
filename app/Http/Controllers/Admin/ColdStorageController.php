<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Services\Core\ColdStorageService;

class ColdStorageController extends Controller
{
    protected $service;

    public function __construct(ColdStorageService $service)
    {
        $this->service = $service;
    }

    public function index($type)
    {
        abort_unless(in_array($type, ['infrastructure', 'identity', 'projects']), 404);

        $archives = $this->service->getArchives($type);
        return view('admin.cold_storage.index', compact('archives', 'type'));
    }

    public function download($type, $filename)
    {
        return $this->service->generateCsv($type, $filename);
    }
}
