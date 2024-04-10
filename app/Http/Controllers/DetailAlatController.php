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
    //mengubah data
    public function update(UpdateDetail_AlatRequest $request, Detail_Alat $DetailAlatID)
    {
        $DetailAlatID->update($request->all());
        return response()->json(['message' => 'Detail Alat berhasil diperbarui', 'data' => $DetailAlatID]);
    }
    //hapus data
    public function delete($DetailAlatID)
    {
        $detailalat = Detail_Alat::find($DetailAlatID);
        $detailalat->delete();
        return response()->json(['message' => 'Detail Alat berhasil dihapus'], 204);
    }
    
}
