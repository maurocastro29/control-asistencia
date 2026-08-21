<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, string $ability) {
            $permission = Permission::query()
                ->where('name', $ability)
                ->where('guard_name', 'web')
                ->first();

            if (!$permission) {
                return null;
            }

            if (!$permission->is_active) {
                return false;
            }

            return $user->roles()
                ->where('is_active', true)
                ->exists()
                ? null
                : false;
        });
    }
}
