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
use App\Models\Status_Peminjaman;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use DateTime;

class PDFController extends Controller
{
    public function generatePDF($UserID, $desiredPeminjamanID, $peminjamanruanganid, $peminjamanalatid)
    {
        $user = User::where('UserID', $UserID)->first();
        $peminjam = Peminjam::where('UserID', $UserID)->first();
        $peminjamid = $peminjam->PeminjamID;
        $nama = $peminjam ? $peminjam->Nama : '';
        $nim = $user ? $user->NIM_NIDN : '';
        $role = $user ? $user->User_role : '';
        $email = $user ? $user->Email : '';

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
        $PeminjamanRuangan = Peminjaman_Ruangan_Bridge::where('Peminjaman_Ruangan_ID', $peminjamanruanganid)->first();

        function hariIndonesia($hariInggris)
        {
            $hariIndonesia = array(
                "Sunday" => "Minggu",
                "Monday" => "Senin",
                "Tuesday" => "Selasa",
                "Wednesday" => "Rabu",
                "Thursday" => "Kamis",
                "Friday" => "Jumat",
                "Saturday" => "Sabtu"
            );

            return $hariIndonesia[$hariInggris];
        }

        if ($PeminjamanRuangan !== null) {
            $personal = $PeminjamanRuangan->Is_Personal;
            $organisasi = $PeminjamanRuangan->Is_Organisation;
            $ekternal = $PeminjamanRuangan->Is_Eksternal;
            $ruanganid = $PeminjamanRuangan->RuanganID;
            $ruangan = Ruangan::find($ruanganid);
            $namaruangan = $ruangan ? $ruangan->Nama_ruangan : '';
            $tanggalawal = $PeminjamanRuangan->Tanggal_pakai_awal;
            $tanggalakhir = $PeminjamanRuangan->Tanggal_pakai_akhir;
            $keterangan = $PeminjamanRuangan->Keterangan;
            $hariawal = hariIndonesia(date('l', strtotime($tanggalawal)));
            $hariakhir = hariIndonesia(date('l', strtotime($tanggalakhir)));
            $formattedAwal = date('d/m/Y H:i', strtotime($tanggalawal));
            $formattedAkhir = date('d/m/Y H:i', strtotime($tanggalakhir));

            $recordDataRuangan = [
                'PeminjamanID' => $desiredPeminjamanID,
                'namaruangan' => $namaruangan,
                'keterangan' => $keterangan,
                'tanggalawal' => $hariawal . ', ' . $formattedAwal,
                'tanggalakhir' => $hariakhir . ', ' . $formattedAkhir,
                'personal' => $personal,
                'organisasi' => $organisasi,
                'eksternal' => $ekternal
            ];

            $recordDataAlat = [];

            $peminjamanalat = Peminjaman_Alat_Bridge::where('RuanganID', $ruanganid)
                ->where('Tanggal_pakai_awal', $tanggalawal)
                ->where('Tanggal_pakai_akhir', $tanggalakhir)
                ->get();

            if ($peminjamanalat != null) {
                foreach ($peminjamanalat as $alat) {
                    $peminjamanalatID = $alat->Peminjaman_Alat_ID;
                    $personal = $alat->Is_Personal;
                    $organisasi = $alat->Is_Organisation;
                    $ekternal = $alat->Is_Eksternal;
                    $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Alat_ID', $peminjamanalatID)->first();
                    if ($statuspeminjaman !== null) {
                        $statusid = $statuspeminjaman->StatusID;
                        if ($statusid === 2) {
                            $alatid = $alat->AlatID;
                            $ALAT = Alat::find($alatid);
                            $namalat = $ALAT ? $ALAT->Nama : '';
                            $jumlahPinjam = $alat->Jumlah_pinjam;
                            $tanggalawal = $alat->Tanggal_pakai_awal;
                            $tanggalakhir = $alat->Tanggal_pakai_akhir;
                            $keterangan = $alat->Keterangan;
                            $formattedAwal = date('d/m/Y H:i', strtotime($tanggalawal));
                            $formattedAkhir = date('d/m/Y H:i', strtotime($tanggalakhir));
                            $hariawal = hariIndonesia(date('l', strtotime($tanggalawal)));
                            $hariakhir = hariIndonesia(date('l', strtotime($tanggalakhir)));

                            $alatData = [
                                'namaalat' => $namalat,
                                'jumlahPinjam' => $jumlahPinjam,
                                'tanggalawalAlat' => $hariawal . ', ' . $formattedAwal,
                                'tanggalakhirAlat' => $hariakhir . ', ' . $formattedAkhir,
                                'keteranganAlat' => $keterangan,
                                'personal' => $personal,
                                'organisasi' => $organisasi,
                                'eksternal' => $ekternal
                            ];

                            $recordDataAlat[] = $alatData;
                        }
                    }
                }
            }
        } else {
            $recordDataRuangan = [];
            $peminjamanalat = Peminjaman_Alat_Bridge::find($peminjamanalatid);
            $alatid = $peminjamanalat->AlatID;
            $ALAT = Alat::find($alatid);
            $namalat = $ALAT ? $ALAT->Nama : '';
            $jumlahPinjam = $peminjamanalat->Jumlah_pinjam;
            $tanggalawal = $peminjamanalat->Tanggal_pakai_awal;
            $tanggalakhir = $peminjamanalat->Tanggal_pakai_akhir;
            $keterangan = $peminjamanalat->Keterangan;

            $peminjamanalatLainnya = Peminjaman_Alat_Bridge::where('PeminjamanID', $desiredPeminjamanID)
                ->where('Tanggal_pakai_awal', $tanggalawal)
                ->where('Tanggal_pakai_akhir', $tanggalakhir)
                ->where('Keterangan', $keterangan)
                ->get();

            if ($peminjamanalatLainnya != null) {
                foreach ($peminjamanalatLainnya as $alat) {
                    $ekternal = $alat->Is_Eksternal;
                    $personal = $alat->Is_Personal;
                    $organisasi = $alat->Is_Organisation;
                    $alatid = $alat->AlatID;
                    $ALAT = Alat::find($alatid);
                    $namalat = $ALAT ? $ALAT->Nama : '';
                    $jumlahPinjam = $alat->Jumlah_pinjam;
                    $tanggalawal = $alat->Tanggal_pakai_awal;
                    $tanggalakhir = $alat->Tanggal_pakai_akhir;
                    $keterangan = $alat->Keterangan;
                    $formattedAwal = date('d/m/Y H:i', strtotime($tanggalawal));
                    $formattedAkhir = date('d/m/Y H:i', strtotime($tanggalakhir));
                    $hariawal = hariIndonesia(date('l', strtotime($tanggalawal)));
                    $hariakhir = hariIndonesia(date('l', strtotime($tanggalakhir)));
                    $alatData = [
                        'namaalat' => $namalat,
                        'jumlahPinjam' => $jumlahPinjam,
                        'tanggalawalAlat' => $hariawal . ', ' . $formattedAwal,
                        'tanggalakhirAlat' => $hariakhir . ', ' . $formattedAkhir,
                        'keteranganAlat' => $keterangan,
                        'personal' => $personal,
                        'organisasi' => $organisasi,
                        'eksternal' => $ekternal
                    ];
                    $recordDataAlat[] = $alatData;
                }
            }
        }

        //var_dump($recordDataAlat);
        /*  $tanggaldownload = date('d/m/Y');
        $haridownload = date('l', strtotime($tanggaldownload));
        $haridownloadInd = hariIndonesia($haridownload);
 */
        $data = [
            'title' => 'Peminjaman Ruangan dan Alat LAB FTI UKDW',
            'nama' => $nama,
            'nim' => $nim,
            'role' => $role,
            'email' => $email,
            'namainstansi' => $namainstansi,
            'namaprodi' => $namaprodi,
            'namafakultas' => $namafakultas,
            'peminjamanDataRuangan' => $recordDataRuangan,
            'peminjamanDataAlat' => $recordDataAlat,
            'desiredPeminjamanID' => $desiredPeminjamanID,
            'tanggaldownload' => date('d/m/Y')
        ];

        //dd($data);

        $pdf = PDF::loadView('document', $data);
        return $pdf->download('document.pdf');
    }
}
