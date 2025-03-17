<?php

namespace App\Providers;

use App\Repositories\Contracts\IUserFineRepository;
use App\Strategies\FineCalculation\DefaultFineStrategy;
use App\Strategies\FineCalculation\IFineCalculationStrategy;
use App\Strategies\FineCalculation\ProgressiveFineStrategy;
use App\Strategies\Notification\EmailNotificationStrategy;
use App\Strategies\Notification\InAppNotificationStrategy;
use App\Strategies\Notification\INotificationStrategy;
use App\Strategies\Notification\PushNotificationStrategy;
use Illuminate\Support\ServiceProvider;

class StrategyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // FineCalculationStrategy
        $this->app->bind(IFineCalculationStrategy::class, function ($app) {
            return new DefaultFineStrategy($app->make(IUserFineRepository::class));
        });

        $this->app->bind('fine.strategy.default', function ($app) {
            return new DefaultFineStrategy($app->make(IUserFineRepository::class));
        });

        $this->app->bind('fine.strategy.progressive', function ($app) {
            return new ProgressiveFineStrategy($app->make(IUserFineRepository::class));
        });

        // NotificationStrategy
        $this->app->bind(INotificationStrategy::class, function ($app) {
            return new InAppNotificationStrategy();
        });

        $this->app->bind('notification.strategy.email', function ($app) {
            return new EmailNotificationStrategy();
        });
        $this->app->bind('notification.strategy.in_app', concrete: function ($app) {
            return new InAppNotificationStrategy();
        });
        $this->app->bind('notification.strategy.push', concrete: function ($app) {
            return new PushNotificationStrategy();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
