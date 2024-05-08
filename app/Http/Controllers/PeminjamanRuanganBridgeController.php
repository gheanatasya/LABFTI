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
        setlocale(LC_ALL, 'id_ID');
        $input = $request->all();
        //dd($input);

        $user = Peminjam::where('UserID', $request[0]['UserID'])->first();
        $peminjamID = $user->PeminjamID;

        $peminjaman = Peminjaman::create([
            'PeminjamID' => $peminjamID,
            'Tanggal_pinjam' => date('d-m-Y')
        ]);
        //dd($peminjaman);
        $peminjamanid = $peminjaman->PeminjamanID;
        $semuapeminjaman = [];

        for ($i = 0; $i < count($input); $i++) {
            $ruanganid = Ruangan::where('Nama_ruangan', $input[$i]['selectedRuangan'])->first();
            $idroom = $ruanganid->RuanganID;

            $peminjaman_ruangan = Peminjaman_Ruangan_Bridge::create([
                'PeminjamanID' => $peminjamanid,
                'RuanganID' => $idroom,
                'Tanggal_pakai_awal' => $input[$i]['tanggalAwal'],
                'Tanggal_pakai_akhir' => $input[$i]['tanggalSelesai'],
                'Keterangan' => $input[$i]['keterangan'],
                'Is_Personal' => $input[$i]['selectedOptionPersonal'],
                'Is_Organisation' => $input[$i]['selectedOptionOrganisation'],
                'Is_Eksternal' => $input[$i]['selectedOptionEksternal'],
                'DokumenID' => $input[$i]['dokumen']
            ]);

            $peminjaman_ruangan_ID = $peminjaman_ruangan->Peminjaman_Ruangan_ID;
            $semuapeminjaman[] = $peminjaman_ruangan;

            $jumlahpinjam = 1;
            $totalpinjamalat = [];

            foreach ($input[$i]['alat'] as $tool) {
                $detail = Detail_Alat::where('Nama_alat', $tool)->first();
                $alatID = $detail->DetailAlatID;

                $peminjaman_alat = Peminjaman_Alat_Bridge::create([
                    'PeminjamanID' => $peminjamanid,
                    'DetailAlatID' => $alatID,
                    'Tanggal_pakai_awal' => $input[$i]['tanggalAwal'],
                    'Tanggal_pakai_akhir' => $input[$i]['tanggalSelesai'],
                    'Tanggal_pengembalian' => $input[$i]['tanggalSelesai'],
                    'Waktu_pengambilan' => $input[$i]['waktuPakai'],
                    'Waktu_pengembalian' => $input[$i]['waktuSelesai'],
                    'Jumlah_pinjam' => $jumlahpinjam,
                    'Is_Personal' => $input[$i]['selectedOptionPersonal'],
                    'Is_Organisation' => $input[$i]['selectedOptionOrganisation'],
                    'Is_Eksternal' => $input[$i]['selectedOptionEksternal'],
                    'DokumenID' => $input[$i]['dokumen']
                ]);
                $totalpinjamalat[] = $peminjaman_alat;
            };
        }

        return response()->json([
            'status' => true, 'message' => "Registration Success", 'peminjaman_ruangan_bridge' => $semuapeminjaman,
            'peminjaman' => $peminjaman, 'peminjaman_alat_bridge' => $totalpinjamalat
        ]);
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
    public function getPeminjamanRuangan($UserID)
    {
        $peminjam = Peminjam::where('UserID', $UserID)->first();
        $peminjamID = $peminjam->PeminjamID;
        $peminjaman = Peminjaman::where('PeminjamID', $peminjamID)->get();
        $allroombooking = [];

        foreach ($peminjaman as $booking) {
            $peminjamanID = $booking->PeminjamanID;
            $datapinjamruangan = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $peminjamanID)->get();

            foreach ($datapinjamruangan as $data) {
                $peminjamanruanganid = $data->Peminjaman_Ruangan_ID;
                $ruanganid = $data->RuanganID;
                $tanggalawal = $data->Tanggal_pakai_awal;
                $tanggalakhir = $data->Tanggal_pakai_akhir;
                $waktupakai = $data->Waktu_pakai;
                $waktuselesai = $data->Waktu_selesai;
                $cariroom = Ruangan::where('RuanganID', $ruanganid)->first();
                $namaruangan = $cariroom->Nama_ruangan;
                $peminjamanid = $data->PeminjamanID;
                $keterangan = $data->Keterangan;

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

    public function jadwalPeminjaman($Tanggal_pakai_awal, $Tanggal_pakai_akhir)
    {
        $peminjamanruangan = Peminjaman_Ruangan_Bridge::where('Tanggal_pakai_awal', "<=", $Tanggal_pakai_awal)
            ->where('Tanggal_pakai_akhir', ">=", $Tanggal_pakai_akhir)
            ->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '>', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '<=', $Tanggal_pakai_akhir);
            })->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '>', $Tanggal_pakai_akhir);
            })->pluck('RuanganID')->unique();

        $dataruangan = Ruangan::pluck('RuanganID', 'Nama_ruangan');
        $ruangan = $dataruangan->diff($peminjamanruangan);

        return $ruangan->toArray();
        //return response()->json(['data tabrak' => $peminjamanruangan, 'list ruangan' => $dataruangan, 'ruangan tersedia' => $ruangan]);
    }
}
