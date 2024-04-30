<?php

namespace App\Http\Controllers;

use App\Models\Peminjam;
use App\Http\Requests\StorePeminjamRequest;
use App\Http\Requests\UpdatePeminjamRequest;
use Illuminate\Support\Str;


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

    //ambil data  berdasarkan UserID
    public function byUserID($UserID)
    {
        return Peminjam::where('UserID', $UserID)->first();
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
    public function updateSemua(UpdatePeminjamRequest $request, Peminjam $PeminjamID)
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

    //update berdasarkan ID
    public function update(UpdatePeminjamRequest $request, $Nama)
    {
        // Find the Peminjam record with the given UserID
        $peminjam = Peminjam::where('Nama', $Nama)->first();

        // Check if the Peminjam record exists
        if (!$peminjam) {
            // Return a response indicating that the record was not found
            return response()->json(['message' => 'Peminjam not found'], 404);
        }

        // Validasi data masukan
        $request->validate([
            'nama' => 'required',
        ]);

        // Update nama
        $peminjam->Nama = $request->nama;
        $peminjam->save();

        // Return a success response
        return response()->json(['message' => 'Nama berhasil diperbarui', 'data' => $peminjam]);
    }

    //ambil ID dari UserID
    public function getIDByUserID($UserID)
    {
        $peminjam = Peminjam::where('UserID', $UserID)->first();
        if ($peminjam) {
            return response()->json(['PeminjamID' => $peminjam->PeminjamID,]);
        } else {
            return response()->json(['Error' => 'Peminjam not found'], 404);
        }
    }
}
