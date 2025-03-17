<?php

namespace App\Providers;

use App\Repositories\BaseRepository;
use App\Repositories\Contracts\IEventImageRepository;
use App\Repositories\Contracts\IEventParticipantRepository;
use App\Repositories\Contracts\IEventRepository;
use App\Repositories\Contracts\IRepository;
use App\Repositories\Contracts\IUserFineRepository;
use App\Repositories\Contracts\IUserRepository;
use App\Repositories\EventImageRepository;
use App\Repositories\EventParticipantRepository;
use App\Repositories\EventRepository;
use App\Repositories\UserFineRepository;
use App\Repositories\UserRepository;
use App\Strategies\FineCalculation\IFineCalculationStrategy;
use App\Strategies\Notification\INotificationStrategy;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(IRepository::class, BaseRepository::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IEventRepository::class, EventRepository::class);
        $this->app->bind(IEventParticipantRepository::class, EventParticipantRepository::class);
        $this->app->bind(IEventImageRepository::class, EventImageRepository::class);
        $this->app->bind(IUserFineRepository::class, UserFineRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
