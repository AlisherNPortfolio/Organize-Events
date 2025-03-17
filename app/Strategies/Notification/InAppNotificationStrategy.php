<?php

namespace App\Strategies\Notification;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class InAppNotificationStrategy implements INotificationStrategy
{
    public function send(User $user, string $subject, string $message): bool
    {
        // TODO: notificationlarni databasega yozadigan qilish. Keyinchalik
        Log::info("In-app notification to user {$user->id}: Title: {$subject}, Body: {$message}");

        return true;
    }
}
