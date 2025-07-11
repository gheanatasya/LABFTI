<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use App\Http\Requests\StorePetugasRequest;
use App\Http\Requests\UpdatePetugasRequest;
use App\Models\Peminjam;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

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

        usort($allRecordPetugas, function ($a, $b) {
            return strcmp($a['NIM'], $b['NIM']);
        });

        return $allRecordPetugas;
    }

    //update data 
    public function update(UpdatePetugasRequest $request, $UserID)
    {
        $petugas = Petugas::where('UserID', $UserID)->first();

        if ($petugas === null) {
            return response()->json(['error' => 'Petugas not found'], 404);
        }

        /* $request->validate([
            'Tgl_Bekerja' => 'required',
            'Tgl_Berhenti' => 'required',
        ]); */

        if ($request->get('Tgl_Berhenti') !== null) {
            $petugas->Tgl_Berhenti = $request->get('Tgl_Berhenti');
            $user = User::where('UserID', $UserID)->first();
            $nim = $user->NIM_NIDN;
            if (substr($nim, 0, 2) === '71') {
                $prodi = 'Informatika';
            } elseif (substr($nim, 0, 2) === '72') {
                $prodi = 'Sistem Informasi';
            } else {
                $prodi = 'Prodi tidak dikenali';
            }
            $user->User_role = 'Mahasiswa';
            $user->save();
            $petugas->save();
        } else {
            $petugas->Tgl_Bekerja = $request->get('Tgl_Bekerja');
            $petugas->save();
            $user = User::where('UserID', $UserID)->first();
            $nim = $user->NIM_NIDN;
            if (substr($nim, 0, 2) === '71') {
                $prodi = 'Informatika';
            } elseif (substr($nim, 0, 2) === '72') {
                $prodi = 'Sistem Informasi';
            } else {
                $prodi = 'Prodi tidak dikenali';
            }
        }

        $newPetugas = [
            'Nama' => $petugas->Nama,
            'Email' => $user->Email,
            'NIM' => $user->NIM_NIDN,
            'Prodi' => $prodi,
            'Tgl_Bekerja' => $petugas->Tgl_Bekerja,
            'Tgl_Berhenti' => $petugas->Tgl_Berhenti,
            'Foto' => $petugas->Foto,
            'UserID' => $UserID
        ];

        return response()->json(['message' => 'Petugas berhasil diperbarui', 'data' => $newPetugas]);
    }

    //hapus petugas
    public function delete($UserID)
    {
        $petugas = Petugas::where('UserID', $UserID)->first();
        $user = User::where('UserID', $UserID)->first();
        $user->User_role = 'Mahasiswa';
        $user->save();
        $petugas->delete();

        return response()->json(['message' => 'Petugas berhasil dihapus', 'UserID' => $UserID]);
    }

    //tambah petugas
    public function store(StorePetugasRequest $request)
    {
        $input = $request->all();
        $user = User::where('Email', $input['Email'])->first();
        $userid = $user->UserID;
        $nim = $user->NIM_NIDN;
        $niminput = $input['NIM'];

        if ($nim !== intval($niminput)) {
            return response()->json(['status' => false, 'message' => "NIM/NIDN tidak sesuai", $niminput, $nim]);
        }
        // return $userid;

        $peminjam = Peminjam::where('UserID', $userid)->first();
        $nama = $peminjam->Nama;

        Petugas::create([
            'Nama' => $nama,
            'UserID' => $userid,
            'Foto' => null,
            'Tgl_Bekerja' => $input['Tgl_Bekerja'],
            'Tgl_Berhenti' => $input['Tgl_Berhenti'],
        ]);

        $directory = 'petugas/' . $nama;
        Storage::makeDirectory($directory);

        $user->User_role = 'Petugas';
        $user->save();

        return response()->json(['status' => true, 'message' => "Tambahkan Petugas Success", 'UserID' => $userid]);
    }

    public function tambahFoto(StorePetugasRequest $request)
    {
        $data = $request->file('foto');
        $userid = $request->input('userid');

        $petugas = Petugas::where('UserID', $userid)->first();
        $user = User::where('UserID', $userid)->first();
        $nim = $user->NIM_NIDN;
        $directory = 'petugas/' . $petugas->Nama;
        if (substr($nim, 0, 2) === '71') {
            $prodi = 'Informatika';
        } elseif (substr($nim, 0, 2) === '72') {
            $prodi = 'Sistem Informasi';
        } else {
            $prodi = 'Prodi tidak dikenali';
        }
        $fileInfo = [
            'originalName' => $data->getClientOriginalName(),
            'size' => $data->getSize(),
            'mimeType' => $data->getClientMimeType(),
        ];

        $path = Storage::putFileAs($directory, $data, $fileInfo['originalName']);

        $petugas->Foto = $path;
        $petugas->save();

        $newPetugas = [
            'Nama' => $petugas->Nama,
            'Email' => $user->Email,
            'NIM' => $user->NIM_NIDN,
            'Prodi' => $prodi,
            'Tgl_Bekerja' => $petugas->Tgl_Bekerja,
            'Tgl_Berhenti' => $petugas->Tgl_Berhenti,
            'Foto' => $petugas->Foto,
            'UserID' => $userid
        ];

        return response()->json(['message' => 'File uploaded successfully!', 'data' => $newPetugas]);
    }

    public function editFoto(UpdatePetugasRequest $request)
    {
        $data = $request->file('foto');
        $userid = $request->input('userid');

        $petugas = Petugas::where('UserID', $userid)->first();
        $directory = 'petugas/' . $petugas->Nama;

        $fileInfo = [
            'originalName' => $data->getClientOriginalName(),
            'size' => $data->getSize(),
            'mimeType' => $data->getClientMimeType(),
        ];

        $path = Storage::putFileAs($directory, $data, $fileInfo['originalName']);

        $petugas->Foto = $path;
        $petugas->save();

        return response()->json(['message' => 'File uploaded successfully!']);
    }
}
