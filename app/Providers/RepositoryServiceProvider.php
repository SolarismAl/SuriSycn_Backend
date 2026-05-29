<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\EventRepositoryInterface::class,
            \App\Repositories\Eloquent\EventRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\ReservationRepositoryInterface::class,
            \App\Repositories\Eloquent\ReservationRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\TaskRepositoryInterface::class,
            \App\Repositories\Eloquent\TaskRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\AnnouncementRepositoryInterface::class,
            \App\Repositories\Eloquent\AnnouncementRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
