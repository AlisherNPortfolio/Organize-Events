<?php

namespace App\Factories;

use App\Strategies\Notification\EmailNotificationStrategy;
use App\Strategies\Notification\InAppNotificationStrategy;
use App\Strategies\Notification\INotificationStrategy;
use App\Strategies\Notification\PushNotificationStrategy;

class NotificationStrategyFactory
{
    public function createStrategy(string $type): INotificationStrategy
    {
        switch ($type) {
            case 'email':
                return new EmailNotificationStrategy();
            case 'push':
                return new PushNotificationStrategy();
            case 'in-app':
                return new InAppNotificationStrategy();
            default:
                throw new \InvalidArgumentException("Noto\'g\'ri notification strategy turi: {$type}");
        }
    }
}
