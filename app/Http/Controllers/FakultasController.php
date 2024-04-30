<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Http\Requests\StoreFakultasRequest;
use App\Http\Requests\UpdateFakultasRequest;

class FakultasController extends Controller
{
    // mengambil semua data pada db
    public function getAllFakultas()
    {
        return Fakultas::all();
    }

    //ambil data sesuai id
    public function show($FakultasID)
    {
        return Fakultas::find($FakultasID);
    }

    //tambah data
    public function store(StoreFakultasRequest $request)
    {
        $fakultas = Fakultas::create($request->only(['FakultasID', 'Nama_fakultas', 'created_at', 'updated_at']));
        if ($fakultas) {
            return response()->json(['message' => 'Fakultas berhasil ditambahkan', 'data' => $fakultas], 201);
        } else {
            return response()->json(['message' => 'Fakultas gagal ditambahkan'], 500);
        }
    }

    //mengubah data
    public function update(UpdateFakultasRequest $request, Fakultas $FakultasID)
    {
        $FakultasID->update($request->all());
        return response()->json(['message' => 'Fakultas berhasil diperbarui', 'data' => $FakultasID]);
    }

    //hapus data
    public function delete($FakultasID)
    {
        $fakultas = Fakultas::find($FakultasID);
        $fakultas->delete();
        return response()->json(['message' => 'Fakultas berhasil dihapus'], 204);
    }

    //ambil ID Fakultas yang dipilih
    public function getFakultasIDByName($Nama_fakultas)
    {
        $fakultas = Fakultas::where('Nama_fakultas', $Nama_fakultas)->first();
        if ($fakultas) {
            return response()->json(['FakultasID' => $fakultas->FakultasID]);
        } else {
            return response()->json(['Error' => 'Fakultas not found'], 404);
        }
    }
}
