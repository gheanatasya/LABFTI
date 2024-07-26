<?php

namespace App\Http\Controllers;

use App\Models\Peminjam;
use App\Models\Peminjaman;
use App\Models\Peminjaman_Ruangan_Bridge;
use App\Models\Peminjaman_Alat_Bridge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use App\Notifications\NewBookingNotification;

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
        $countUnread = 0;
        foreach ($peminjam->notifications as $notification) {
            $alldata[] = $notification;
            if ($notification->read_at === null) {
                $countUnread++;
            }
        }

        return response([
            'data' => $alldata,
            'unread' => $countUnread
        ]);
    }

    public function markAsRead($id, $UserID)
    {
        if ($id) {
            $peminjam = Peminjam::where('UserID', $UserID)->first();
            $peminjam->notifications->where('id', $id)->markAsRead();
        }
        return 'success';
    }

    public function testRuangan($Peminjaman_Ruangan_ID)
    {
        $user = User::whereIn('User_role', ['Petugas', 'Kepala Lab', 'Koordinator Lab'])->get();
        $peminjamanruanganid = Peminjaman_Ruangan_Bridge::where('Peminjaman_Ruangan_ID', $Peminjaman_Ruangan_ID)->first();
        $isOrganisasi = $peminjamanruanganid->Is_Organisation;
        $isPersonal = $peminjamanruanganid->Is_Personal;
        $isEksternal = $peminjamanruanganid->Is_Eksternal;
        $dataNotifikasi = [
            'subject' => 'Peminjaman Baru'
        ];

        foreach ($user as $u) {
            $userid = $u->UserID;
            $peminjam = Peminjam::where('UserID', $userid)->first();
            if ($peminjam) {
                $peminjam->notify(new NewBookingNotification($dataNotifikasi));
            }
        }

        return response()->json([
            'message' => 'Persetujuan ruangan berhasil diperbarui', 'data' => $Peminjaman_Ruangan_ID,
        ]);
    }
}
