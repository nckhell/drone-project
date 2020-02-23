<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes(function ($router) {
            $router->forAccessTokens();
            $router->forPersonalAccessTokens();
            $router->forTransientTokens();
        });
        Passport::tokensExpireIn(now()->addMinutes(5));
        Passport::refreshTokensExpireIn(now()->addDays(10));
    }
}
