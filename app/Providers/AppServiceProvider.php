<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use App\Mail\Transport\GmailApiTransport;

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
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        Mail::extend('gmail_api', function (array $config = []) {
            return new GmailApiTransport($config);
        });

        Gate::define('is-admin', function ($user) {
            return $user->role === \App\Enums\Role::ADMIN->value;
        });

        Gate::define('is-staff', function ($user) {
            return $user->role === \App\Enums\Role::STAFF->value;
        });
    }
}
