<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Http\Requests\StoreDokumenRequest;
use App\Http\Requests\UpdateDokumenRequest;
use App\Models\Peminjam;
use App\Models\Peminjaman;
use App\Models\Peminjaman_Alat_Bridge;
use App\Models\Peminjaman_Ruangan_Bridge;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function forDokumenPeminjaman(StoreDokumenRequest $request){
        $data = $request->file('dokumen');
        $userid = $request->input('UserID');
        $tanggalpinjam = $request->input('Tanggal_pinjam');
        $peminjamanruanganid = $request->input('peminjamanruanganid');
        $totalalat = $request->input('totalalat');
        $a = []; 

        $peminjam = Peminjam::where('UserID', $userid)->first();
        $nama = $peminjam->Nama;
        $peminjamid = $peminjam->PeminjamID;
        $directory = 'dokumen/' . $nama;

        $fileInfo = [
            'originalName' => $data->getClientOriginalName(),
            'size' => $data->getSize(),
            'mimeType' => $data->getClientMimeType(),
        ];

        $path = Storage::putFileAs($directory, $data, $fileInfo['originalName']);

        $dokumen = Dokumen::create([
            'Nama_dokumen' => $fileInfo['originalName'],
            'Path' => $path,
        ]);

        $peminjamanruangan = Peminjaman_Ruangan_Bridge::where('Peminjaman_Ruangan_ID', $peminjamanruanganid)->first();
        $peminjamanruangan->DokumenID = $dokumen->DokumenID;
        $peminjamanruangan->save();

        for ($i = 0; $i < $totalalat; $i++) {
            $peminjamanalatid = $request->input('peminjamanalatid'.$i);
            $a[] = $peminjamanalatid; 

            $peminjamanalat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $peminjamanalatid)->first();
            $peminjamanalat->DokumenID = $dokumen->DokumenID;
            $peminjamanalat->save();
        }

        //return $tanggalpinjam;
        return response()->json(['message' => 'File uploaded successfully!']);
    }

    public function forDokumenPeminjamanAlat(StoreDokumenRequest $request){
        $data = $request->file('dokumen');
        $userid = $request->input('UserID');
        $tanggalpinjam = $request->input('Tanggal_pinjam');
        $totalalat = $request->input('totalalat');
        $a = []; 

        $peminjam = Peminjam::where('UserID', $userid)->first();
        $nama = $peminjam->Nama;
        $peminjamid = $peminjam->PeminjamID;
        $directory = 'dokumen/' . $nama;

        $fileInfo = [
            'originalName' => $data->getClientOriginalName(),
            'size' => $data->getSize(),
            'mimeType' => $data->getClientMimeType(),
        ];

        $path = Storage::putFileAs($directory, $data, $fileInfo['originalName']);

        $dokumen = Dokumen::create([
            'Nama_dokumen' => $fileInfo['originalName'],
            'Path' => $path,
        ]);

        for ($i = 0; $i < $totalalat; $i++) {
            $peminjamanalatid = $request->input('peminjamanalatid'.$i);
            $a[] = $peminjamanalatid; 

            $peminjamanalat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $peminjamanalatid)->first();
            $peminjamanalat->DokumenID = $dokumen->DokumenID;
            $peminjamanalat->save();
        }

        //return $tanggalpinjam;
        return response()->json(['message' => 'File uploaded successfully!']);
    }
}
