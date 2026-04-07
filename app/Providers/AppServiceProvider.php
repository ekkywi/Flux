<?php

namespace App\Providers;

use App\Models\AccessRequest;
use App\Models\Project;
use App\Models\Environment;
use App\Models\ProjectMember;
use App\Observers\ProjectObserver;
use App\Observers\EnvironmentObserver;
use App\Observers\ProjectMemberObserver;
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

        Project::observe(ProjectObserver::class);
        Environment::observe(EnvironmentObserver::class);
        ProjectMember::observe(ProjectMemberObserver::class);
    }
}
