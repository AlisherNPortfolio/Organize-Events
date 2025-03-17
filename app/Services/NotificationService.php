<?php

namespace App\Services;

use App\Strategies\Notification\EmailNotificationStrategy;

class NotificationService
{
    public function __construct(protected NotificationStrategy $notificationStrategy)
    {
        $this->notificationStrategy = $notificationStrategy ?? new EmailNotificationStrategy();
    }

    public function setStrategy(INotificationStrategy $strategy)
    {
        $this->notificationStrategy = $strategy;
        return $this;
    }

    public function sendEventCreationNotification($event)
    {
        return $this->notificationStrategy->send(
            $event->creator,
            'Event Created',
            "Your event '{$event->name}' has been created successfully!"
        );
    }

    public function sendEventParticipationNotification($event, $user)
    {
        return $this->notificationStrategy->send(
            $user,
            'Event Participation',
            "You have successfully signed up for the event '{$event->name}'!"
        );
    }

    public function sendEventReminderNotification($event, $user)
    {
        return $this->notificationStrategy->send(
            $user,
            'Event Reminder',
            "Reminder: The event '{$event->name}' is tomorrow at {$event->start_time}!"
        );
    }

    public function sendFineNotification($user, $event)
    {
        return $this->notificationStrategy->send(
            $user,
            'Fine Applied',
            "You have been fined for not attending the event '{$event->name}'. You cannot participate in events until the fine expires."
        );
    }
}
