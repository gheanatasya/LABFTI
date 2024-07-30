<?php

namespace App\Broadcasting;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class FcmChannel
{
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toFcm($notifiable);
        $token = $notifiable->web_token;
        $cloudMessage = CloudMessage::fromArray([
            'token' => $token,
            'notification' => $message,
        ]);
    
        $messaging->send($cloudMessage);
    
    }
}
