<?php

namespace Cruftman\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
//use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Cruftman\Auth\UserProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'Cruftman\Model' => 'Cruftman\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        Auth::provider('cruft', function ($app, array $config) {
            return new UserProvider($app['hash'], $config['model']);
        });
    }
}
