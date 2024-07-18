<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use App\Notifications\SendEmailNotification;

class AboutEmailController extends Controller
{
    public function sendnotification(){
        /* $email = $request->input('email');
        $user = User::where('Email', $email)->first(); */
        $user = User::all();

        $details = [
            'greeting' => 'Hello!',
            'body' => 'This is a test email from our Laravel application. This email was sent using the Mailgun mail service.',
            'actionText' => 'View My Profile',
            'actionURL' => '/',
            'lastline' => 'Thank you for using our application!'
        ];

        foreach ($user as $u) {
            $u->notify(new SendEmailNotification($details));
        }

        return response()->json(['message' => 'Email sent successfully.']);
    }
}
