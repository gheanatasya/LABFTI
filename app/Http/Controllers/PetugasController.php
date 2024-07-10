<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use App\Http\Requests\StorePetugasRequest;
use App\Http\Requests\UpdatePetugasRequest;
use App\Models\Peminjam;
use App\Models\User;

class PetugasController extends Controller
{
    // data untuk halaman petugas
    public function allPetugas()
    {
        $semuaPetugas = Petugas::all();
        $allRecordPetugas = [];

        foreach ($semuaPetugas as $petugas) {
            $userid = $petugas->UserID;
            $datauser = User::where('UserID', $userid)->first();
            $nama = $petugas->Nama;
            $tglbekerja = $petugas->Tgl_Bekerja;
            $tglberhenti = $petugas->Tgl_Berhenti;
            $foto = $petugas->Foto;
            $email = $datauser->Email;
            $nim = $datauser->NIM_NIDN;

            if (substr($nim, 0, 2) === '71') {
                $prodi = 'Informatika';
            } elseif (substr($nim, 0, 2) === '72') {
                $prodi = 'Sistem Informasi';
            } else {
                $prodi = 'Prodi tidak dikenali';
            }

            $record = [
                'Nama' => $nama,
                'Email' => $email,
                'NIM' => $nim,
                'Prodi' => $prodi,
                'Tgl_Bekerja' => $tglbekerja,
                'Tgl_Berhenti' => $tglberhenti,
                'Foto' => $foto,
                'UserID' => $userid
            ];

            $allRecordPetugas[] = $record;
        }

        return $allRecordPetugas;
    }

    //update data 
    public function update(UpdatePetugasRequest $request, $UserID)
    {
        $petugas = Petugas::where('UserID', $UserID)->first();

        if ($petugas === null) {
            return response()->json(['error' => 'Petugas not found'], 404);
        }

        $request->validate([
            'Tgl_Bekerja' => 'required',
            'Tgl_Berhenti' => 'required',
        ]);

        $petugas->Tgl_Bekerja = $request->get('Tgl_Bekerja');
        $petugas->Tgl_Berhenti = $request->get('Tgl_Berhenti');
        $petugas->Foto = $request->get('Foto');
        $petugas->save();

        return response()->json(['message' => 'Petugas berhasil diperbarui', 'data' => $petugas]);
    }

    //hapus petugas
    public function delete($UserID)
    {
        $petugas = Petugas::where('UserID', $UserID)->first();
        $user = User::where('UserID', $UserID)->first();
        $user->User_role = 'Mahasiswa';
        $user->save();
        $petugas->delete();

        return response()->json(['message' => 'Petugas berhasil dihapus'], 204);
    }

    //tambah petugas
    public function store(StorePetugasRequest $request)
    {
        $input = $request->all();
        $user = User::where('Email', $input['Email'])->first();
        $userid = $user->UserID;
        // return $userid;

        $peminjam = Peminjam::where('UserID', $userid)->first();
        $nama = $peminjam->Nama;

        Petugas::create([
            'Nama' => $nama,
            'UserID' => $userid,
            'Foto' => $input['Foto'],
            'Tgl_Bekerja' => $input['Tgl_Bekerja'],
            'Tgl_Berhenti' => $input['Tgl_Berhenti'],
        ]);

        $user->User_role = 'Petugas';
        $user->save();

        return response()->json(['status' => true, 'message' => "Tambahkan Petugas Success"]);
    }

}
