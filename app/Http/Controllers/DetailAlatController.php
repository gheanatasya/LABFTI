<?php

namespace App\Http\Controllers;

use App\Models\Detail_Alat;
use App\Http\Requests\StoreDetail_AlatRequest;
use App\Http\Requests\UpdateDetail_AlatRequest;

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
        $detailalat = Detail_Alat::create($request->only(['DetailAlatID', 'AlatID', 'Nama_alat', 'Status_Kebergunaan', 'Status_Peminjaman', 'Foto']));
        if ($detailalat) {
            return response()->json(['message' => 'Detail Alat berhasil ditambahkan', 'data' => $detailalat], 201);
        } else {
            return response()->json(['message' => 'Detail Alat gagal ditambahkan'], 500);
        }
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
}
