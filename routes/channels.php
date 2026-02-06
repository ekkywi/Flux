<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ProjectEnvironment;

// Channel untuk Log Deployment (Sesuai dengan Event Anda)
Broadcast::channel('environment.logs.{envId}', function ($user, $envId) {
    // LOGIKA: Izinkan user login manapun untuk mendengar log ini.
    // Jika user belum login, fungsi ini return false -> Error 403.
    // Jika user login, return true -> Sukses connect.
    return !is_null($user);
});
