<?php

namespace App\Strategies\Notification;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class PushNotificationStrategy implements INotificationStrategy
{
    public function send(User $user, string $subject, string $message): bool
    {
        // TODO: notification service bilan ishlatish
        Log::info("Push notification to user {$user->id}: Title: {$subject}, Body: {$message}");

        return true;
    }
}
