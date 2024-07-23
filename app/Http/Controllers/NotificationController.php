<?php

namespace App\Http\Controllers;

use App\Models\Peminjam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use App\Notifications\SendNotification;

class NotificationController extends Controller
{
    public function store(Request $request)
    {
        //$comment = new 
    }

    public function getNotifications($UserID)
    {
        $peminjam = Peminjam::where('UserID', $UserID)->first();
        $peminjamid = $peminjam->PeminjamID;
        $notificationunread = $peminjam->notifications();

        $alldata = [];
        foreach ($peminjam->notifications as $notification) {
            $alldata[] = $notification;
        }

        return $alldata;
    }

    public function markAsRead($id, $UserID)
    {
        if ($id) {
            $peminjam = Peminjam::where('UserID', $UserID)->first();
            $peminjam->notifications->where('id', $id)->markAsRead();
        }
        return 'success';
    }
}
