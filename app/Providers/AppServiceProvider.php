<?php

namespace App\Providers;

use App\Models\AccessRequest;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $count = AccessRequest::where('status', 'pending')->count();

                $view->with('pendingCount', $count);
            }
        });
    }
}
