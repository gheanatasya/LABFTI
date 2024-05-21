<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Http\Requests\StoreAlatRequest;
use App\Http\Requests\UpdateAlatRequest;
use App\Models\Detail_Alat;

use function Laravel\Prompts\error;

class AlatController extends Controller
{
    public function getAllAlat()
    {
        return Alat::all()->where('Status', 'Tersedia');
    }

    //ambil data sesuai id
    public function show($AlatID)
    {
        return Alat::find($AlatID);
    }

    //tambah data
    public function store(StoreAlatRequest $request)
    {
        $alat = Alat::create($request->only(['AlatID', 'Nama', 'Jumlah_ketersediaan']));
        if ($alat) {
            return response()->json(['message' => 'Alat berhasil ditambahkan', 'data' => $alat], 201);
        } else {
            return response()->json(['message' => 'Alat gagal ditambahkan'], 500);
        }
    }

    //edit data alat 
    public function update(UpdateAlatRequest $request, $AlatID)
    {
        $alat = Alat::where('AlatID', $AlatID)->first();

        if ($alat === null) {
            return response()->json(['error' => 'Alat not found'], 404);
        }

        $request->validate([
            'namaAlat' => 'required',
            'statusAlat' => 'required',
        ]);

        $alat->Nama = $request->get('namaAlat');
        $alat->Status = $request->get('statusAlat');
        $alat->save();

        return response()->json(['message' => 'Alat berhasil diperbarui', 'data' => $alat]);
    }

    //hapus data alat
    public function delete($AlatID)
    {
        $alat = Alat::find($AlatID);
        $detailAlat = Detail_Alat::where('AlatID', $AlatID)->get();
        $semuaRusak = true;

        foreach ($detailAlat as $detail) {
            if ($detail->Status_Kebergunaan != 'Rusak') {
                $semuaRusak = false;
                break;
            }
        }

        if ($semuaRusak) {
            $alat->Status = 'Tidak Tersedia';
            $alat->save();
            return response()->json(['message' => 'Alat berhasil dihapus'], 204);
        } else {
            return 'Gagal';
        }
    }

    //data detail alat untuk halaman daftar alat
    public function forDaftarAlat()
    {
        $alat = Alat::all();
        $semuaData = [];

        foreach ($alat as $tool) {
            $namaAlat = $tool->Nama;
            $kodeAlat = $tool->AlatID;
            $statusAlat = $tool->Status;
            $detailAlat = Detail_Alat::where('AlatID', $kodeAlat)->get();
            $jumlahAlat = count($detailAlat);
            $recordData = [
                'Nama' => $namaAlat,
                'KodeAlat' => $kodeAlat,
                'JumlahAlat' => $jumlahAlat,
                'StatusAlat' => $statusAlat,
                'detailAlat' => [],
            ];

            foreach ($detailAlat as $detail) {
                $kodeDetailAlat = $detail->DetailAlatID;
                $namaDetailAlat = $detail->Nama_alat;
                $statusKebergunaan = $detail->Status_Kebergunaan;
                $statusPeminjaman = $detail->Status_Peminjaman;
                $foto = $detail->Foto;

                $recordPerDetail = [
                    'KodeDetailAlat' => $kodeDetailAlat,
                    'NamaDetailAlat' => $namaDetailAlat,
                    'StatusKebergunaan' => $statusKebergunaan,
                    'StatusPeminjaman' => $statusPeminjaman,
                    'Foto' => $foto
                ];

                $recordData['detailAlat'][] = $recordPerDetail;
            }

            $semuaData[] = $recordData;
        }

        return $semuaData;
    }
}
