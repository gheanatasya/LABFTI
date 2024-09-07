<?php

namespace App\Http\Controllers;

use App\Models\Detail_Alat;
use App\Http\Requests\StoreDetail_AlatRequest;
use App\Http\Requests\UpdateDetail_AlatRequest;
use App\Models\Alat;
use Illuminate\Support\Facades\Storage;

class DetailAlatController extends Controller
{
    public function getAllDetailAlat()
    {
        return Detail_Alat::all();
    }

    //ambil data sesuai id
    public function show($DetailAlatID)
    {
        return Detail_Alat::find($DetailAlatID);
    }

    //tambah data
    public function store(StoreDetail_AlatRequest $request)
    {
        $input = $request->all();
        $detail = Detail_Alat::create([
            'KodeDetailAlat' => $input['kodeDetailAlat'],
            'AlatID' => $input['AlatID'],
            'Nama_alat' => $input['namaDetailAlat'],
            'Status_Kebergunaan' => $input['statusKebergunaan'],
            'Status_Peminjaman' => $input['statusPeminjaman'],
            'Foto' => null
        ]);

        $alat = Alat::where('AlatID', $input['AlatID'])->first();
        $jumlahalat = $alat->Jumlah_ketersediaan;
        $alat->Jumlah_ketersediaan = $jumlahalat + 1;
        $alat->save();
        return response()->json(['status' => true, 'message' => "Registration Success", 'DetailAlatID' => $detail->DetailAlatID]);
    }

    //mengubah data detail alat
    public function update(UpdateDetail_AlatRequest $request, $DetailAlatID)
    {
        $detailalat = Detail_Alat::where('DetailAlatID', $DetailAlatID)->first();

        if ($detailalat === null) {
            return response()->json(['error' => 'Detail alat not found'], 404);
        }

        $request->validate([
            'namaDetailAlat' => 'required',
            'statusKebergunaan' => 'required',
            'statusPeminjaman' => 'required',
            'kodeDetailAlat' => 'required'
        ]);

        $detailalat->Nama_alat = $request->get('namaDetailAlat');
        $detailalat->Status_Kebergunaan = $request->get('statusKebergunaan');
        $detailalat->Status_Peminjaman = $request->get('statusPeminjaman');
        $detailalat->KodeDetailAlat = $request->get('kodeDetailAlat');
        $detailalat->save();

        $newDetailAlat = [
            'KodeDetailAlat' => $detailalat->KodeDetailAlat,
            'DetailAlatID' => $detailalat->DetailAlatID,
            'NamaDetailAlat' => $detailalat->Nama_alat,
            'StatusKebergunaan' => $detailalat->Status_Kebergunaan,
            'StatusPeminjaman' => $detailalat->Status_Peminjaman,
            'Foto' => $detailalat->Foto,
            'AlatID' => $detailalat->AlatID
        ];

        return response()->json(['message' => 'Detail alat berhasil diperbarui', 'data' => $newDetailAlat]);
    }

    //hapus data
    public function delete($DetailAlatID)
    {
        $detailalat = Detail_Alat::find($DetailAlatID);
        $detailalat->delete();
        return response()->json(['message' => 'Detail Alat berhasil dihapus'], 204);
    }

    //tambah foto, edit foto
    public function tambahFoto(StoreDetail_AlatRequest $request, $DetailAlatID)
    {
        $data = $request->file('foto');
        $namaData = [];

        $detailalat = Detail_Alat::where('DetailAlatID', $DetailAlatID)->first();
        $alatid = $detailalat->AlatID;
        $alat = Alat::where('AlatID', $alatid)->first();
        $namaalat = $alat->Nama;
        $directory = 'alat/' . $namaalat;

        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        foreach ($data as $foto) {
            $fileInfo = [
                'originalName' => $foto->getClientOriginalName(),
                'size' => $foto->getSize(),
                'mimeType' => $foto->getClientMimeType(),
            ];

            $path = Storage::putFileAs($directory, $foto, $fileInfo['originalName']);
            $namaData[] = $path;
        };

        $namaDataString = implode(':', $namaData);
        $detailalat->Foto = $namaDataString;
        $detailalat->save();

        $newDetailAlat = [
            'KodeDetailAlat' => $detailalat->KodeDetailAlat,
            'DetailAlatID' => $detailalat->DetailAlatID,
            'NamaDetailAlat' => $detailalat->Nama_alat,
            'StatusKebergunaan' => $detailalat->Status_Kebergunaan,
            'StatusPeminjaman' => $detailalat->Status_Peminjaman,
            'Foto' => $detailalat->Foto,
            'AlatID' => $detailalat->AlatID
        ];

        return response()->json(['message' => 'File uploaded successfully!', 'dataTambah' => $newDetailAlat]);
    }

    public function editFoto(StoreDetail_AlatRequest $request, $DetailAlatID)
    {
        $data = $request->file('foto');
        $fotolama = $request->get('fotoLama');
        //return $fotolama;
        $namaData = [];

        if (!empty($fotolama)) {
            foreach ($fotolama as $pict) {
                $namaData[] = $pict;
            }
        }

        $detailalat = Detail_Alat::where('DetailAlatID', $DetailAlatID)->first();
        $alatid = $detailalat->AlatID;
        $alat = Alat::where('AlatID', $alatid)->first();
        $namaalat = $alat->Nama;
        $directory = 'alat/' . $namaalat;

        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        if (!empty($data)) {
            foreach ($data as $foto) {
                $fileInfo = [
                    'originalName' => $foto->getClientOriginalName(),
                    'size' => $foto->getSize(),
                    'mimeType' => $foto->getClientMimeType(),
                ];

                $path = Storage::putFileAs($directory, $foto, $fileInfo['originalName']);
                $namaData[] = $path;
            };
        };

        if (empty($fotolama) && empty($data)) {
            $detailalat->Foto = null;
            $detailalat->save();
        } else {
            $namaDataString = implode(':', $namaData);
            $detailalat->Foto = $namaDataString;
            $detailalat->save();
        }

        $newDetailAlat = [
            'KodeDetailAlat' => $detailalat->KodeDetailAlat,
            'DetailAlatID' => $detailalat->DetailAlatID,
            'NamaDetailAlat' => $detailalat->Nama_alat,
            'StatusKebergunaan' => $detailalat->Status_Kebergunaan,
            'StatusPeminjaman' => $detailalat->Status_Peminjaman,
            'Foto' => $detailalat->Foto,
            'AlatID' => $detailalat->AlatID
        ];

        return response()->json(['message' => 'File uploaded successfully!', 'dataTambah' => $newDetailAlat]);
    }
}
