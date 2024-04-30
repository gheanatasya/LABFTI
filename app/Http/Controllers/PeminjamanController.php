<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Http\Requests\StorePeminjamanRequest;
use App\Http\Requests\UpdatePeminjamanRequest;

class PeminjamanController extends Controller
{
    public function getAllPeminjaman()
    {
        return Peminjaman::all();
    }
    //ambil data sesuai id
    public function show($PeminjamanID)
    {
        return Peminjaman::find($PeminjamanID);
    }
    //tambah data
    public function store(StorePeminjamanRequest $request)
    {
        $peminjaman = Peminjaman::create($request->only(['PeminjamanID', 'PeminjamID', 'Tanggal_pinjam', 'Keterangan', 'Is_Personal', 'Is_Organisation', 'Is_Eksternal', 'DokumenID']));
        if ($peminjaman) {
            return response()->json(['message' => 'Peminjaman berhasil ditambahkan', 'data' => $peminjaman], 201);
        } else {
            return response()->json(['message' => 'Peminjaman gagal ditambahkan'], 500);
        }
    }
    //mengubah data
    public function update(UpdatePeminjamanRequest $request, Peminjaman $PeminjamanID)
    {
        $PeminjamanID->update($request->all());
        return response()->json(['message' => 'Peminjaman berhasil diperbarui', 'data' => $PeminjamanID]);
    }
    //hapus data
    public function delete($PeminjamanID)
    {
        $peminjaman = Peminjaman::find($PeminjamanID);
        $peminjaman->delete();
        return response()->json(['message' => 'Peminjaman berhasil dihapus'], 204);
    }
    
}
