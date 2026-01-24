<?php

namespace App\Providers;

use App\Services\RegisterUserService;
use App\Services\RoleResolverService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RegisterUserService::class, function ($app) {
            return new RegisterUserService(
                $app->make(RoleResolverService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
