<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use Illuminate\Http\Request;
use App\Http\Requests\StoreInstansiRequest;
use App\Http\Requests\UpdateInstansiRequest;

class InstansiController extends Controller
{
    // mengambil semua data pada db
    public function getAllInstansi()
    {
        return Instansi::all();
    }
    //ambil data sesuai id
    public function show($InstansiID)
    {
        return Instansi::find($InstansiID);
    }
    //tambah data
    public function store(StoreInstansiRequest $request)
    {
        $instansi = Instansi::create($request->only(['InstansiID', 'Nama_instansi', 'Jenis_instansi']));
        if ($instansi) {
            return response()->json(['message' => 'Instansi berhasil ditambahkan', 'data' => $instansi], 201);
        } else {
            return response()->json(['message' => '
            Instansi gagal ditambahkan'], 500);
        }
    }
    //mengubah data
    public function update(UpdateInstansiRequest $request, Instansi $InstansiID)
    {
        $InstansiID->update($request->all());
        return response()->json(['message' => 'Instansi berhasil diperbarui', 'data' => $InstansiID]);
    }
    //hapus data
    public function delete($InstansiID)
    {
        $instansi = Instansi::find($InstansiID);
        $instansi->delete();
        return response()->json(['message' => 'Instansi berhasil dihapus'], 204);
    }

    public function getInstansiIDByName($Nama_instansi)
    {
        $instansi = Instansi::where('Nama_instansi', $Nama_instansi)->first();
        if ($instansi) {
            return response()->json(['InstansiID' => $instansi->InstansiID]);
        } else {
            return response()->json(['Error' => 'Instansi not found'], 404);
        }
    }
}
