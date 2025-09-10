<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Alle permissies: alleen 'eigenaar'
        Gate::define('manage.company', fn($user) => $user->employee?->position === 'eigenaar');
        Gate::define('manage.employees', fn($user) => $user->employee?->position === 'eigenaar');
        Gate::define('manage.cars', fn($user) => $user->employee?->position === 'eigenaar');
        Gate::define('view.reports', fn($user) => $user->employee?->position === 'eigenaar');
        Gate::define('assign.cars', fn($user) => $user->employee?->position === 'eigenaar');
        Gate::define('manage.customers', fn($user) => $user->employee?->position === 'eigenaar');
    }
}
