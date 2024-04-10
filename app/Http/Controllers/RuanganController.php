<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Http\Requests\StoreRuanganRequest;
use App\Http\Requests\UpdateRuanganRequest;

class RuanganController extends Controller
{
    public function getAllRuangan()
    {
        return Ruangan::all();
    }
    //ambil data sesuai id
    public function show($RuanganID)
    {
        return Ruangan::find($RuanganID);
    }
    //tambah data
    public function store(StoreRuanganRequest $request)
    {
        $ruangan = Ruangan::create($request->only(['RuanganID', 'Nama_ruangan', 'Kapasitas', 'Lokasi', 'Kategori', 'Fasilitas', 'Foto']));
        if ($ruangan) {
            return response()->json(['message' => 'Ruangan berhasil ditambahkan', 'data' => $ruangan], 201);
        } else {
            return response()->json(['message' => 'Ruangan gagal ditambahkan'], 500);
        }
    }
    //mengubah data
    public function update(UpdateRuanganRequest $request, Ruangan $RuanganID)
    {
        $RuanganID->update($request->all());
        return response()->json(['message' => 'Ruangan berhasil diperbarui', 'data' => $RuanganID]);
    }
    //hapus data
    public function delete($RuanganID)
    {
        $ruangan = Ruangan::find($RuanganID);
        $ruangan->delete();
        return response()->json(['message' => 'Ruangan berhasil dihapus'], 204);
    }
    
}
