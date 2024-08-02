<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PostNotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // you can the notifiable instance
        $notifiable = $event->notifiable;
        // you can get the notification instance
        $notification = $event->notification;
        // you can get the message instance. This is the message that was sent to FCM
        $message = $event->message;
        // you can get the response of the notification
        $response = $event->response;
    }
}
