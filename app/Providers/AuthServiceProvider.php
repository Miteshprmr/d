<?php

namespace App\Providers;

use App\Passport\Client;
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

        $accessTokenExpiration = config('auth.passport.access_token_expiration');

        $refreshTokenExpiration = config('auth.passport.refresh_token_expiration');

        Passport::useClientModel(Client::class);

        Passport::ignoreCsrfToken();

        Passport::tokensExpireIn(now()->addMinutes($accessTokenExpiration));

        Passport::refreshTokensExpireIn(now()->addMinutes($refreshTokenExpiration));
    }
}
