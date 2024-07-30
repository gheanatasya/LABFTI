<?php
/* namespace App\Http\Controllers;
use App\Notifications\TestNotification;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class WebNotificationController extends Controller
{
    public function sendWebNotification()
    {
        $user = User::find('UserID', '338ffdaf-f4cc-4304-b463-20735996b588');
        Notification::sendNow($user, new TestNotification());
    }
} */

namespace App\Http\Controllers;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class WebNotificationController extends Controller
{
    public function sendPushNotification()
    {
        $firebase = (new Factory)
            ->withServiceAccount('./config/labfti-15835-12a161b454b0.json');

        $messaging = $firebase->createMessaging();

        $message = CloudMessage::fromArray([
            'notification' => [
                'title' => 'Hello from Firebase!',
                'body' => 'This is a test notification.'
            ],
            'token' => 'disCppwUhhp382mjLbgl8b:APA91bFrgU6559AQx1buuWSS6mYj0LLm3eKjEaL5xGdQEylsxU7Fjd4ShZGDu_KtSzyugOVsn08Ah8s9LCyhdN9x2fKhfWvNLregMkYP5NRZPgITdyiYLMYHA8GKVb6s9KycH55JVmVq'
        ]);

        $messaging->send($message);

        return response()->json(['message' => 'Push notification sent successfully']);
    }
}