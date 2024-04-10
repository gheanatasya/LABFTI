<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Http\Requests\StoreAlatRequest;
use App\Http\Requests\UpdateAlatRequest;

class AlatController extends Controller
{
    public function getAllAlat()
    {
        return Alat::all();
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
    //mengubah data
    public function update(UpdateAlatRequest $request, Alat $AlatID)
    {
        $AlatID->update($request->all());
        return response()->json(['message' => 'Alat berhasil diperbarui', 'data' => $AlatID]);
    }
    //hapus data
    public function delete($AlatID)
    {
        $alat = Alat::find($AlatID);
        $alat->delete();
        return response()->json(['message' => 'Alat berhasil dihapus'], 204);
    }
    
}
