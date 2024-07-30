<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        /* $factory = (new Factory)
            ->withServiceAccount('./config/labfti-15835-12a161b454b0.json');

        $firebase = $factory->create();
        $messaging = $firebase->getMessaging();
 */
        
    }
}

