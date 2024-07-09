<?php

namespace App\Providers;

use App\Models\User;
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
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('admin-access', function (User $user) {
            return $user->hasRole(User::ROLES['admin']);
        });

        Gate::define('customer-access', function (User $user) {
            return $user->hasRole(User::ROLES['customer']);
        });

        Gate::define('shop-access', function (User $user) {
            return $user->hasRole(User::ROLES['shop']);
        });
    }
}
