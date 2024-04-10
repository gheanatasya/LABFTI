<?php

namespace App\Http\Controllers;

use App\Models\Peminjam;
use App\Http\Requests\StorePeminjamRequest;
use App\Http\Requests\UpdatePeminjamRequest;

class PeminjamController extends Controller
{
    // mengambil semua data pada db
    public function getAllPeminjam()
    {
        return Peminjam::all();
    }
    //ambil data sesuai id
    public function show($PeminjamID)
    {
        return Peminjam::find($PeminjamID);
    }
    //tambah data
    public function store(StorePeminjamRequest $request)
    {
        $input = $request->all();
        Peminjam::create([
            'Nama' => $input['name'],
            'UserID' => $input['UserID'],
            'ProdiID' => $input['selectedProdiID'],
            'InstansiID' => $input['selectedInstansiID']
        ]);
        return response()->json(['status' => true, 'message' => "Registration Success"]);
    }
    //mengubah data
    public function update(UpdatePeminjamRequest $request, Peminjam $PeminjamID)
    {
        $PeminjamID->update($request->all());
        return response()->json(['message' => 'Peminjam berhasil diperbarui', 'data' => $PeminjamID]);
    }
    //hapus data
    public function delete($PeminjamID)
    {
        $peminjam = Peminjam::find($PeminjamID);
        $peminjam->delete();
        return response()->json(['message' => 'Peminjam berhasil dihapus'], 204);
    }
}
