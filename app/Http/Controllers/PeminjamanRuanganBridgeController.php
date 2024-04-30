<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman_Ruangan_Bridge;
use App\Http\Requests\StorePeminjaman_Ruangan_BridgeRequest;
use App\Http\Requests\UpdatePeminjaman_Ruangan_BridgeRequest;
use App\Models\Alat;
use App\Models\Detail_Alat;
use App\Models\Peminjam;
use App\Models\Ruangan;
use App\Models\Peminjaman;
use App\Models\Peminjaman_Alat_Bridge;
use App\Models\User;

class PeminjamanRuanganBridgeController extends Controller
{
    // mengambil semua data pada db
    public function getAllPeminjamanRuangan()
    {
        return Peminjaman_Ruangan_Bridge::all();
    }

    //ambil data sesuai id
    public function show($Peminjaman_Ruangan_ID)
    {
        return Peminjaman_Ruangan_Bridge::find($Peminjaman_Ruangan_ID);
    }

    //tambah data
    public function store(StorePeminjaman_Ruangan_BridgeRequest $request)
    {
        dd($request);
        setlocale(LC_ALL, 'id_ID');
        $input = $request->all();

        $ruanganid = Ruangan::where('Nama_ruangan', $input['selectedRuangan'])->first();
        $idroom = $ruanganid->RuanganID;

        $peminjaman = Peminjaman::create([
            'DokumenID' => $input['dokumen'],
            'Tanggal_pinjam' => date('l, d-m-Y') ,
            'Keterangan' => $input['keterangan'],
            'Is_Personal' => $input['selectedOptionPersonal'],
            'Is_Organisation' => $input['selectedOptionOrganisation'],
            'Is_Eksternal' => $input['selectedOptionEksternal'],
        ]);
        $peminjamanid = $peminjaman->PeminjamanID;

        $peminjaman_ruangan = Peminjaman_Ruangan_Bridge::create([
            'PeminjamanID' => $peminjamanid,
            'RuanganID' => $idroom,
            'Tanggal_pakai_awal' => $input['tanggalAwal'],
            'Tanggal_pakai_akhir' => $input['tanggalSelesai'],
            'Waktu_pakai' => $input['waktuPakai'],
            'Waktu_selesai' => $input['waktuSelesai']
        ]);

        $peminjaman_ruangan_ID = $peminjaman_ruangan->Peminjaman_Ruangan_ID;

        
        return response()->json(['status' => true, 'message' => "Registration Success", 'Peminjaman_Ruangan_ID' => $peminjaman_ruangan_ID, 'peminjaman_ruangan_bridge' => $peminjaman_ruangan]);
    }

    //mengubah data semua row
    public function updateSemuaRow(UpdatePeminjaman_Ruangan_BridgeRequest $request, Peminjaman_Ruangan_Bridge $Peminjaman_Ruangan_ID)
    {
        $Peminjaman_Ruangan_ID->update($request->all());
        return response()->json(['message' => 'Peminjaman Ruangan berhasil diperbarui', 'data' => $Peminjaman_Ruangan_ID]);
    }

    //hapus data
    public function delete($Peminjaman_Ruangan_ID)
    {
        $peminjamanruangan = Peminjaman_Ruangan_Bridge::find($Peminjaman_Ruangan_ID);
        $peminjamanruangan->delete();
        return response()->json(['message' => 'peminjamanruangan berhasil dihapus'], 204);
    }

    //mengubah data user yang login
    public function update(UpdatePeminjaman_Ruangan_BridgeRequest $request, $Peminjaman_Ruangan_ID)
    {
        $peminjamanruangan = Peminjaman_Ruangan_Bridge::find($Peminjaman_Ruangan_ID);

        // Validasi data masukan
        /*  $request->validate([ */
        /*      'email' => 'required|email', */
        /*      'password' => 'required', */
        /*  ]); */
        // Update email dan password
        /* $user->Email = $request->email; */
        /* $user->Password = bcrypt($request->password); */
        /* $user->save(); */
        /* return response()->json(['message' => 'Email dan password berhasil diperbarui', 'data' => $user]); */
    }

    //mengambil data berdasarkan peminjamanID
    public function getAllPeminjamanRuanganByPeminjamanID($PeminjamanID)
    {
        $peminjaman = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $PeminjamanID)->get();
        if ($peminjaman->isEmpty()) {
            return null;
        } else {
            return $peminjaman;
        }
    }

    // mengambil nama ruangan berdasarkan ID 
    public function getNameByID($RuanganID)
    {
        $ruangan = Ruangan::where('RuanganID', $RuanganID)->first();
        if ($ruangan) {
            return response()->json(['Nama_ruangan' => $ruangan->Nama_ruangan]);
        } else {
            return response()->json(['Error' => 'Ruangan not found'], 404);
        }
    }

    //mengambil data-data peminjaman ruangan
    public function getPeminjamanRuangan($UserID){
        $peminjam = Peminjam::where('UserID', $UserID)->first();
        $peminjamID = $peminjam->PeminjamID;
        $peminjaman = Peminjaman::where('PeminjamID', $peminjamID)->get();
        $allroombooking = [];

        foreach ($peminjaman as $booking){
            $keterangan = $booking->Keterangan;
            $peminjamanID = $booking->PeminjamanID;
            $datapinjamruangan = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $peminjamanID)->get();

            foreach ($datapinjamruangan as $data){
                $peminjamanruanganid = $data->Peminjaman_Ruangan_ID;
                $ruanganid = $data->RuanganID;
                $tanggalawal = $data->Tanggal_pakai_awal;
                $tanggalakhir = $data->Tanggal_pakai_akhir;
                $waktupakai = $data->Waktu_pakai;
                $waktuselesai = $data->Waktu_selesai;
                $cariroom = Ruangan::where('RuanganID', $ruanganid)->first();
                $namaruangan = $cariroom->Nama_ruangan;
                $peminjamanid = $data->PeminjamanID;

                $recordData = [
                    'peminjamanruanganid' => $peminjamanruanganid,
                    'peminjamanid' => $peminjamanid,
                    'tanggalawal' => $tanggalawal,
                    'tanggalakhir' => $tanggalakhir,
                    'waktupakai' => $waktupakai,
                    'waktuselesai' => $waktuselesai,
                    'keterangan' => $keterangan,
                    'namaruangan' => $namaruangan
                ];

                $allroombooking[] = $recordData;
            }
        }

        return $allroombooking;
    }

    //mengambil keterangan peminjaman ruangan 
    public function getKeterangan($PeminjamanID)
    {
        $peminjaman = Peminjaman::where('PeminjamanID', $PeminjamanID)->first();
        if ($peminjaman) {
            return response()->json(['Keterangan' => $peminjaman->Keterangan]);
        } else {
            return response()->json(['Error' => 'Keterangan not found'], 404);
        }
    }

    //menghapus peminjaman berdasarkan peminjamanID
    public function deletePeminjamanRuangan($PeminjamanID, $RuanganID)
    {
        $peminjaman = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $PeminjamanID)
            ->where('RuanganID', $RuanganID)
            ->first();

        if ($peminjaman) {
            $peminjaman->delete();
            return response()->json(['message' => 'Peminjaman Ruangan berhasil dihapus'], 204);
        } else {
            return response()->json(['Error' => 'Peminjaman Ruangan tidak ditemukan']);
        }
    }

    //mengecek apabila terdapat relasi dengan peminjaman alat
    public function checkRelation($PeminjamanID)
    {
        $peminjaman = Peminjaman_Alat_Bridge::where('PeminjamanID', $PeminjamanID)->first();

        if ($peminjaman) {
            return response()->json(['relasi' => true]);
        } else {
            return response()->json(['relasi' => false]);
        }
    }

    //melihat ruangan dan alat yang kosong (menghindari tabrakan jadwal)
    public function jadwalPeminjaman($Tanggal_pakai_awal, $Tanggal_pakai_akhir, $Waktu_pakai, $Waktu_selesai)
    {
        /* $ruangan = Peminjaman_Ruangan_Bridge::whereNot('Tanggal_pakai_awal', $Tanggal_pakai_awal) */
        /*     ->whereNot('Waktu_pakai', $Waktu_pakai) */
        /*     ->whereNot('Tanggal_pakai_akhir', $Tanggal_pakai_akhir) */
        /*     ->whereNot('Waktu_selesai', $Waktu_selesai) */
        /*     ->pluck('RuanganID'); */

        /* $alat = Peminjaman_Alat_Bridge::whereNot('Tanggal_pakai_awal', $Tanggal_pakai_awal) */
        /*     ->whereNot('Waktu_pakai', $Waktu_pakai) */
        /*     ->whereNot('Tanggal_pakai_akhir', $Tanggal_pakai_akhir) */
        /*     ->whereNot('Waktu_selesai', $Waktu_selesai) */
        /*     ->pluck('AlatID'); */

        $ruangan = Peminjaman_Ruangan_Bridge::where('Tanggal_pakai_awal', $Tanggal_pakai_awal)
            ->where('Waktu_pakai', $Waktu_pakai)
            ->where('Tanggal_pakai_akhir', $Tanggal_pakai_akhir)
            ->where('Waktu_selesai', $Waktu_selesai)
            ->pluck('RuanganID');

       /*  $alat = Peminjaman_Alat_Bridge::where('Tanggal_pakai_awal', $Tanggal_pakai_awal) */
       /*      ->where('Waktu_pakai', $Waktu_pakai) */
       /*      ->where('Tanggal_pakai_akhir', $Tanggal_pakai_akhir) */
       /*      ->where('Waktu_selesai', $Waktu_selesai) */
       /*      ->pluck('AlatID'); */

        $RUANGAN = [];

        if ($ruangan) {
            foreach ($ruangan as $room) {
                $ruang = Ruangan::where('RuanganID', $room)->first();
                $namaRuangan = $ruang->Nama_ruangan;
                $RUANGAN[] = $namaRuangan;

                return ['RUANGAN' => $RUANGAN];
            }

        }

        /* $RUANGAN = []; */
        /* $ALAT = []; */

        /* if ($ruangan || $alat) { */

        /*     foreach ($ruangan as $roomname) { */
        /*         $room = Ruangan::where('RuanganID', $roomname)->first(); */
        /*         $namaRuangan = $room->Nama_ruangan; */
        /*         $RUANGAN[] = $namaRuangan; */
        /*     } */

        /*     foreach ($alat as $toolname) { */
        /*         $tools = Detail_Alat::where('AlatID', $toolname)->get(); */
        /*         foreach ($tools as $tool) { */
        /*             $namaAlat = $tool->Nama_alat; */
        /*             $ALAT[] = $namaAlat; */
        /*         } */
        /*     } */

        /*     $RUANGAN = array_unique($RUANGAN); */
        /*     $ALAT = array_unique($ALAT); */

        /*     return [ */
        /*         'RUANGAN' => $RUANGAN, */
        /*         'ALAT' => $ALAT */
        /*     ]; */
        /* } */
    }
}
