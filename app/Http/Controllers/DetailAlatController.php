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
        Detail_Alat::create([
            'DetailAlatID' => $input['kodeDetailAlat'],
            'AlatID' => $input['kodeAlat'],
            'Nama_alat' => $input['namaDetailAlat'],
            'Status_Kebergunaan' => $input['statusKebergunaan'],
            'Status_Peminjaman' => $input['statusPeminjaman'],
            'Foto' => null
        ]);

        $alat = Alat::where('AlatID', $input['kodeAlat'])->first();
        $jumlahalat = $alat->Jumlah_ketersediaan;
        $alat->Jumlah_ketersediaan = $jumlahalat + 1;
        $alat->save();
        return response()->json(['status' => true, 'message' => "Registration Success"]);
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
        ]);

        $detailalat->Nama_alat = $request->get('namaDetailAlat');
        $detailalat->Status_Kebergunaan = $request->get('statusKebergunaan');
        $detailalat->Status_Peminjaman = $request->get('statusPeminjaman');
        $detailalat->Foto = $request->get('foto');
        $detailalat->save();

        return response()->json(['message' => 'Detail alat berhasil diperbarui', 'data' => $detailalat]);
    }
    
    //hapus data
    public function delete($DetailAlatID)
    {
        $detailalat = Detail_Alat::find($DetailAlatID);
        $detailalat->delete();
        return response()->json(['message' => 'Detail Alat berhasil dihapus'], 204);
    }

    //tambah foto, edit foto
    public function tambahFoto(StoreDetail_AlatRequest $request, $DetailAlatID){
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

        foreach ($data as $foto){
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

        return response()->json(['message' => 'File uploaded successfully!']);
    }
}
