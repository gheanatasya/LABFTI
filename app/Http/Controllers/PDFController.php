<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Detail_Alat;
use App\Models\Fakultas;
use App\Models\Instansi;
use App\Models\Peminjam;
use App\Models\Peminjaman;
use App\Models\Peminjaman_Alat_Bridge;
use App\Models\Peminjaman_Ruangan_Bridge;
use App\Models\Program_Studi;
use App\Models\Ruangan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function generatePDF($UserID, $desiredPeminjamanID)
    {
        // Fetch user and related data
        $user = User::where('UserID', $UserID)->first();
        $peminjam = Peminjam::where('UserID', $UserID)->first();
        $peminjamid = $peminjam->PeminjamID;
        // Handle potential null values
        $nama = $peminjam ? $peminjam->Nama : '';
        $nim = $user ? $user->NIM_NIDN : '';
        $role = $user ? $user->User_role : '';
        $email = $user ? $user->Email : '';

        // Retrieve related data
        $prodiId = $peminjam ? $peminjam->ProdiID : null;
        $instansiID = $peminjam ? $peminjam->InstansiID : null;
        $instansi = Instansi::find($instansiID);
        $namainstansi = $instansi ? $instansi->Nama_instansi : '';
        $prodi = Program_Studi::find($prodiId);
        $namaprodi = $prodi ? $prodi->Nama_prodi : '';
        $fakultasId = $prodi ? $prodi->FakultasID : null;
        $fakultas = Fakultas::find($fakultasId);
        $namafakultas = $fakultas ? $fakultas->Nama_fakultas : '';

        $peminjamID = $peminjam->PeminjamID;
        $peminjaman = Peminjaman::where('PeminjamID', $peminjamID)->get();
        if ($peminjaman->isEmpty()) {
            // No matching records
        } elseif ($peminjaman->count() === 1) {
            // Single record
            $singleRecord = $peminjaman->first();
            $peminjamanid = $singleRecord->PeminjamanID;
            $keterangan = $singleRecord->Keterangan;
            $peminjamanRuang = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $peminjamanid)->get();
            $peminjamanAlat = Peminjaman_Alat_Bridge::where('PeminjamanID', $peminjamanid)->get();
            $recordData = [
                'keterangan' => $keterangan,
                'PeminjamanID' => $peminjamanid,
                'peminjamanRuang' => [],
                'peminjamanAlat' => [],
            ];
            $peminjamanData = [];

            if ($peminjamanRuang->count() === 1) {
                $singlePeminjaman = $peminjamanRuang->first();
                $tanggalawal = $singlePeminjaman->Tanggal_pakai_awal;
                $tanggalakhir = $singlePeminjaman->Tanggal_pakai_akhir;
                $waktupakai = $singlePeminjaman->Waktu_pakai;
                $waktuselesai = $singlePeminjaman->Waktu_selesai;
                $ruanganid = $singlePeminjaman->RuanganID;
                $ruangan = Ruangan::where('RuanganID', $ruanganid)->first();
                $namaruangan = $ruangan->Nama_ruangan;
                $roomBookingData = [
                    'tanggalawal' => $tanggalawal,
                    'tanggalakhir' => $tanggalakhir,
                    'waktupakai' => $waktupakai,
                    'waktuselesai' => $waktuselesai,
                    'namaruangan' => $namaruangan,
                ];
                $recordData['peminjamanRuang'][] = $roomBookingData;
            } else {
                foreach ($peminjamanRuang as $booking) {
                    $tanggalawal = $booking->Tanggal_pakai_awal;
                    $tanggalakhir = $booking->Tanggal_pakai_akhir;
                    $waktupakai = $booking->Waktu_pakai;
                    $waktuselesai = $booking->Waktu_selesai;
                    $ruanganid = $booking->RuanganID;
                    $ruangan = Ruangan::where('RuanganID', $ruanganid)->first();
                    $namaruangan = $ruangan->Nama_ruangan;

                    $roomBookingData = [
                        'tanggalawal' => $booking->Tanggal_pakai_awal,
                        'tanggalakhir' => $booking->Tanggal_pakai_akhir,
                        'waktupakai' => $booking->Waktu_pakai,
                        'waktuselesai' => $booking->Waktu_selesai,
                        'namaruangan' => $namaruangan,
                    ];
                    $recordData['peminjamanRuang'][] = $roomBookingData;
                }
            }

            if ($peminjamanAlat->count() === 1) {
                $singlePeminjaman = $peminjamanAlat->first();
                $tanggalawal = $singlePeminjaman->Tanggal_pakai_awal;
                $tanggalakhir = $singlePeminjaman->Tanggal_pakai_akhir;
                $waktupakai = $singlePeminjaman->Waktu_pakai;
                $waktuselesai = $singlePeminjaman->Waktu_selesai;
                $detailalatid = $singlePeminjaman->DetailAlatID;
                $detailalat = Detail_Alat::where('DetailAlatID', $detailalatid)->first();
                $namaalat = $detailalat->Nama_alat;
                $equipmentBookingData = [
                    'tanggalawal' => $tanggalawal,
                    'tanggalakhir' => $tanggalakhir,
                    'waktupakai' => $waktupakai,
                    'waktuselesai' => $waktuselesai,
                    'namaalat' => $namaalat,
                ];
                $recordData['peminjamanAlat'][] = $equipmentBookingData;
            } else {
                foreach ($peminjamanAlat as $booking) {
                    $tanggalawal = $booking->Tanggal_pakai_awal;
                    $tanggalakhir = $booking->Tanggal_pakai_akhir;
                    $waktupakai = $booking->Waktu_pakai;
                    $waktuselesai = $booking->Waktu_selesai;
                    $detailalatid = $singlePeminjaman->DetailAlatID;
                    $detailalat = Detail_Alat::where('DetailAlatID', $detailalatid)->first();
                    $namaalat = $detailalat->Nama_alat;

                    $equipmentBookingData = [
                        'tanggalawal' => $booking->Tanggal_pakai_awal,
                        'tanggalakhir' => $booking->Tanggal_pakai_akhir,
                        'waktupakai' => $booking->Waktu_pakai,
                        'waktuselesai' => $booking->Waktu_selesai,
                        'namaalat' => $namaalat,
                    ];
                    $recordData['peminjamanAlat'][] = $equipmentBookingData;
                }
            }
            $peminjamanData[] = $recordData;
        } else {
            // Multiple records
            $peminjamanData = [];

            foreach ($peminjaman as $record) {
                $keterangan = $record->Keterangan;
                $peminjamanid = $record->PeminjamanID;
                $peminjamanRuang = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $peminjamanid)->get();
                $peminjamanAlat = Peminjaman_Alat_Bridge::where('PeminjamanID', $peminjamanid)->get();

                $recordData = [
                    'keterangan' => $keterangan,
                    'PeminjamanID' => $peminjamanid,
                    'peminjamanRuang' => [],
                    'peminjamanAlat' => [],
                ];

                if ($peminjamanRuang->count() === 1) {
                    $singlePeminjaman = $peminjamanRuang->first();
                    $tanggalawal = $singlePeminjaman->Tanggal_pakai_awal;
                    $tanggalakhir = $singlePeminjaman->Tanggal_pakai_akhir;
                    $waktupakai = $singlePeminjaman->Waktu_pakai;
                    $waktuselesai = $singlePeminjaman->Waktu_selesai;
                    $ruanganid = $singlePeminjaman->RuanganID;
                    $ruangan = Ruangan::where('RuanganID', $ruanganid)->first();
                    $namaruangan = $ruangan->Nama_ruangan;

                    $roomBookingData = [
                        'tanggalawal' => $tanggalawal,
                        'tanggalakhir' => $tanggalakhir,
                        'waktupakai' => $waktupakai,
                        'waktuselesai' => $waktuselesai,
                        'namaruangan' => $namaruangan,
                    ];
                    $recordData['peminjamanRuang'][] = $roomBookingData;
                } else {
                    foreach ($peminjamanRuang as $booking) {
                        $tanggalawal = $booking->Tanggal_pakai_awal;
                        $tanggalakhir = $booking->Tanggal_pakai_akhir;
                        $waktupakai = $booking->Waktu_pakai;
                        $waktuselesai = $booking->Waktu_selesai;
                        $ruanganid = $booking->RuanganID;
                        $ruangan = Ruangan::where('RuanganID', $ruanganid)->first();
                        $namaruangan = $ruangan->Nama_ruangan;

                        $roomBookingData = [
                            'tanggalawal' => $booking->Tanggal_pakai_awal,
                            'tanggalakhir' => $booking->Tanggal_pakai_akhir,
                            'waktupakai' => $booking->Waktu_pakai,
                            'waktuselesai' => $booking->Waktu_selesai,
                            'namaruangan' => $namaruangan,
                        ];

                        $recordData['peminjamanRuang'][] = $roomBookingData;
                    }
                }

                if ($peminjamanAlat->count() === 1) {
                    $singlePeminjaman = $peminjamanAlat->first();
                    $tanggalawal = $singlePeminjaman->Tanggal_pakai_awal;
                    $tanggalakhir = $singlePeminjaman->Tanggal_pakai_akhir;
                    $waktupakai = $singlePeminjaman->Waktu_pakai;
                    $waktuselesai = $singlePeminjaman->Waktu_selesai;
                    $detailalatid = $singlePeminjaman->DetailAlatID;
                    $detailalat = Detail_Alat::where('DetailAlatID', $detailalatid)->first();
                    $namaalat = $detailalat->Nama_alat;

                    $equipmentBookingData = [
                        'tanggalawal' => $tanggalawal,
                        'tanggalakhir' => $tanggalakhir,
                        'waktupakai' => $waktupakai,
                        'waktuselesai' => $waktuselesai,
                        'namaalat' => $namaalat,
                    ];
                    $recordData['peminjamanAlat'][] = $equipmentBookingData;
                } else {
                    foreach ($peminjamanAlat as $booking) {
                        $tanggalawal = $booking->Tanggal_pakai_awal;
                        $tanggalakhir = $booking->Tanggal_pakai_akhir;
                        $waktupakai = $booking->Waktu_pakai;
                        $waktuselesai = $booking->Waktu_selesai;
                        $detailalatid = $booking->DetailAlatID;
                        $detailalat = Detail_Alat::where('DetailAlatID', $detailalatid)->first();
                        $namaalat = $detailalat->Nama_alat;

                        $equipmentBookingData = [
                            'tanggalawal' => $booking->Tanggal_pakai_awal,
                            'tanggalakhir' => $booking->Tanggal_pakai_akhir,
                            'waktupakai' => $booking->Waktu_pakai,
                            'waktuselesai' => $booking->Waktu_selesai,
                            'namaalat' => $namaalat,
                        ];

                        $recordData['peminjamanAlat'][] = $equipmentBookingData;
                    }
                }
            }
            $peminjamanData[] = $recordData;
        }

        // Prepare data for the view
        $data = [
            'title' => 'Peminjaman Ruangan dan Alat LAB FTI UKDW',
            'nama' => $nama,
            'nim' => $nim,
            'role' => $role,
            'email' => $email,
            'namainstansi' => $namainstansi,
            'namaprodi' => $namaprodi,
            'namafakultas' => $namafakultas,
            'peminjamanData' => $peminjamanData,
            'desiredPeminjamanID' => $desiredPeminjamanID
        ];

        // Load PDF view and download
        //$pdf = PDF::loadView('document', $data);
        //return $pdf->download('document.pdf');
        // Load PDF view and return without download
        $pdf = PDF::loadView('document', $data);
        return $pdf->download('document.pdf');
    }
}
