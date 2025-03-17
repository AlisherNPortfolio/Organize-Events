<?php

namespace App\Strategies\Notification;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailNotificationStrategy implements INotificationStrategy
{
    public function send(User $user, string $subject, string $message): bool
    {
        // TODO: pochtaga xat yuboradigan qilish
        Log::info("Email to {$user->email}: Subject: {$subject}, Message: {$message}");

        /*
        Mail::raw($message, function ($mail) use ($user, $subject) {
            $mail->to($user->email)
                ->subject($subject);
        });
        */

        return true;
    }
}
