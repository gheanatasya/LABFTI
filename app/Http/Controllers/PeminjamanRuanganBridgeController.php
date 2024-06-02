<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman_Ruangan_Bridge;
use App\Http\Requests\StorePeminjaman_Ruangan_BridgeRequest;
use App\Http\Requests\UpdatePeminjaman_Ruangan_BridgeRequest;
use App\Models\Alat;
use App\Models\Detail_Alat;
use App\Models\Peminjam;
use App\Models\Ruangan;
use App\Models\Peminjaman;
use App\Models\Peminjaman_Alat_Bridge;
use App\Models\Persetujuan;
use App\Models\Status;
use App\Models\Status_Peminjaman;
use App\Models\User;

class PeminjamanRuanganBridgeController extends Controller
{
    // mengambil semua data pada db
    public function getAllPeminjamanRuangan()
    {
        return Peminjaman_Ruangan_Bridge::all();
    }

    //ambil data sesuai id
    public function show($Peminjaman_Ruangan_ID)
    {
        return Peminjaman_Ruangan_Bridge::find($Peminjaman_Ruangan_ID);
    }

    //tambah data
    public function store(StorePeminjaman_Ruangan_BridgeRequest $request)
    {
        setlocale(LC_ALL, 'id_ID');
        $input = $request->all();
        //dd($input);

        $user = Peminjam::where('UserID', $request[0]['UserID'])->first();
        $peminjamID = $user->PeminjamID;

        $peminjaman = Peminjaman::create([
            'PeminjamID' => $peminjamID,
            'Tanggal_pinjam' => date('d-m-Y')
        ]);
        //dd($peminjaman);
        $peminjamanid = $peminjaman->PeminjamanID;
        $semuapeminjaman = [];


        for ($i = 0; $i < count($input); $i++) {
            $ruanganid = Ruangan::where('Nama_ruangan', $input[$i]['selectedRuangan'])->first();
            $idroom = $ruanganid->RuanganID;

            $peminjaman_ruangan = Peminjaman_Ruangan_Bridge::create([
                'PeminjamanID' => $peminjamanid,
                'RuanganID' => $idroom,
                'Tanggal_pakai_awal' => $input[$i]['tanggalAwal'],
                'Tanggal_pakai_akhir' => $input[$i]['tanggalSelesai'],
                'Keterangan' => $input[$i]['keterangan'],
                'Is_Personal' => $input[$i]['selectedOptionPersonal'],
                'Is_Organisation' => $input[$i]['selectedOptionOrganisation'],
                'Is_Eksternal' => $input[$i]['selectedOptionEksternal'],
                'DokumenID' => $input[$i]['dokumen']
            ]);

            $peminjaman_ruangan_ID = $peminjaman_ruangan->Peminjaman_Ruangan_ID;
            $semuapeminjaman[] = $peminjaman_ruangan;

            $totalpinjamalat = [];

            if (count($input[$i]['alat']) > 0) {
                foreach ($input[$i]['alat'] as $tool) {
                    if (!empty($tool['nama']) && $tool['jumlahPinjam'] > 0) {
                        $detail = Alat::where('Nama', $tool['nama'])->first();
                        $jumlahpinjam = $tool['jumlahPinjam'];
                        $alatID = $detail->AlatID;
                        $peminjaman_alat = Peminjaman_Alat_Bridge::create([
                            'PeminjamanID' => $peminjamanid,
                            'AlatID' => $alatID,
                            'Tanggal_pakai_awal' => $input[$i]['tanggalAwal'],
                            'Tanggal_pakai_akhir' => $input[$i]['tanggalSelesai'],
                            'Jumlah_pinjam' => $jumlahpinjam,
                            'Is_Personal' => $input[$i]['selectedOptionPersonal'],
                            'Is_Organisation' => $input[$i]['selectedOptionOrganisation'],
                            'Is_Eksternal' => $input[$i]['selectedOptionEksternal'],
                            'DokumenID' => $input[$i]['dokumen'],
                            'Keterangan' => $input[$i]['keterangan']
                        ]);

                        $stokSedia = $detail->Jumlah_ketersediaan;
                        $stokAkhir = $stokSedia - $jumlahpinjam;
                        $currentStok = Alat::where('AlatID', $alatID)->update(['Jumlah_ketersediaan' => $stokAkhir]);

                        $totalpinjamalat[] = $peminjaman_alat;
                    }
                };
            }
        }

        return response()->json([
            'status' => true, 'message' => "Registration Success", 'peminjaman_ruangan_bridge' => $semuapeminjaman,
            'peminjaman' => $peminjaman, 'peminjaman_alat_bridge' => $totalpinjamalat
        ]);
    }

    //mengubah data semua row
    public function updateSemuaRow(UpdatePeminjaman_Ruangan_BridgeRequest $request, Peminjaman_Ruangan_Bridge $Peminjaman_Ruangan_ID)
    {
        $Peminjaman_Ruangan_ID->update($request->all());
        return response()->json(['message' => 'Peminjaman Ruangan berhasil diperbarui', 'data' => $Peminjaman_Ruangan_ID]);
    }

    //hapus data
    public function delete($Peminjaman_Ruangan_ID)
    {
        $peminjamanruangan = Peminjaman_Ruangan_Bridge::find($Peminjaman_Ruangan_ID);
        $peminjamanruangan->delete();
        return response()->json(['message' => 'peminjamanruangan berhasil dihapus'], 204);
    }

    //mengubah data user yang login
    public function update(UpdatePeminjaman_Ruangan_BridgeRequest $request, $Peminjaman_Ruangan_ID)
    {
        $peminjamanruangan = Peminjaman_Ruangan_Bridge::find($Peminjaman_Ruangan_ID);

        // Validasi data masukan
        /*  $request->validate([ */
        /*      'email' => 'required|email', */
        /*      'password' => 'required', */
        /*  ]); */
        // Update email dan password
        /* $user->Email = $request->email; */
        /* $user->Password = bcrypt($request->password); */
        /* $user->save(); */
        /* return response()->json(['message' => 'Email dan password berhasil diperbarui', 'data' => $user]); */
    }

    //mengambil data berdasarkan peminjamanID
    public function getAllPeminjamanRuanganByPeminjamanID($PeminjamanID)
    {
        $peminjaman = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $PeminjamanID)->get();
        if ($peminjaman->isEmpty()) {
            return null;
        } else {
            return $peminjaman;
        }
    }

    // mengambil nama ruangan berdasarkan ID 
    public function getNameByID($RuanganID)
    {
        $ruangan = Ruangan::where('RuanganID', $RuanganID)->first();
        if ($ruangan) {
            return response()->json(['Nama_ruangan' => $ruangan->Nama_ruangan]);
        } else {
            return response()->json(['Error' => 'Ruangan not found'], 404);
        }
    }

    //mengambil data-data peminjaman ruangan
    public function getPeminjamanRuangan($UserID)
    {
        $peminjam = Peminjam::where('UserID', $UserID)->first();
        $peminjamID = $peminjam->PeminjamID;
        $namapeminjam = $peminjam->Nama;
        $peminjaman = Peminjaman::where('PeminjamID', $peminjamID)->get();
        $allroombooking = [];

        foreach ($peminjaman as $booking) {
            $peminjamanID = $booking->PeminjamanID;
            $tanggalpinjam = $booking->Tanggal_pinjam;
            $datapinjamruangan = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $peminjamanID)->get();

            foreach ($datapinjamruangan as $data) {
                $peminjamanruanganid = $data->Peminjaman_Ruangan_ID;
                $ruanganid = $data->RuanganID;
                $tanggalawal = $data->Tanggal_pakai_awal;
                $tanggalakhir = $data->Tanggal_pakai_akhir;
                $cariroom = Ruangan::where('RuanganID', $ruanganid)->first();
                $namaruangan = $cariroom->Nama_ruangan;
                $peminjamanid = $data->PeminjamanID;
                $keterangan = $data->Keterangan;
                $isPersonal = $data->Is_Personal;
                $isOrganisation = $data->Is_Organisation;
                $isEksternal = $data->Is_Eksternal;
                $persetujuan = Persetujuan::where('PeminjamanID', $peminjamanID)->first();
                $dekan = $persetujuan->Dekan_Approve ?? null;
                $wd2 = $persetujuan->WD2_Approve ?? null;
                $wd3 = $persetujuan->WD3_Approve ?? null;
                $kepala = $persetujuan->Kepala_Approve ?? null;
                $petugas = $persetujuan->Petugas_Approve ?? null;
                $koord = $persetujuan->Koordinator_Approve ?? null;
                $namastatus = 'Diproses';

                if (!empty($persetujuan)) {
                    if ($isOrganisation) {
                        if ($wd3 && $kepala && $koord && $petugas) {
                            $namastatus = 'Diterima';
                        } elseif ($wd3 === null || $kepala === null || $koord === null || $petugas === null) {
                            $namastatus = 'Diproses';
                        } elseif ($wd3 === false || $kepala === false || $koord === false || $petugas === false) {
                            $namastatus = 'Ditolak';
                        }
                    } elseif ($isEksternal) {
                        if ($dekan && $kepala && $koord && $petugas) {
                            $namastatus = 'Diterima';
                        } elseif ($dekan === null || $kepala === null || $koord === null || $petugas === null) {
                            $namastatus = 'Diproses';
                        } elseif ($dekan === false || $kepala === false || $koord === false || $petugas === false) {
                            $namastatus = 'Ditolak';
                        }
                    } elseif ($isPersonal) {
                        if ($petugas) {
                            $namastatus = 'Diterima';
                        } elseif ($petugas === null) {
                            $namastatus = 'Diproses';
                        } elseif ($petugas === false) {
                            $namastatus = 'Ditolak';
                        }
                    } else {
                        if ($wd2 && $kepala && $koord && $petugas) {
                            $namastatus = 'Diterima';
                        } elseif ($wd2 === null || $kepala === null || $koord === null || $petugas === null) {
                            $namastatus = 'Diproses';
                        } elseif ($wd2 === false || $kepala === false || $koord === false || $petugas === false) {
                            $namastatus = 'Ditolak';
                        }
                    }
                }

                $recordData = [
                    'peminjamanruanganid' => $peminjamanruanganid,
                    'peminjamanid' => $peminjamanid,
                    'tanggalpinjam' => $tanggalpinjam,
                    'tanggalawal' => $tanggalawal,
                    'tanggalakhir' => $tanggalakhir,
                    'keterangan' => $keterangan,
                    'namaruangan' => $namaruangan,
                    'status' => $namastatus,
                    'namapeminjam' => $namapeminjam
                ];

                $allroombooking[] = $recordData;
            }
        }

        return $allroombooking;
    }

    //mengambil keterangan peminjaman ruangan 
    public function getKeterangan($PeminjamanID)
    {
        $peminjaman = Peminjaman::where('PeminjamanID', $PeminjamanID)->first();
        if ($peminjaman) {
            return response()->json(['Keterangan' => $peminjaman->Keterangan]);
        } else {
            return response()->json(['Error' => 'Keterangan not found'], 404);
        }
    }

    //menghapus peminjaman berdasarkan peminjamanID
    public function deletePeminjamanRuangan($PeminjamanID, $RuanganID)
    {
        $peminjaman = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $PeminjamanID)
            ->where('RuanganID', $RuanganID)
            ->first();

        if ($peminjaman) {
            $peminjaman->delete();
            return response()->json(['message' => 'Peminjaman Ruangan berhasil dihapus'], 204);
        } else {
            return response()->json(['Error' => 'Peminjaman Ruangan tidak ditemukan']);
        }
    }

    //mengecek apabila terdapat relasi dengan peminjaman alat
    public function checkRelation($PeminjamanID)
    {
        $peminjaman = Peminjaman_Alat_Bridge::where('PeminjamanID', $PeminjamanID)->first();

        if ($peminjaman) {
            return response()->json(['relasi' => true]);
        } else {
            return response()->json(['relasi' => false]);
        }
    }

    public function jadwalPeminjaman($Tanggal_pakai_awal, $Tanggal_pakai_akhir)
    {
        $peminjamanruangan = Peminjaman_Ruangan_Bridge::where('Tanggal_pakai_awal', "<=", $Tanggal_pakai_awal)
            ->where('Tanggal_pakai_akhir', ">=", $Tanggal_pakai_akhir)
            ->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '>', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '<=', $Tanggal_pakai_akhir);
            })->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '>', $Tanggal_pakai_akhir);
            })->pluck('RuanganID')->unique();

        $dataruangan = Ruangan::pluck('RuanganID', 'Nama_ruangan');
        $ruangan = $dataruangan->diff($peminjamanruangan);

        $room = $ruangan->toArray();
        $array = array_keys($room);
        $detailRoom = [];

        foreach ($array as $availableRoom) {
            $ambildata = Ruangan::where('Nama_ruangan', $availableRoom)->first();
            $detailRoom[] = $ambildata;
        }

        $peminjamanalat = Peminjaman_Alat_Bridge::where('Tanggal_pakai_awal', "<=", $Tanggal_pakai_awal)
            ->where('Tanggal_pakai_akhir', ">=", $Tanggal_pakai_akhir)
            ->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '>', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '<=', $Tanggal_pakai_akhir);
            })->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '>', $Tanggal_pakai_akhir);
            })->pluck('AlatID')->unique();
        $dataalat = Alat::pluck('AlatID', 'Nama', 'Jumlah_ketersediaan');
        $alat = $dataalat->diff($peminjamanalat);
        $tool = $alat->toArray();
        $array = array_keys($tool);

        $detailTool = [];
        foreach ($array as $availableTool) {
            $ambildata = Alat::where('Nama', $availableTool)->first();
            $detailTool[] = $ambildata;
        }

        return response()->json(['availableRoom' => $array, 'detailRuangan' => $detailRoom]);
    }

    //ambil semua data peminjaman untuk admin 
    public function getAllPeminjamanforAccRuangan()
    {
        $peminjaman = Peminjaman::all();
        $totalsemuapeminjaman = [];

        foreach ($peminjaman as $booking) {
            $peminjamanID = $booking->PeminjamanID;
            $tanggalpinjam = $booking->Tanggal_pinjam;
            $peminjamID = $booking->PeminjamID;
            $peminjam = Peminjam::where('PeminjamID', $peminjamID)->first();
            $namapeminjam = $peminjam->Nama;
            $datapinjamruangan = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $peminjamanID)->get();

            foreach ($datapinjamruangan as $data) {
                $peminjamanruanganid = $data->Peminjaman_Ruangan_ID;
                $ruanganid = $data->RuanganID;
                $tanggalawal = $data->Tanggal_pakai_awal;
                $tanggalakhir = $data->Tanggal_pakai_akhir;
                $cariroom = Ruangan::where('RuanganID', $ruanganid)->first();
                $namaruangan = $cariroom->Nama_ruangan;
                $peminjamanid = $data->PeminjamanID;
                $keterangan = $data->Keterangan;
                $isPersonal = $data->Is_Personal;
                $isOrganisation = $data->Is_Organisation;
                $isEksternal = $data->Is_Eksternal;
                $persetujuan = Persetujuan::where('PeminjamanID', $peminjamanID)->first();
                $dekan = $persetujuan->Dekan_Approve ?? null;
                $wd2 = $persetujuan->WD2_Approve ?? null;
                $wd3 = $persetujuan->WD3_Approve ?? null;
                $kepala = $persetujuan->Kepala_Approve ?? null;
                $petugas = $persetujuan->Petugas_Approve ?? null;
                $koord = $persetujuan->Koordinator_Approve ?? null;
                $namastatus = 'Diproses';

                if (!empty($persetujuan)) {
                    if ($isOrganisation) {
                        if ($wd3 && $kepala && $koord && $petugas) {
                            $namastatus = 'Diterima';
                        } elseif ($wd3 === null || $kepala === null || $koord === null || $petugas === null) {
                            $namastatus = 'Diproses';
                        } elseif ($wd3 === false || $kepala === false || $koord === false || $petugas === false) {
                            $namastatus = 'Ditolak';
                        }
                    } elseif ($isEksternal) {
                        if ($dekan && $kepala && $koord && $petugas) {
                            $namastatus = 'Diterima';
                        } elseif ($dekan === null || $kepala === null || $koord === null || $petugas === null) {
                            $namastatus = 'Diproses';
                        } elseif ($dekan === false || $kepala === false || $koord === false || $petugas === false) {
                            $namastatus = 'Ditolak';
                        }
                    } elseif ($isPersonal) {
                        if ($petugas) {
                            $namastatus = 'Diterima';
                        } elseif ($petugas === null) {
                            $namastatus = 'Diproses';
                        } elseif ($petugas === false) {
                            $namastatus = 'Ditolak';
                        }
                    } else {
                        if ($wd2 && $kepala && $koord && $petugas) {
                            $namastatus = 'Diterima';
                        } elseif ($wd2 === null || $kepala === null || $koord === null || $petugas === null) {
                            $namastatus = 'Diproses';
                        } elseif ($wd2 === false || $kepala === false || $koord === false || $petugas === false) {
                            $namastatus = 'Ditolak';
                        }
                    }
                }

                $recordDataRoom = [
                    'peminjamanruanganid' => $peminjamanruanganid,
                    'peminjamanid' => $peminjamanid,
                    'tanggalpinjam' => $tanggalpinjam,
                    'tanggalawal' => $tanggalawal,
                    'tanggalakhir' => $tanggalakhir,
                    'keterangan' => $keterangan,
                    'namaruangan' => $namaruangan,
                    'status' => $namastatus,
                    'namapeminjam' => $namapeminjam
                ];

                $totalsemuapeminjaman[] = $recordDataRoom;
            }
        }
        return $totalsemuapeminjaman;
    }

    public function getAllPeminjamanforAccAlat()
    {
        $peminjaman = Peminjaman::all();
        $totalsemuapeminjaman = [];

        foreach ($peminjaman as $booking) {
            $keterangan = $booking->Keterangan;
            $peminjamanID = $booking->PeminjamanID;
            $peminjamID = $booking->PeminjamID;
            $tanggalpinjam = $booking->Tanggal_pinjam;
            $peminjam = Peminjam::where('PeminjamID', $peminjamID)->first();
            $namapeminjam = $peminjam->Nama;
            $datapinjamalat = Peminjaman_Alat_Bridge::where('PeminjamanID', $peminjamanID)->get();

            foreach ($datapinjamalat as $data) {
                $peminjamanalatid = $data->Peminjaman_Alat_ID;
                $alatid = $data->AlatID;
                $tanggalawal = $data->Tanggal_pakai_awal;
                $tanggalakhir = $data->Tanggal_pakai_akhir;
                $peminjamanid = $data->PeminjamanID;
                $alat = Alat::where('AlatID', $alatid)->first();
                $namaalat = $alat->Nama;
                $jumlahPinjam = $data->Jumlah_pinjam;
                $keterangan = $data->Keterangan;
                $isPersonal = $data->Is_Personal;
                $isOrganisation = $data->Is_Organisation;
                $isEksternal = $data->Is_Eksternal;
                $persetujuan = Persetujuan::where('PeminjamanID', $peminjamanID)->first();
                $dekan = $persetujuan->Dekan_Approve ?? null;
                $wd2 = $persetujuan->WD2_Approve ?? null;
                $wd3 = $persetujuan->WD3_Approve ?? null;
                $kepala = $persetujuan->Kepala_Approve ?? null;
                $petugas = $persetujuan->Petugas_Approve ?? null;
                $koord = $persetujuan->Koordinator_Approve ?? null;
                $namastatus = 'Diproses';

                if (!empty($persetujuan)) {
                    if ($isOrganisation) {
                        if ($wd3 && $kepala && $koord && $petugas) {
                            $namastatus = 'Diterima';
                        } elseif ($wd3 === null || $kepala === null || $koord === null || $petugas === null) {
                            $namastatus = 'Diproses';
                        } elseif ($wd3 === false || $kepala === false || $koord === false || $petugas === false) {
                            $namastatus = 'Ditolak';
                        }
                    } elseif ($isEksternal) {
                        if ($dekan && $kepala && $koord && $petugas) {
                            $namastatus = 'Diterima';
                        } elseif ($dekan === null || $kepala === null || $koord === null || $petugas === null) {
                            $namastatus = 'Diproses';
                        } elseif ($dekan === false || $kepala === false || $koord === false || $petugas === false) {
                            $namastatus = 'Ditolak';
                        }
                    } elseif ($isPersonal) {
                        if ($petugas) {
                            $namastatus = 'Diterima';
                        } elseif ($petugas === null) {
                            $namastatus = 'Diproses';
                        } elseif ($petugas === false) {
                            $namastatus = 'Ditolak';
                        }
                    } else {
                        if ($wd2 && $kepala && $koord && $petugas) {
                            $namastatus = 'Diterima';
                        } elseif ($wd2 === null || $kepala === null || $koord === null || $petugas === null) {
                            $namastatus = 'Diproses';
                        } elseif ($wd2 === false || $kepala === false || $koord === false || $petugas === false) {
                            $namastatus = 'Ditolak';
                        }
                    }
                }

                $recordDataAlat = [
                    'peminjamanalatid' => $peminjamanalatid,
                    'peminjamanid' => $peminjamanid,
                    'tanggalawal' => $tanggalawal,
                    'tanggalakhir' => $tanggalakhir,
                    'keterangan' => $keterangan,
                    'namaalat' => $namaalat,
                    'status' => $namastatus,
                    'jumlahPinjam' => $jumlahPinjam,
                    'namapeminjam' => $namapeminjam,
                    'tanggalpinjam' => $tanggalpinjam,
                ];

                $totalsemuapeminjaman[] = $recordDataAlat;
            }
        }
        return $totalsemuapeminjaman;
    }
}
