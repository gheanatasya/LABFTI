<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman_Alat_Bridge;
use App\Http\Requests\StorePeminjaman_Alat_BridgeRequest;
use App\Http\Requests\UpdatePeminjaman_Alat_BridgeRequest;
use App\Models\Alat;
use App\Models\Detail_Alat;
use App\Models\Peminjam;
use App\Models\Peminjaman;
use App\Models\Peminjaman_Ruangan_Bridge;
use Illuminate\Support\Facades\DB;


class PeminjamanAlatBridgeController extends Controller
{
    // mengambil semua data pada db
    public function getAllPeminjamanAlat()
    {
        return Peminjaman_Alat_Bridge::all();
    }
    //ambil data sesuai id
    public function show($Peminjaman_Alat_ID)
    {
        return Peminjaman_Alat_Bridge::find($Peminjaman_Alat_ID);
    }
    //tambah data
    public function store(StorePeminjaman_Alat_BridgeRequest $request)
    {
        $input = $request->all();
        $peminjaman_alat = Peminjaman_Alat_Bridge::create([
            'PeminjamanID' => $input['PeminjamanID'],
            'AlatID' => $input['AlatID'],
            'Tanggal_pakai_awal' => $input['Tanggal_pakai_awal'],
            'Tanggal_pakai_akhir' => $input['Tanggal_pakai_akhir'],
            'Waktu_pakai' => $input['Waktu_pakai'],
            'Waktu_selesai' => $input['Waktu_selesai'],
            'Waktu_pengambilan' => $input['Waktu_pengambilan'],
            'Tanggal_pengembalian' => $input['Tanggal_pengembalian'],
            'Waktu_pengembalian' => $input['Waktu_pengembalian'],
            'Jumlah_pinjam' => $input['Jumlah_pinjam']
        ]);
        $peminjaman_alat_ID = $peminjaman_alat->Peminjaman_Alat_ID;
        return response()->json(['status' => true, 'message' => "Registration Success", 'Peminjaman_Alat_ID' => $peminjaman_alat_ID, 'peminjaman_alat_bridge' => $peminjaman_alat]);
    }
    //mengubah data semua row
    public function updateSemuaRow(UpdatePeminjaman_Alat_BridgeRequest $request, Peminjaman_Alat_Bridge $Peminjaman_Alat_ID)
    {
        $Peminjaman_Alat_ID->update($request->all());
        return response()->json(['message' => 'Peminjaman Alat berhasil diperbarui', 'data' => $Peminjaman_Alat_ID]);
    }
    //hapus data
    public function delete($Peminjaman_Alat_ID)
    {
        $peminjamanalat = Peminjaman_Alat_Bridge::find($Peminjaman_Alat_ID);
        $peminjamanalat->delete();
        return response()->json(['message' => 'peminjamanalat berhasil dihapus'], 204);
    }

    //menghapus peminjaman berdasarkan peminjamanID
    public function deletePeminjamanAlat($PeminjamanID, $AlatID)
    {
        $peminjaman = Peminjaman_Alat_Bridge::where('PeminjamanID', $PeminjamanID)
            ->where('AlatID', $AlatID)
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
        $peminjaman = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $PeminjamanID)->first();
        if ($peminjaman) {
            return response()->json(['relasi' => true]);
        } else {
            return response()->json(['relasi' => false]);
        }
    }

    public function getNameByID($AlatID)
    {
        $alat = Alat::where('AlatID', $AlatID)->first();
        if ($alat) {
            return response()->json(['Nama' => $alat->Nama]);
        } else {
            return response()->json(['Error' => 'Alat not found'], 404);
        }
    }

    //mengambil data berdasarkan peminjamanID
    public function getAllPeminjamanAlatByPeminjamanID($PeminjamanID)
    {
        $peminjaman = Peminjaman_Alat_Bridge::where('PeminjamanID', $PeminjamanID)->get();
        if ($peminjaman->isEmpty()) {
            return null;
        } else {
            return $peminjaman;
        }
    }

    //mengambil data-data peminjaman alat
    public function getPeminjamanAlat($UserID)
    {
        $peminjam = Peminjam::where('UserID', $UserID)->first();
        $peminjamID = $peminjam->PeminjamID;
        $peminjaman = Peminjaman::where('PeminjamID', $peminjamID)->get();
        $alltoolsbooking = [];

        foreach ($peminjaman as $booking) {
            $keterangan = $booking->Keterangan;
            $peminjamanID = $booking->PeminjamanID;
            $datapinjamalat = Peminjaman_Alat_Bridge::where('PeminjamanID', $peminjamanID)->get();

            foreach ($datapinjamalat as $data) {
                $peminjamanalatid = $data->Peminjaman_Alat_ID;
                $alatid = $data->AlatID;
                $tanggalawal = $data->Tanggal_pakai_awal;
                $tanggalakhir = $data->Tanggal_pakai_akhir;
                $waktupakai = $data->Waktu_pakai;
                $waktuselesai = $data->Waktu_selesai;
                $peminjamanid = $data->PeminjamanID;
                $alat = Alat::where('AlatID', $alatid)->first();
                $namaalat = $alat->Nama;

                $recordData = [
                    'peminjamanalatid' => $peminjamanalatid,
                    'peminjamanid' => $peminjamanid,
                    'tanggalawal' => $tanggalawal,
                    'tanggalakhir' => $tanggalakhir,
                    'waktupakai' => $waktupakai,
                    'waktuselesai' => $waktuselesai,
                    'keterangan' => $keterangan,
                    'namaalat' => $namaalat
                ];

                $alltoolsbooking[] = $recordData;
            }
        }

        return $alltoolsbooking;
    }

    //cek jadwal tabrakan
    public function jadwalAlat($Tanggal_pakai_awal, $Tanggal_pakai_akhir)
    {
        $peminjamanalat = Peminjaman_Alat_Bridge::where('Tanggal_pakai_awal', "<=", $Tanggal_pakai_awal)
            ->where('Tanggal_pakai_akhir', ">=", $Tanggal_pakai_akhir)
            ->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '>', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '<=', $Tanggal_pakai_akhir);
            })->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '>', $Tanggal_pakai_akhir);
            })->pluck('AlatID')->unique();
        $dataalat = Alat::pluck('AlatID', 'Nama', 'Jumlah_ketersediaan');
        $alat = $dataalat->diff($peminjamanalat);
        $tool = $alat->toArray();
        $array = array_keys($tool);
        $detailTool = [];

        foreach ($array as $availableTool) {
            $ambildata = Alat::where('Nama', $availableTool)->first();
            $detailTool[] = $ambildata;
        }
        return response()->json(['availableTool' => $array, 'detailAlat' => $detailTool]);
    }
}
