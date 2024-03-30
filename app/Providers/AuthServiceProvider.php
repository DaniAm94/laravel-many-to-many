<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Project::class => ProjectPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('update-project', [ProjectPolicy::class, 'update']);
        Gate::define('destroy-project', [ProjectPolicy::class, 'delete']);
        Gate::define('restore-project', [ProjectPolicy::class, 'restore']);
        Gate::define('drop-project', [ProjectPolicy::class, 'forceDelete']);
    }
}
