<?php

namespace App\Strategies\Notification;

use App\Models\User;

interface INotificationStrategy
{
    public function send(User $user, string $subject, string $message): bool;
}
