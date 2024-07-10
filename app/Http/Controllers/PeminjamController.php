<?php

namespace App\Http\Controllers;

use App\Models\Peminjam;
use App\Http\Requests\StorePeminjamRequest;
use App\Http\Requests\UpdatePeminjamRequest;
use App\Models\Fakultas;
use App\Models\Instansi;
use App\Models\Program_Studi;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
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
            'InstansiID' => $input['selectedInstansiID'],
            'Total_batal' => 0,
            'Tanggal_batal_terakhir' => null
        ]);

        $directory = 'dokumen/' . $input['name'];
        Storage::makeDirectory($directory);

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
        $peminjam = Peminjam::where('Nama', $Nama)->first();

        if (!$peminjam) {
            return response()->json(['message' => 'Peminjam not found'], 404);
        }

        // Validasi data masukan
        $request->validate([
            'nama' => 'required',
        ]);

        // Update nama
        $peminjam->Nama = $request->nama;
        $peminjam->save();

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

    //ambil data untuk profil
    public function getDataforProfil($UserID)
    {
        $user = User::where('UserID', $UserID)->first();
        $nim = $user->NIM_NIDN;
        $email = $user->Email;
        $role = $user->User_role;

        $peminjam = Peminjam::where('UserID', $UserID)->first();
        $nama = $peminjam->Nama;
        $prodiID = $peminjam->ProdiID;
        $instansiID = $peminjam->InstansiID;

        $prodi = Program_Studi::where('ProdiID', $prodiID)->first();
        $namaprodi = $prodi->Nama_prodi;
        $fakultasID = $prodi->FakultasID;

        $instansi = Instansi::where('InstansiID', $instansiID)->first();
        $namainstansi = $instansi->Nama_instansi;

        $fakultas = Fakultas::where('FakultasID', $fakultasID)->first();
        $namafakultas = $fakultas->Nama_fakultas;

        return response()->json(['NIM_NIDN' => $nim, 'Email' => $email, 'Role' => $role, 'Nama' => $nama, 'Prodi' => $namaprodi, 'Fakultas' => $namafakultas, 'Instansi' => $namainstansi]);
    }

    //reset total batal user 
    public function resetTotalBatal()
    {
        $currentMonth = date('m');
        $peminjams = Peminjam::all();
        foreach ($peminjams as $peminjam) {
            $currentTanggalBatal = $peminjam->Tanggal_batal_terakhir;
            $bulanTanggalBatal = date('m', strtotime($currentTanggalBatal));
            if ($currentMonth !== $bulanTanggalBatal) {
                $peminjam->Total_batal = 0;
                $peminjam->save();
            } 
        }
        return response()->json(['total batal berhasil diperbarui']);
    }
}
