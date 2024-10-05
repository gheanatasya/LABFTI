<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman_Ruangan_Bridge;
use App\Http\Requests\StorePeminjaman_Ruangan_BridgeRequest;
use App\Http\Requests\UpdatePeminjaman_Ruangan_BridgeRequest;
use App\Models\Activity_Log;
use App\Models\Alat;
use App\Models\Detail_Alat;
use App\Models\Dokumen;
use App\Models\Fakultas;
use App\Models\Instansi;
use App\Models\Peminjam;
use App\Models\Ruangan;
use App\Models\Peminjaman;
use App\Models\Peminjaman_Alat_Bridge;
use App\Models\Persetujuan;
use App\Models\Program_Studi;
use App\Models\Status;
use App\Models\Status_Peminjaman;
use App\Models\User;
use App\Mail\CancelBooking;
use App\Notifications\CancelNotification;
use App\Notifications\NewBookingNotification;
use App\Notifications\NewMessage;
use DateTime;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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

        $user = Peminjam::where('UserID', $request['UserID'])->first();
        $peminjamID = $user->PeminjamID;
        $USER = User::where('UserID', $request['UserID'])->first();
        $userrole = $USER->User_role;
        $peminjam = Peminjam::where('UserID', $request['UserID'])->first();
        $nama = $peminjam->Nama;
        $email = $USER->Email;
        $prodiid = $peminjam->ProdiID;
        $instansiid = $peminjam->InstansiID;
        $emailParts = explode("@", $email);
        $domain = $emailParts[1];

        if ($userrole === 'Mahasiswa' || $userrole === 'Petugas') {
            $nilaiprioritas = 1;
        } else {
            $nilaiprioritas = 2;
        }

        $peminjaman = Peminjaman::create([
            'PeminjamID' => $peminjamID,
            'Tanggal_pinjam' => date('d-m-Y'),
            'No_HP' => $request['Nohp']
        ]);

        $peminjamanid = $peminjaman->PeminjamanID;
        $semuapeminjaman = [];
        $persetujuanRuangan = [];
        $statuspeminjamanRuangan = [];
        $ruanganid = Ruangan::where('Nama_ruangan', $input['selectedRuangan'])->first();
        $idroom = $ruanganid->RuanganID;

        $peminjaman_ruangan = Peminjaman_Ruangan_Bridge::create([
            'PeminjamanID' => $peminjamanid,
            'RuanganID' => $idroom,
            'Tanggal_pakai_awal' => $input['tanggalAwal'],
            'Tanggal_pakai_akhir' => $input['tanggalSelesai'],
            'Keterangan' => $input['keterangan'],
            'Is_Personal' => $input['selectedOptionPersonal'],
            'Is_Organisation' => $input['selectedOptionOrganisation'],
            'Is_Eksternal' => $input['selectedOptionEksternal'],
            'DokumenID' => null,
            'Prioritas' => $nilaiprioritas
        ]);

        $detailruangan = [
            'namaruangan' => $input['selectedRuangan'],
            'tanggalawal' => $input['tanggalAwal'],
            'tanggalakhir' => $input['tanggalSelesai']
        ];

        $accRoom = Persetujuan::create([
            'Peminjaman_Ruangan_ID' => $peminjaman_ruangan->Peminjaman_Ruangan_ID,
            'Peminjaman_Alat_ID' => null,
            'Dekan_Approve' => null,
            'WD2_Approve' => null,
            'WD3_Approve' => null,
            'Kepala Lab' => null,
            'Koordinator Lab' => null,
            'Petugas' => null
        ]);

        $statuspeminjamanR = Status_Peminjaman::create([
            'Peminjaman_Ruangan_ID' => $peminjaman_ruangan->Peminjaman_Ruangan_ID,
            'Peminjaman_Alat_ID' => null,
            'StatusID' => 1,
            'Tanggal_Acc' => date('d-m-Y')
        ]);

        $peminjaman_ruangan_ID = $peminjaman_ruangan->Peminjaman_Ruangan_ID;
        $semuapeminjaman[] = $peminjaman_ruangan;
        $persetujuanRuangan[] = $accRoom;
        $statuspeminjamanRuangan[] = $statuspeminjamanR;

        $persetujuanAlat = [];
        $totalpinjamalat = [];
        $statuspeminjamanAlat = [];
        $detailalat = [];

        if (count($input['alat']) > 0) {
            foreach ($input['alat'] as $tool) {
                if ($tool['nama'] !== '' && $tool['jumlahPinjam'] > 0) {
                    $detail = Alat::where('Nama', $tool['nama'])->first();
                    $jumlahpinjam = $tool['jumlahPinjam'];
                    $alatID = $detail->AlatID;
                    $datanotif = [
                        'namaalat' => $tool['nama'],
                        'jumlahPinjam' => $tool['jumlahPinjam']
                    ];
                    $detailalat[] = $datanotif;

                    $peminjaman_alat = Peminjaman_Alat_Bridge::create([
                        'PeminjamanID' => $peminjamanid,
                        'AlatID' => $alatID,
                        'Tanggal_pakai_awal' => $input['tanggalAwal'],
                        'Tanggal_pakai_akhir' => $input['tanggalSelesai'],
                        'Jumlah_pinjam' => $jumlahpinjam,
                        'Is_Personal' => $input['selectedOptionPersonal'],
                        'Is_Organisation' => $input['selectedOptionOrganisation'],
                        'Is_Eksternal' => $input['selectedOptionEksternal'],
                        'DokumenID' => null,
                        'Keterangan' => $input['keterangan'],
                        'RuanganID' => $idroom,
                        'Prioritas' => $nilaiprioritas
                    ]);

                    $accAlat = Persetujuan::create([
                        'Peminjaman_Ruangan_ID' => null,
                        'Peminjaman_Alat_ID' => $peminjaman_alat->Peminjaman_Alat_ID,
                        'Dekan_Approve' => null,
                        'WD2_Approve' => null,
                        'WD3_Approve' => null,
                        'Kepala Lab' => null,
                        'Koordinator Lab' => null,
                        'Petugas' => null
                    ]);

                    $statuspeminjamanA = Status_Peminjaman::create([
                        'Peminjaman_Ruangan_ID' => null,
                        'Peminjaman_Alat_ID' => $peminjaman_alat->Peminjaman_Alat_ID,
                        'StatusID' => 1,
                        'Tanggal_Acc' => date('d-m-Y')
                    ]);

                    /* $stokSedia = $detail->Jumlah_ketersediaan;
                    $stokAkhir = $stokSedia - $jumlahpinjam;
                    $currentStok = Alat::where('AlatID', $alatID)->update(['Jumlah_ketersediaan' => $stokAkhir]); */

                    $totalpinjamalat[] = $peminjaman_alat;
                    $persetujuanAlat[] = $accAlat;
                    $statuspeminjamanAlat[] = $statuspeminjamanA;
                }
            };
        }

        $dataNotifikasi = [
            'subject' => 'Peminjaman Ruangan Baru',
            'detailruangan' => $detailruangan,
            'detailalat' => $detailalat,
        ];

        $title = 'Peminjaman Ruangan Baru';
        $body = $detailruangan['namaruangan'] . ' dipinjam! Segera berikan konfirmasi.';

        $userAll = User::whereIn('User_role', ['Petugas', 'Kepala Lab', 'Koordinator Lab'])->get();

        foreach ($userAll as $userr) {
            $userid = $userr->UserID;
            $peminjam = Peminjam::where('UserID', $userid)->first();
            if ($peminjam) {
                $peminjam->notify(new NewBookingNotification($dataNotifikasi));
                $peminjam->notify(new NewMessage($title, $body));
            }
        }

        if ($input['selectedOptionOrganisation'] === true) {
            $userOrg = User::where('User_role', 'Wakil Dekan 3')->first();
            $userOrgid = $userOrg->UserID;
            $peminjam = Peminjam::where('UserID', $userOrgid)->first();
            if ($peminjam) {
                $peminjam->notify(new NewBookingNotification($dataNotifikasi));
                $peminjam->notify(new NewMessage($title, $body));
            }
        } elseif ($input['selectedOptionEksternal'] === true) {
            $userExt = User::where('User_role', 'Dekan')->first();
            $userExtid = $userExt->UserID;
            $peminjam = Peminjam::where('UserID', $userExtid)->first();
            if ($peminjam) {
                $peminjam->notify(new NewBookingNotification($dataNotifikasi));
                $peminjam->notify(new NewMessage($title, $body));
            }
        } elseif (strtolower($domain) !== "ti.ukdw.ac.id" && strtolower($domain) !== "si.ukdw.ac.id") {
            $userOutside = User::where('User_role', 'Wakil Dekan 2')->first();
            $userOutsideid = $userOutside->UserID;
            $peminjam = Peminjam::where('UserID', $userOutsideid)->first();
            if ($peminjam) {
                $peminjam->notify(new NewBookingNotification($dataNotifikasi));
                $peminjam->notify(new NewMessage($title, $body));
            }
        }

        return response()->json([
            'status' => true,
            'message' => "Registration Success",
            'peminjaman_ruangan_bridge' => $semuapeminjaman,
            'peminjaman' => $peminjaman,
            'peminjaman_alat_bridge' => $totalpinjamalat,
            'persetujuanRuangan' => $persetujuanRuangan,
            'persetujuanAlat' => $persetujuanAlat,
            'statuspeminjamanruangan' => $statuspeminjamanRuangan,
            'statuspeminjamanalat' => $statuspeminjamanAlat,
            'notifikasi berhasil dikirim' => $dataNotifikasi
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
        $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Ruangan_ID', $Peminjaman_Ruangan_ID)->first();
        if ($statuspeminjaman) {
            $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
            $activitylog = Activity_Log::where('Status_PeminjamanID', $statuspeminjamanid)->first();

            if ($activitylog !== null) {
                $activitylog->delete();
            }

            $statuspeminjaman->delete();
        }
        $persetujuan = Persetujuan::where('Peminjaman_Ruangan_ID', $Peminjaman_Ruangan_ID)->first();
        $peminjamanruangan = Peminjaman_Ruangan_Bridge::find($Peminjaman_Ruangan_ID);
        $peminjamanid = $peminjamanruangan->PeminjamanID;
        $peminjaman = Peminjaman::where('PeminjamanID', $peminjamanid)->first();
        $peminjamid = $peminjaman->PeminjamID;

        if ($persetujuan !== null) {
            $organisasi = $peminjamanruangan->Is_Organisation;
            $personal = $peminjamanruangan->Is_Personal;
            $eksternal = $peminjamanruangan->Is_Eksternal;
            $tanggalpenggunaan = $peminjamanruangan->Tanggal_pakai_awal;
            $tanggalbatal = date('Y-m-d');
            $penggunaanMonth = date('m', strtotime($tanggalpenggunaan));
            $batalMonth = date('m', strtotime($tanggalbatal));
            $currentMonth = date('m');

            $dekan = $persetujuan->Dekan_Approve;
            $wd2 = $persetujuan->WD2_Approve;
            $wd3 = $persetujuan->WD3_Approve;
            $kepala = $persetujuan->Kepala_Lab;
            $koordinator = $persetujuan->Koordinator_Lab;
            $petugas = $persetujuan->Petugas;

            if ($organisasi === true) {
                if ($wd3 === true && $kepala === true && $koordinator === true && $petugas === true) {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
                    $totalbtl = $peminjam->Total_batal;
                    $peminjam->Total_batal = $totalbtl + 1;
                    $peminjam->Tanggal_batal_terakhir = $tanggalbatal;
                    $peminjam->save();
                } elseif ($wd3 === true || $kepala === true || $koordinator === true || $petugas === true) {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
                    $totalbtl = $peminjam->Total_batal;
                    $peminjam->Total_batal = $totalbtl + 1;
                    $peminjam->Tanggal_batal_terakhir = $tanggalbatal;
                    $peminjam->save();
                } /* else {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
                    $totalbtl = $peminjam->Total_batal;
                    $peminjam->Total_batal = $totalbtl + 1;
                    $peminjam->Tanggal_batal_terakhir = $tanggalbatal;
                    $peminjam->save();
                } */
            } elseif ($eksternal === true) {
                if ($dekan === true && $kepala === true && $koordinator === true && $petugas === true) {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
                    $totalbtl = $peminjam->Total_batal;
                    $peminjam->Total_batal = $totalbtl + 1;
                    $peminjam->Tanggal_batal_terakhir = $tanggalbatal;
                    $peminjam->save();
                } elseif ($dekan === true || $kepala === true || $koordinator === true || $petugas === true) {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
                    $totalbtl = $peminjam->Total_batal;
                    $peminjam->Total_batal = $totalbtl + 1;
                    $peminjam->Tanggal_batal_terakhir = $tanggalbatal;
                    $peminjam->save();
                } /* else {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
                    $totalbtl = $peminjam->Total_batal;
                    $peminjam->Total_batal = $totalbtl + 1;
                    $peminjam->Tanggal_batal_terakhir = $tanggalbatal;
                    $peminjam->save();
                } */
            } elseif ($personal === true) {
                if ($petugas === true) {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
                    $totalbtl = $peminjam->Total_batal;
                    $peminjam->Total_batal = $totalbtl + 1;
                    $peminjam->Tanggal_batal_terakhir = $tanggalbatal;
                    $peminjam->save();
                } /* elseif ($petugas === false) {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
                    $totalbtl = $peminjam->Total_batal;
                    $peminjam->Total_batal = $totalbtl + 1;
                    $peminjam->Tanggal_batal_terakhir = $tanggalbatal;
                    $peminjam->save(); */
                /* } else {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
                    $totalbtl = $peminjam->Total_batal;
                    $peminjam->Total_batal = $totalbtl + 1;
                    $peminjam->Tanggal_batal_terakhir = $tanggalbatal;
                    $peminjam->save();
                } */
            } else {
                $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
                $userid = $peminjam->UserID;
                $user = User::where('UserID', $userid)->first();
                $role = $user->User_role;
                if ($role === 'Staff' || $role === 'Dosen' || $role === 'Wakil Dekan 2' || $role === 'Wakil Dekan 3' || $role === 'Dekan' || $role === 'Kepala Lab' || $role === 'Koordinator Lab' || $role === 'Mahasiswa') {
                    if ($wd2 === true && $kepala === true && $koordinator === true && $petugas === true) {
                        $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
                        $totalbtl = $peminjam->Total_batal;
                        $peminjam->Total_batal = $totalbtl + 1;
                        $peminjam->Tanggal_batal_terakhir = $tanggalbatal;
                        $peminjam->save();
                    } elseif ($wd2 === true || $kepala === true || $koordinator === true || $petugas === true) {
                        $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
                        $totalbtl = $peminjam->Total_batal;
                        $peminjam->Total_batal = $totalbtl + 1;
                        $peminjam->Tanggal_batal_terakhir = $tanggalbatal;
                        $peminjam->save();
                    } /* else {
                        $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
                        $totalbtl = $peminjam->Total_batal;
                        $peminjam->Total_batal = $totalbtl + 1;
                        $peminjam->Tanggal_batal_terakhir = $tanggalbatal;
                        $peminjam->save();
                    } */
                }
            }

            $persetujuan->delete();
        }

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
                $persetujuan = Persetujuan::where('Peminjaman_Ruangan_ID', $peminjamanruanganid)->first();
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

                $histori = [];
                $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Ruangan_ID', $peminjamanruanganid)->first();
                if ($statuspeminjaman != null) {
                    $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
                    $activitylog = Activity_Log::where('Status_PeminjamanID', $statuspeminjamanid)->get();
                    foreach ($activitylog as $log) {
                        $recordHistory = [
                            'Acc_by' => $log->Acc_by,
                            'Tanggal_acc' => $log->Tanggal_Acc,
                            'Namastatus' => $log->Nama_status,
                            'Catatan' => $log->Catatan
                        ];

                        $histori[] = $recordHistory;
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
                    'namapeminjam' => $namapeminjam,
                    'histori' => $histori
                ];

                $allroombooking[] = $recordData;
            }
        }

        usort($allroombooking, function ($a, $b) {
            return strcmp($b['tanggalawal'], $a['tanggalawal']);
        });

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
        $peminjaman = Peminjaman_Alat_Bridge::where('PeminjamanID', $PeminjamanID)->get();
        $daftarrelasi = [];
        foreach ($peminjaman as $booking) {
            $peminjamanalatid = $booking->Peminjaman_Alat_ID;
            $daftarrelasi[] = $peminjamanalatid;
        }

        if ($peminjaman) {
            return response()->json(['relasi' => true, 'daftarrelasi' => $daftarrelasi]);
        } else {
            return response()->json(['relasi' => false]);
        }
    }

    // cek jadwal tabrakan
    public function jadwalPeminjaman($Tanggal_pakai_awal, $Tanggal_pakai_akhir)
    {
        //$statuspeminjaman = Status_Peminjaman::whereNot('StatusID', 7)->get();
        /* $peminjamanruangan = Peminjaman_Ruangan_Bridge::where('Tanggal_pakai_awal', "<", $Tanggal_pakai_awal)
            ->where('Tanggal_pakai_akhir', ">", $Tanggal_pakai_akhir)
            ->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '>', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '<', $Tanggal_pakai_akhir);
            })->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '<', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '>', $Tanggal_pakai_akhir);
            })->pluck('RuanganID')->unique(); */
        $peminjamanruangan = Peminjaman_Ruangan_Bridge::where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_akhir)
            ->where('Tanggal_pakai_akhir', '>=', $Tanggal_pakai_awal)->pluck('RuanganID')->unique();

        $dataruangan = Ruangan::pluck('RuanganID', 'Nama_ruangan');
        $ruangan = $dataruangan->diff($peminjamanruangan);

        $room = $ruangan->toArray();
        $array = array_keys($room);
        $detailRoom = [];

        foreach ($array as $availableRoom) {
            $ambildata = Ruangan::where('Nama_ruangan', $availableRoom)->first();
            $detailRoom[] = $ambildata;
        }

        usort($detailRoom, function ($a, $b) {
            return strcmp($a['Nama_ruangan'], $b['Nama_ruangan']);
        });

        return response()->json(['availableRoom' => $array, 'detailRuangan' => $detailRoom, 'dataruangan' => $dataruangan, 'peminjamanruangan' => $peminjamanruangan]);
    }

    //ambil semua data peminjaman untuk admin 
    public function getAllPeminjamanforAccRuangan()
    {
        $peminjaman = Peminjaman::all();
        $totalsemuapeminjaman = [];

        foreach ($peminjaman as $booking) {
            $peminjamanID = $booking->PeminjamanID;
            $nohp = $booking->No_HP;
            $tanggalpinjam = $booking->Tanggal_pinjam;
            $peminjamID = $booking->PeminjamID;
            $peminjam = Peminjam::where('PeminjamID', $peminjamID)->first();
            $namapeminjam = $peminjam->Nama;
            $userid = $peminjam->UserID;
            $user = User::where('UserID', $userid)->first();
            $rolepeminjam = $user->User_role;
            $datapinjamruangan = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $peminjamanID)->get();

            foreach ($datapinjamruangan as $data) {
                $peminjamanruanganid = $data->Peminjaman_Ruangan_ID;
                $ruanganid = $data->RuanganID;
                $tanggalawal = $data->Tanggal_pakai_awal;
                $tanggalakhir = $data->Tanggal_pakai_akhir;
                $dokumenID = $data->DokumenID;
                if ($dokumenID !== null) {
                    $doc = Dokumen::where('DokumenID', $dokumenID)->first();
                    $path = $doc->Path;
                    $namadokumen = $doc->Nama_dokumen;
                } else {
                    $path = null;
                    $namadokumen = null;
                }
                $cariroom = Ruangan::where('RuanganID', $ruanganid)->first();
                $cekalat = Peminjaman_Alat_Bridge::where('RuanganID', $ruanganid)
                    ->where('Tanggal_pakai_awal', $tanggalawal)
                    ->where('Tanggal_pakai_akhir', $tanggalakhir)
                    ->get();
                $kumpulanalat = [];

                foreach ($cekalat as $cek) {
                    $jumlahPinjam = $cek->Jumlah_pinjam;
                    $alatid = $cek->AlatID;
                    $alat = Alat::where('AlatID', $alatid)->first();
                    $namaalat = $alat->Nama;
                    $dokumenid = $cek->DokumenID;
                    if ($dokumenid !== null) {
                        $docx = Dokumen::where('DokumenID', $dokumenid)->first();
                        $path = $docx->Path;
                        $namadokumen = $docx->Nama_dokumen;
                    }
                    $dataAlat = ['namaalat' => $namaalat, 'jumlahPinjam' => $jumlahPinjam, 'path' => $path, 'namadokumen' => $namadokumen];
                    $kumpulanalat[] = $dataAlat;
                }

                $namaruangan = $cariroom->Nama_ruangan;
                $peminjamanid = $data->PeminjamanID;
                $keterangan = $data->Keterangan;
                $isPersonal = $data->Is_Personal;
                $isOrganisation = $data->Is_Organisation;
                $isEksternal = $data->Is_Eksternal;
                $persetujuan = Persetujuan::where('Peminjaman_Ruangan_ID', $peminjamanruanganid)->first();
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

                $histori = [];
                $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Ruangan_ID', $peminjamanruanganid)->first();
                if ($statuspeminjaman != null) {
                    $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
                    $activitylog = Activity_Log::where('Status_PeminjamanID', $statuspeminjamanid)->get();
                    foreach ($activitylog as $log) {
                        $recordHistory = [
                            'Acc_by' => $log->Acc_by,
                            'Tanggal_acc' => $log->Tanggal_Acc,
                            'Namastatus' => $log->Nama_status,
                            'Catatan' => $log->Catatan
                        ];

                        $histori[] = $recordHistory;
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
                    'namapeminjam' => $namapeminjam,
                    'personal' => $isPersonal,
                    'eksternal' => $isEksternal,
                    'organisasi' => $isOrganisation,
                    'alat' => $kumpulanalat,
                    'histori' => $histori,
                    'dekan' => $dekan,
                    'wd2' => $wd2,
                    'wd3' => $wd3,
                    'kepala' => $kepala,
                    'petugas' => $petugas,
                    'koordinator' => $koord,
                    'path' => $path,
                    'namadokumen' => $namadokumen,
                    'rolepeminjam' => $rolepeminjam,
                    'nohp' => $nohp
                ];

                $totalsemuapeminjaman[] = $recordDataRoom;
            }
        }

        usort($totalsemuapeminjaman, function ($a, $b) {
            return strcmp($b['tanggalpinjam'], $a['tanggalpinjam']);
        });

        return $totalsemuapeminjaman;
    }

    public function getAllPeminjamanforAccAlat()
    {
        $peminjaman = Peminjaman::all();
        $totalsemuapeminjaman = [];

        foreach ($peminjaman as $booking) {
            $keterangan = $booking->Keterangan;
            $peminjamanID = $booking->PeminjamanID;
            $nohp = $booking->No_HP;
            $peminjamID = $booking->PeminjamID;
            $tanggalpinjam = $booking->Tanggal_pinjam;
            $peminjam = Peminjam::where('PeminjamID', $peminjamID)->first();
            $namapeminjam = $peminjam->Nama;
            $userid = $peminjam->UserID;
            $user = User::where('UserID', $userid)->first();
            $rolepeminjam = $user->User_role;
            $datapinjamalat = Peminjaman_Alat_Bridge::where('PeminjamanID', $peminjamanID)->get();
            $alatbersama = []; //kalau pinjam alat lebih dari 1 dalam 1 form

            foreach ($datapinjamalat as $data) {
                $peminjamanalatid = $data->Peminjaman_Alat_ID;
                $alatid = $data->AlatID;
                $tanggalawal = $data->Tanggal_pakai_awal;
                $tanggalakhir = $data->Tanggal_pakai_akhir;
                $peminjamanid = $data->PeminjamanID;
                $dokumenID = $data->DokumenID;
                if ($dokumenID != null) {
                    $doc = Dokumen::where('DokumenID', $dokumenID)->first();
                    $path = $doc->Path;
                    $namadokumen = $doc->Nama_dokumen;
                } else {
                    $path = null;
                    $namadokumen = null;
                }
                $alat = Alat::where('AlatID', $alatid)->first();
                $namaalat = $alat->Nama;
                $jumlahPinjam = $data->Jumlah_pinjam;
                $keterangan = $data->Keterangan;
                $isPersonal = $data->Is_Personal;
                $isOrganisation = $data->Is_Organisation;
                $isEksternal = $data->Is_Eksternal;
                $persetujuan = Persetujuan::where('Peminjaman_Alat_ID', $peminjamanalatid)->first();
                $dekan = $persetujuan->Dekan_Approve ?? null;
                $wd2 = $persetujuan->WD2_Approve ?? null;
                $wd3 = $persetujuan->WD3_Approve ?? null;
                $kepala = $persetujuan->Kepala_Approve ?? null;
                $petugas = $persetujuan->Petugas_Approve ?? null;
                $koord = $persetujuan->Koordinator_Approve ?? null;
                $namastatus = 'Diproses';
                $another = [
                    'toolName' => $namaalat,
                    'toolAmount' => $jumlahPinjam,
                ];
                $alatbersama[] = $another;

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

                $histori = [];
                $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Alat_ID', $peminjamanalatid)->first();
                if ($statuspeminjaman != null) {
                    $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
                    $activitylog = Activity_Log::where('Status_PeminjamanID', $statuspeminjamanid)->get();
                    foreach ($activitylog as $log) {
                        $recordHistory = [
                            'Acc_by' => $log->Acc_by,
                            'Tanggal_acc' => $log->Tanggal_Acc,
                            'Namastatus' => $log->Nama_status,
                            'Catatan' => $log->Catatan
                        ];

                        $histori[] = $recordHistory;
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
                    'personal' => $isPersonal,
                    'eksternal' => $isEksternal,
                    'organisasi' => $isOrganisation,
                    'histori' => $histori,
                    'dekan' => $dekan,
                    'wd2' => $wd2,
                    'wd3' => $wd3,
                    'kepala' => $kepala,
                    'petugas' => $petugas,
                    'koordinator' => $koord,
                    'path' => $path,
                    'namadokumen' => $namadokumen,
                    'rolepeminjam' => $rolepeminjam,
                    'nohp' => $nohp,
                ];

                $totalsemuapeminjaman[] = $recordDataAlat;
            }
        }

        usort($totalsemuapeminjaman, function ($a, $b) {
            return strcmp($b['tanggalpinjam'], $a['tanggalpinjam']);
        });

        return $totalsemuapeminjaman;
    }

    public function getAllPeminjamanforAccRuanganDekan()
    {
        $peminjaman = Peminjaman::all();
        $totalsemuapeminjaman = [];

        foreach ($peminjaman as $booking) {
            $peminjamanID = $booking->PeminjamanID;
            $nohp = $booking->No_HP;
            $tanggalpinjam = $booking->Tanggal_pinjam;
            $peminjamID = $booking->PeminjamID;
            $peminjam = Peminjam::where('PeminjamID', $peminjamID)->first();
            $namapeminjam = $peminjam->Nama;
            $userid = $peminjam->UserID;
            $user = User::where('UserID', $userid)->first();
            $rolepeminjam = $user->User_role;
            $datapinjamruangan = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $peminjamanID)->get();

            foreach ($datapinjamruangan as $data) {
                $peminjamanruanganid = $data->Peminjaman_Ruangan_ID;
                $ruanganid = $data->RuanganID;
                $tanggalawal = $data->Tanggal_pakai_awal;
                $tanggalakhir = $data->Tanggal_pakai_akhir;
                $dokumenID = $data->DokumenID;
                if ($dokumenID != null) {
                    $doc = Dokumen::where('DokumenID', $dokumenID)->first();
                    $path = $doc->Path;
                    $namadokumen = $doc->Nama_dokumen;
                } else {
                    $path = null;
                    $namadokumen = null;
                }
                $cariroom = Ruangan::where('RuanganID', $ruanganid)->first();
                $cekalat = Peminjaman_Alat_Bridge::where('RuanganID', $ruanganid)
                    ->where('Tanggal_pakai_awal', $tanggalawal)
                    ->where('Tanggal_pakai_akhir', $tanggalakhir)
                    ->get();
                $kumpulanalat = [];
                foreach ($cekalat as $cek) {
                    $jumlahPinjam = $cek->Jumlah_pinjam;
                    $alatid = $cek->AlatID;
                    $dokumenid = $cek->DokumenID;
                    if ($dokumenid != null) {
                        $docx = Dokumen::where('DokumenID', $dokumenid)->first();
                        $path = $docx->Path;
                        $namadokumen = $docx->Nama_dokumen;
                    }
                    $alat = Alat::where('AlatID', $alatid)->first();
                    $namaalat = $alat->Nama;
                    $dataAlat = ['namaalat' => $namaalat, 'jumlahPinjam' => $jumlahPinjam, 'path' => $path, 'namadokumen' => $namadokumen];
                    $kumpulanalat[] = $dataAlat;
                }

                $namaruangan = $cariroom->Nama_ruangan;
                $peminjamanid = $data->PeminjamanID;
                $keterangan = $data->Keterangan;
                $isPersonal = $data->Is_Personal;
                $isOrganisation = $data->Is_Organisation;
                $isEksternal = $data->Is_Eksternal;
                $persetujuan = Persetujuan::where('Peminjaman_Ruangan_ID', $peminjamanruanganid)->first();
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

                $histori = [];
                $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Ruangan_ID', $peminjamanruanganid)->first();
                if ($statuspeminjaman != null) {
                    $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
                    $activitylog = Activity_Log::where('Status_PeminjamanID', $statuspeminjamanid)->get();
                    foreach ($activitylog as $log) {
                        $recordHistory = [
                            'Acc_by' => $log->Acc_by,
                            'Tanggal_acc' => $log->Tanggal_Acc,
                            'Namastatus' => $log->Nama_status,
                            'Catatan' => $log->Catatan
                        ];

                        $histori[] = $recordHistory;
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
                    'namapeminjam' => $namapeminjam,
                    'personal' => $isPersonal,
                    'eksternal' => $isEksternal,
                    'organisasi' => $isOrganisation,
                    'alat' => $kumpulanalat,
                    'histori' => $histori,
                    'dekan' => $dekan,
                    'wd2' => $wd2,
                    'wd3' => $wd3,
                    'kepala' => $kepala,
                    'petugas' => $petugas,
                    'koordinator' => $koord,
                    'path' => $path,
                    'namadokumen' => $namadokumen,
                    'rolepeminjam' => $rolepeminjam,
                    'nohp' => $nohp
                ];

                if ($isEksternal) {
                    $totalsemuapeminjaman[] = $recordDataRoom;
                }
            }
        }
        usort($totalsemuapeminjaman, function ($a, $b) {
            return strcmp($b['tanggalpinjam'], $a['tanggalpinjam']);
        });

        return $totalsemuapeminjaman;
    }

    public function getAllPeminjamanforAccRuanganWD2()
    {
        $user = User::all();
        $totalsemuapeminjaman = [];
        $semuadomain = [];
        $semuapeminjaman = [];
        foreach ($user as $data) {
            $email = $data->Email;
            $emailParts = explode("@", $email);
            $domain = $emailParts[1];

            if (strtolower($domain) !== "ti.ukdw.ac.id" && strtolower($domain) !== "si.ukdw.ac.id") {
                $semuadomain[] = $email;
                $peminjam = Peminjam::where('UserID', $data->UserID)->first();
                if ($peminjam != null) {
                    $peminjamID = $peminjam->PeminjamID;
                    $namapeminjam = $peminjam->Nama;
                    $userid = $peminjam->UserID;
                    $user = User::where('UserID', $userid)->first();
                    $rolepeminjam = $user->User_role;
                    $peminjaman = Peminjaman::where('PeminjamID', $peminjamID)->get();
                    foreach ($peminjaman as $booking) {
                        $peminjamanID = $booking->PeminjamanID;
                        $tanggalpinjam = $booking->Tanggal_pinjam;
                        $nohp = $booking->No_HP;
                        $peminjamanruangan = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $peminjamanID)->get();
                        foreach ($peminjamanruangan as $data) {
                            $peminjamanruanganid = $data->PeminjamanRuanganID;
                            $ruanganid = $data->RuanganID;
                            $tanggalawal = $data->Tanggal_pakai_awal;
                            $tanggalakhir = $data->Tanggal_pakai_akhir;
                            $dokumenID = $data->DokumenID;
                            if ($dokumenID != null) {
                                $doc = Dokumen::where('DokumenID', $dokumenID)->first();
                                $path = $doc->Path;
                                $namadokumen = $doc->Nama_dokumen;
                            } else {
                                $path = null;
                                $namadokumen = null;
                            }
                            $cariroom = Ruangan::where('RuanganID', $ruanganid)->first();
                            $cekalat = Peminjaman_Alat_Bridge::where('RuanganID', $ruanganid)
                                ->where('Tanggal_pakai_awal', $tanggalawal)
                                ->where('Tanggal_pakai_akhir', $tanggalakhir)
                                ->get();
                            $kumpulanalat = [];
                            foreach ($cekalat as $cek) {
                                $jumlahPinjam = $cek->Jumlah_pinjam;
                                $alatid = $cek->AlatID;
                                $dokumenid = $cek->DokumenID;
                                if ($dokumenid != null) {
                                    $docx = Dokumen::where('DokumenID', $dokumenid)->first();
                                    $path = $docx->Path;
                                    $namadokumen = $docx->Nama_dokumen;
                                } else {
                                    $path = null;
                                    $namadokumen = null;
                                }
                                $alat = Alat::where('AlatID', $alatid)->first();
                                $namaalat = $alat->Nama;
                                $dataAlat = ['namaalat' => $namaalat, 'jumlahPinjam' => $jumlahPinjam, 'path' => $path, 'namadokumen' => $namadokumen];
                                $kumpulanalat[] = $dataAlat;
                            }

                            $namaruangan = $cariroom->Nama_ruangan;
                            $peminjamanid = $data->PeminjamanID;
                            $keterangan = $data->Keterangan;
                            $isPersonal = $data->Is_Personal;
                            $isOrganisation = $data->Is_Organisation;
                            $isEksternal = $data->Is_Eksternal;
                            $persetujuan = Persetujuan::where('Peminjaman_Ruangan_ID', $peminjamanruanganid)->first();
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

                            $histori = [];
                            $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Ruangan_ID', $peminjamanruanganid)->first();
                            if ($statuspeminjaman != null) {
                                $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
                                $activitylog = Activity_Log::where('Status_PeminjamanID', $statuspeminjamanid)->get();
                                foreach ($activitylog as $log) {
                                    $recordHistory = [
                                        'Acc_by' => $log->Acc_by,
                                        'Tanggal_acc' => $log->Tanggal_Acc,
                                        'Namastatus' => $log->Nama_status,
                                        'Catatan' => $log->Catatan
                                    ];

                                    $histori[] = $recordHistory;
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
                                'namapeminjam' => $namapeminjam,
                                'personal' => $isPersonal,
                                'eksternal' => $isEksternal,
                                'organisasi' => $isOrganisation,
                                'email' => $email,
                                'alat' => $kumpulanalat,
                                'histori' => $histori,
                                'dekan' => $dekan,
                                'wd2' => $wd2,
                                'wd3' => $wd3,
                                'kepala' => $kepala,
                                'petugas' => $petugas,
                                'koordinator' => $koord,
                                'path' => $path,
                                'namadokumen' => $namadokumen,
                                'rolepeminjam' => $rolepeminjam,
                                'nohp' => $nohp
                            ];
                        }
                        $totalsemuapeminjaman[] = $recordDataRoom;
                    }
                }
            }
        }

        usort($totalsemuapeminjaman, function ($a, $b) {
            return strcmp($b['tanggalpinjam'], $a['tanggalpinjam']);
        });

        return $totalsemuapeminjaman;
    }

    public function getAllPeminjamanforAccRuanganWD3()
    {
        $peminjaman = Peminjaman::all();
        $totalsemuapeminjaman = [];

        foreach ($peminjaman as $booking) {
            $peminjamanID = $booking->PeminjamanID;
            $tanggalpinjam = $booking->Tanggal_pinjam;
            $nohp = $booking->No_HP;
            $peminjamID = $booking->PeminjamID;
            $peminjam = Peminjam::where('PeminjamID', $peminjamID)->first();
            $namapeminjam = $peminjam->Nama;
            $userid = $peminjam->UserID;
            $user = User::where('UserID', $userid)->first();
            $rolepeminjam = $user->User_role;
            $datapinjamruangan = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $peminjamanID)->get();

            foreach ($datapinjamruangan as $data) {
                $peminjamanruanganid = $data->Peminjaman_Ruangan_ID;
                $ruanganid = $data->RuanganID;
                $tanggalawal = $data->Tanggal_pakai_awal;
                $tanggalakhir = $data->Tanggal_pakai_akhir;
                $dokumenID = $data->DokumenID;
                if ($dokumenID != null) {
                    $doc = Dokumen::where('DokumenID', $dokumenID)->first();
                    $path = $doc->Path;
                    $namadokumen = $doc->Nama_dokumen;
                } else {
                    $path = null;
                    $namadokumen = null;
                }
                $cariroom = Ruangan::where('RuanganID', $ruanganid)->first();
                $cekalat = Peminjaman_Alat_Bridge::where('RuanganID', $ruanganid)
                    ->where('Tanggal_pakai_awal', $tanggalawal)
                    ->where('Tanggal_pakai_akhir', $tanggalakhir)
                    ->get();
                $kumpulanalat = [];
                foreach ($cekalat as $cek) {
                    $jumlahPinjam = $cek->Jumlah_pinjam;
                    $alatid = $cek->AlatID;
                    $dokumenid = $cek->DokumenID;
                    if ($dokumenid != null) {
                        $docx = Dokumen::where('DokumenID', $dokumenid)->first();
                        $path = $docx->Path;
                        $namadokumen = $docx->Nama_dokumen;
                    } else {
                        $path = null;
                        $namadokumen = null;
                    }
                    $alat = Alat::where('AlatID', $alatid)->first();
                    $namaalat = $alat->Nama;
                    $dataAlat = ['namaalat' => $namaalat, 'jumlahPinjam' => $jumlahPinjam, 'path' => $path, 'namadokumen' => $namadokumen];
                    $kumpulanalat[] = $dataAlat;
                }

                $namaruangan = $cariroom->Nama_ruangan;
                $peminjamanid = $data->PeminjamanID;
                $keterangan = $data->Keterangan;
                $isPersonal = $data->Is_Personal;
                $isOrganisation = $data->Is_Organisation;
                $isEksternal = $data->Is_Eksternal;
                $persetujuan = Persetujuan::where('Peminjaman_Ruangan_ID', $peminjamanruanganid)->first();
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

                $histori = [];
                $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Ruangan_ID', $peminjamanruanganid)->first();
                if ($statuspeminjaman != null) {
                    $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
                    $activitylog = Activity_Log::where('Status_PeminjamanID', $statuspeminjamanid)->get();
                    foreach ($activitylog as $log) {
                        $recordHistory = [
                            'Acc_by' => $log->Acc_by,
                            'Tanggal_acc' => $log->Tanggal_Acc,
                            'Namastatus' => $log->Nama_status,
                            'Catatan' => $log->Catatan
                        ];

                        $histori[] = $recordHistory;
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
                    'namapeminjam' => $namapeminjam,
                    'personal' => $isPersonal,
                    'eksternal' => $isEksternal,
                    'organisasi' => $isOrganisation,
                    'alat' => $kumpulanalat,
                    'histori' => $histori,
                    'dekan' => $dekan,
                    'wd2' => $wd2,
                    'wd3' => $wd3,
                    'kepala' => $kepala,
                    'petugas' => $petugas,
                    'koordinator' => $koord,
                    'path' => $path,
                    'namadokumen' => $namadokumen,
                    'rolepeminjam' => $rolepeminjam,
                    'nohp' => $nohp
                ];

                if ($isOrganisation) {
                    $totalsemuapeminjaman[] = $recordDataRoom;
                }
            }
        }
        usort($totalsemuapeminjaman, function ($a, $b) {
            return strcmp($b['tanggalpinjam'], $a['tanggalpinjam']);
        });

        return $totalsemuapeminjaman;
    }

    public function getAllPeminjamanforAccAlatDekan()
    {
        $peminjaman = Peminjaman::all();
        $totalsemuapeminjaman = [];

        foreach ($peminjaman as $booking) {
            $keterangan = $booking->Keterangan;
            $peminjamanID = $booking->PeminjamanID;
            $nohp = $booking->No_HP;
            $peminjamID = $booking->PeminjamID;
            $tanggalpinjam = $booking->Tanggal_pinjam;
            $peminjam = Peminjam::where('PeminjamID', $peminjamID)->first();
            $namapeminjam = $peminjam->Nama;
            $userid = $peminjam->UserID;
            $user = User::where('UserID', $userid)->first();
            $rolepeminjam = $user->User_role;
            $datapinjamalat = Peminjaman_Alat_Bridge::where('PeminjamanID', $peminjamanID)->get();

            foreach ($datapinjamalat as $data) {
                $peminjamanalatid = $data->Peminjaman_Alat_ID;
                $alatid = $data->AlatID;
                $tanggalawal = $data->Tanggal_pakai_awal;
                $tanggalakhir = $data->Tanggal_pakai_akhir;
                $peminjamanid = $data->PeminjamanID;
                $dokumenID = $data->DokumenID;
                if ($dokumenID != null) {
                    $doc = Dokumen::where('DokumenID', $dokumenID)->first();
                    $path = $doc->Path;
                    $namadokumen = $doc->Nama_dokumen;
                } else {
                    $path = null;
                    $namadokumen = null;
                }

                $alat = Alat::where('AlatID', $alatid)->first();
                $namaalat = $alat->Nama;
                $jumlahPinjam = $data->Jumlah_pinjam;
                $keterangan = $data->Keterangan;
                $isPersonal = $data->Is_Personal;
                $isOrganisation = $data->Is_Organisation;
                $isEksternal = $data->Is_Eksternal;
                $persetujuan = Persetujuan::where('Peminjaman_Alat_ID', $peminjamanalatid)->first();
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

                $histori = [];
                $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Alat_ID', $peminjamanalatid)->first();
                if ($statuspeminjaman != null) {
                    $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
                    $activitylog = Activity_Log::where('Status_PeminjamanID', $statuspeminjamanid)->get();
                    foreach ($activitylog as $log) {
                        $recordHistory = [
                            'Acc_by' => $log->Acc_by,
                            'Tanggal_acc' => $log->Tanggal_Acc,
                            'Namastatus' => $log->Nama_status,
                            'Catatan' => $log->Catatan
                        ];

                        $histori[] = $recordHistory;
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
                    'personal' => $isPersonal,
                    'eksternal' => $isEksternal,
                    'organisasi' => $isOrganisation,
                    'histori' => $histori,
                    'dekan' => $dekan,
                    'wd2' => $wd2,
                    'wd3' => $wd3,
                    'kepala' => $kepala,
                    'petugas' => $petugas,
                    'koordinator' => $koord,
                    'path' => $path,
                    'namadokumen' => $namadokumen,
                    'rolepeminjam' => $rolepeminjam,
                    'nohp' => $nohp
                ];

                if ($isEksternal) {
                    $totalsemuapeminjaman[] = $recordDataAlat;
                }
            }
        }

        usort($totalsemuapeminjaman, function ($a, $b) {
            return strcmp($b['tanggalpinjam'], $a['tanggalpinjam']);
        });

        return $totalsemuapeminjaman;
    }

    public function getAllPeminjamanforAccAlatWD3()
    {
        $peminjaman = Peminjaman::all();
        $totalsemuapeminjaman = [];

        foreach ($peminjaman as $booking) {
            $keterangan = $booking->Keterangan;
            $peminjamanID = $booking->PeminjamanID;
            $peminjamID = $booking->PeminjamID;
            $nohp = $booking->No_HP;
            $tanggalpinjam = $booking->Tanggal_pinjam;
            $peminjam = Peminjam::where('PeminjamID', $peminjamID)->first();
            $namapeminjam = $peminjam->Nama;
            $userid = $peminjam->UserID;
            $user = User::where('UserID', $userid)->first();
            $rolepeminjam = $user->User_role;
            $datapinjamalat = Peminjaman_Alat_Bridge::where('PeminjamanID', $peminjamanID)->get();

            foreach ($datapinjamalat as $data) {
                $peminjamanalatid = $data->Peminjaman_Alat_ID;
                $alatid = $data->AlatID;
                $tanggalawal = $data->Tanggal_pakai_awal;
                $tanggalakhir = $data->Tanggal_pakai_akhir;
                $peminjamanid = $data->PeminjamanID;
                $dokumenID = $data->DokumenID;
                if ($dokumenID != null) {
                    $doc = Dokumen::where('DokumenID', $dokumenID)->first();
                    $path = $doc->Path;
                    $namadokumen = $doc->Nama_dokumen;
                } else {
                    $path = null;
                    $namadokumen = null;
                }

                $alat = Alat::where('AlatID', $alatid)->first();
                $namaalat = $alat->Nama;
                $jumlahPinjam = $data->Jumlah_pinjam;
                $keterangan = $data->Keterangan;
                $isPersonal = $data->Is_Personal;
                $isOrganisation = $data->Is_Organisation;
                $isEksternal = $data->Is_Eksternal;
                $persetujuan = Persetujuan::where('Peminjaman_Alat_ID', $peminjamanalatid)->first();
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

                $histori = [];
                $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Alat_ID', $peminjamanalatid)->first();
                if ($statuspeminjaman != null) {
                    $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
                    $activitylog = Activity_Log::where('Status_PeminjamanID', $statuspeminjamanid)->get();
                    foreach ($activitylog as $log) {
                        $recordHistory = [
                            'Acc_by' => $log->Acc_by,
                            'Tanggal_acc' => $log->Tanggal_Acc,
                            'Namastatus' => $log->Nama_status,
                            'Catatan' => $log->Catatan
                        ];

                        $histori[] = $recordHistory;
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
                    'personal' => $isPersonal,
                    'eksternal' => $isEksternal,
                    'organisasi' => $isOrganisation,
                    'histori' => $histori,
                    'dekan' => $dekan,
                    'wd2' => $wd2,
                    'wd3' => $wd3,
                    'kepala' => $kepala,
                    'petugas' => $petugas,
                    'koordinator' => $koord,
                    'path' => $path,
                    'namadokumen' => $namadokumen,
                    'rolepeminjam' => $rolepeminjam,
                    'nohp' => $nohp
                ];

                if ($isOrganisation) {
                    $totalsemuapeminjaman[] = $recordDataAlat;
                }
            }
        }

        usort($totalsemuapeminjaman, function ($a, $b) {
            return strcmp($b['tanggalpinjam'], $a['tanggalpinjam']);
        });

        return $totalsemuapeminjaman;
    }

    public function getAllPeminjamanforAccAlatWD2()
    {
        $user = User::all();
        $totalsemuapeminjaman = [];
        $semuadomain = [];
        $semuapeminjaman = [];
        foreach ($user as $data) {
            $email = $data->Email;
            $emailParts = explode("@", $email);
            $domain = $emailParts[1];

            if (strtolower($domain) !== "ti.ukdw.ac.id" && strtolower($domain) !== "si.ukdw.ac.id") {
                $semuadomain[] = $email;
                $peminjam = Peminjam::where('UserID', $data->UserID)->first();
                if ($peminjam != null) {
                    $peminjamID = $peminjam->PeminjamID;
                    $namapeminjam = $peminjam->Nama;
                    $userid = $peminjam->UserID;
                    $user = User::where('UserID', $userid)->first();
                    $rolepeminjam = $user->User_role;
                    $peminjaman = Peminjaman::where('PeminjamID', $peminjamID)->get();
                    foreach ($peminjaman as $booking) {
                        $peminjamanID = $booking->PeminjamanID;
                        $tanggalpinjam = $booking->Tanggal_pinjam;
                        $nohp = $booking->No_HP;
                        $peminjamanalat = Peminjaman_Alat_Bridge::where('PeminjamanID', $peminjamanID)->get();
                        foreach ($peminjamanalat as $data) {
                            $peminjamanalatid = $data->Peminjaman_Alat_ID;
                            $alatid = $data->AlatID;
                            $tanggalawal = $data->Tanggal_pakai_awal;
                            $tanggalakhir = $data->Tanggal_pakai_akhir;
                            $dokumenID = $data->DokumenID;
                            if ($dokumenID != null) {
                                $doc = Dokumen::where('DokumenID', $dokumenID)->first();
                                $path = $doc->Path;
                                $namadokumen = $doc->Nama_dokumen;
                            } else {
                                $path = null;
                                $namadokumen = null;
                            }

                            $alat = Alat::where('AlatID', $alatid)->first();
                            $namaalat = $alat->Nama;
                            $jumlahPinjam = $data->Jumlah_pinjam;
                            $keterangan = $data->Keterangan;
                            $isPersonal = $data->Is_Personal;
                            $isOrganisation = $data->Is_Organisation;
                            $isEksternal = $data->Is_Eksternal;
                            $persetujuan = Persetujuan::where('Peminjaman_Alat_ID', $peminjamanalatid)->first();
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

                            $histori = [];
                            $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Alat_ID', $peminjamanalatid)->first();
                            if ($statuspeminjaman != null) {
                                $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
                                $activitylog = Activity_Log::where('Status_PeminjamanID', $statuspeminjamanid)->get();
                                foreach ($activitylog as $log) {
                                    $recordHistory = [
                                        'Acc_by' => $log->Acc_by,
                                        'Tanggal_acc' => $log->Tanggal_Acc,
                                        'Namastatus' => $log->Nama_status,
                                        'Catatan' => $log->Catatan
                                    ];

                                    $histori[] = $recordHistory;
                                }
                            }

                            $recordDataAlat = [
                                'peminjamanalatid' => $peminjamanalatid,
                                'peminjamanid' => $peminjamanID,
                                'tanggalawal' => $tanggalawal,
                                'tanggalakhir' => $tanggalakhir,
                                'keterangan' => $keterangan,
                                'namaalat' => $namaalat,
                                'status' => $namastatus,
                                'jumlahPinjam' => $jumlahPinjam,
                                'namapeminjam' => $namapeminjam,
                                'tanggalpinjam' => $tanggalpinjam,
                                'personal' => $isPersonal,
                                'eksternal' => $isEksternal,
                                'organisasi' => $isOrganisation,
                                'email' => $email,
                                'histori' => $histori,
                                'dekan' => $dekan,
                                'wd2' => $wd2,
                                'wd3' => $wd3,
                                'kepala' => $kepala,
                                'petugas' => $petugas,
                                'koordinator' => $koord,
                                'path' => $path,
                                'namadokumen' => $namadokumen,
                                'rolepeminjam' => $rolepeminjam,
                                'nohp' => $nohp
                            ];

                            if ($isOrganisation) {
                                $totalsemuapeminjaman[] = $recordDataAlat;
                            }
                        }
                    }

                    usort($totalsemuapeminjaman, function ($a, $b) {
                        return strcmp($b['tanggalpinjam'], $a['tanggalpinjam']);
                    });

                    return $totalsemuapeminjaman;
                }
            }
        }
    }

    public function generatePDF($UserID, $desiredPeminjamanID, $peminjamanruanganid)
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

        if ($PeminjamanRuangan !== null) {
            $ruanganid = $PeminjamanRuangan->RuanganID;
            $ruangan = Ruangan::find($ruanganid);
            $namaruangan = $ruangan ? $ruangan->Nama_ruangan : '';
            $tanggalawal = $PeminjamanRuangan->Tanggal_pakai_awal;
            $tanggalakhir = $PeminjamanRuangan->Tanggal_pakai_akhir;
            $keterangan = $PeminjamanRuangan->Keterangan;

            $recordDataRuangan = [
                'PeminjamanID' => $desiredPeminjamanID,
                'namaruangan' => $namaruangan,
                'keterangan' => $keterangan,
                'tanggalawal' => $tanggalawal,
                'tanggalakhir' => $tanggalakhir,
            ];

            $recordDataAlat = [];

            $peminjamanalat = Peminjaman_Alat_Bridge::where('RuanganID', $ruanganid)
                ->where('Tanggal_pakai_awal', $tanggalawal)
                ->where('Tanggal_pakai_akhir', $tanggalakhir)
                ->get();

            if ($peminjamanalat != null) {
                foreach ($peminjamanalat as $alat) {
                    $alatid = $alat->AlatID;
                    $alatt = Alat::find($alatid);
                    $namalat = $alatt ? $alatt->Nama : '';
                    $jumlahPinjam = $alat->Jumlah_pinjam;
                    $tanggalawal = $alat->Tanggal_pakai_awal;
                    $tanggalakhir = $alat->Tanggal_pakai_akhir;
                    $keterangan = $alat->Keterangan;

                    $recordDataAlat[] = [
                        'namaalat' => $namalat,
                        'jumlahPinjam' => $jumlahPinjam,
                        'tanggalawal' => $tanggalawal,
                        'tanggalakhir' => $tanggalakhir,
                        'keterangan' => $keterangan
                    ];
                }
            }
        }

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

        return $data;
    }

    //ambil data peminjaman ruangan dan alat untuk kalender 
    public function getDataforCalender()
    {
        $peminjamanruangan = [];
        $peminjamanalat = [];

        $dataRoomsBook = Peminjaman_Ruangan_Bridge::all();
        $dataToolsBook = Peminjaman::all();

        foreach ($dataRoomsBook as $room) {
            $id = $room->Peminjaman_Ruangan_ID;
            $roomid = $room->RuanganID;
            $roomss = Ruangan::find($roomid);
            $namaruangan = $roomss->Nama_ruangan;
            $namaruangan_kecil = strtolower($namaruangan);
            $persetujuan = Persetujuan::where('Peminjaman_Ruangan_ID', $id)->first();
            if ($persetujuan) {
                $petugas = $persetujuan->Petugas_Approve;
                $koordinator = $persetujuan->Koordinator_Approve;
                $kepala = $persetujuan->Kepala_Approve;
                $wd2 = $persetujuan->WD2_Approve;
                $wd3 = $persetujuan->WD3_Approve;
                $dekan = $persetujuan->Dekan_Approve;

                if ($petugas === true && $koordinator === true && $kepala === true && $wd2 === true) {
                    $tanggalawal = $room->Tanggal_pakai_awal;
                    $tanggalakhir = $room->Tanggal_pakai_akhir;

                    $datetimeawal = new DateTime($tanggalawal);
                    $datetimeakhir = new DateTime($tanggalakhir);

                    $tanggalawal_baru = $datetimeawal->format('Y-m-d H:i');
                    $tanggalakhir_baru = $datetimeakhir->format('Y-m-d H:i');

                    $record = [
                        'id' => $room->Peminjaman_Ruangan_ID,
                        'title' => $room->Keterangan,
                        'start' => $tanggalawal_baru,
                        'end' => $tanggalakhir_baru,
                        'desc' => $namaruangan,
                        'calendarId' => $namaruangan_kecil
                    ];

                    $peminjamanruangan[] = $record;
                } elseif ($petugas === true && $koordinator === true && $kepala === true && $wd3 === true) {
                    $tanggalawal = $room->Tanggal_pakai_awal;
                    $tanggalakhir = $room->Tanggal_pakai_akhir;

                    $datetimeawal = new DateTime($tanggalawal);
                    $datetimeakhir = new DateTime($tanggalakhir);

                    $tanggalawal_baru = $datetimeawal->format('Y-m-d H:i');
                    $tanggalakhir_baru = $datetimeakhir->format('Y-m-d H:i');

                    $record = [
                        'id' => $room->Peminjaman_Ruangan_ID,
                        'title' => $room->Keterangan,
                        'start' => $tanggalawal_baru,
                        'end' => $tanggalakhir_baru,
                        'desc' => $namaruangan,
                        'calendarId' => $namaruangan_kecil
                    ];

                    $peminjamanruangan[] = $record;
                } elseif ($petugas === true && $koordinator === true && $kepala === true && $dekan === true) {
                    $tanggalawal = $room->Tanggal_pakai_awal;
                    $tanggalakhir = $room->Tanggal_pakai_akhir;

                    $datetimeawal = new DateTime($tanggalawal);
                    $datetimeakhir = new DateTime($tanggalakhir);

                    $tanggalawal_baru = $datetimeawal->format('Y-m-d H:i');
                    $tanggalakhir_baru = $datetimeakhir->format('Y-m-d H:i');

                    $record = [
                        'id' => $room->Peminjaman_Ruangan_ID,
                        'title' => $room->Keterangan,
                        'start' => $tanggalawal_baru,
                        'end' => $tanggalakhir_baru,
                        'desc' => $namaruangan,
                        'calendarId' => $namaruangan_kecil
                    ];

                    $peminjamanruangan[] = $record;
                } elseif ($petugas === true && $koordinator === true && $kepala === true) {
                    $tanggalawal = $room->Tanggal_pakai_awal;
                    $tanggalakhir = $room->Tanggal_pakai_akhir;

                    $datetimeawal = new DateTime($tanggalawal);
                    $datetimeakhir = new DateTime($tanggalakhir);

                    $tanggalawal_baru = $datetimeawal->format('Y-m-d H:i');
                    $tanggalakhir_baru = $datetimeakhir->format('Y-m-d H:i');

                    $record = [
                        'id' => $room->Peminjaman_Ruangan_ID,
                        'title' => $room->Keterangan,
                        'start' => $tanggalawal_baru,
                        'end' => $tanggalakhir_baru,
                        'desc' => $namaruangan,
                        'calendarId' => $namaruangan_kecil
                    ];

                    $peminjamanruangan[] = $record;
                } elseif ($petugas === true) {
                    $tanggalawal = $room->Tanggal_pakai_awal;
                    $tanggalakhir = $room->Tanggal_pakai_akhir;

                    $datetimeawal = new DateTime($tanggalawal);
                    $datetimeakhir = new DateTime($tanggalakhir);

                    $tanggalawal_baru = $datetimeawal->format('Y-m-d H:i');
                    $tanggalakhir_baru = $datetimeakhir->format('Y-m-d H:i');

                    $record = [
                        'id' => $room->Peminjaman_Ruangan_ID,
                        'title' => $room->Keterangan,
                        'start' => $tanggalawal_baru,
                        'end' => $tanggalakhir_baru,
                        'desc' => $namaruangan,
                        'calendarId' => $namaruangan_kecil
                    ];

                    $peminjamanruangan[] = $record;
                }
            }
        };

        foreach ($dataToolsBook as $tool) {
            $peminjamanid = $tool->PeminjamanID;
            $peminjamanALAT = Peminjaman_Alat_Bridge::where('PeminjamanID', $peminjamanid)->get();
            $peminjamanALAT2 = Peminjaman_Alat_Bridge::where('PeminjamanID', $peminjamanid)->first();

            if ($peminjamanALAT2 !== null) {
                $tanggalawal = $peminjamanALAT2->Tanggal_pakai_awal;
                $tanggalakhir = $peminjamanALAT2->Tanggal_pakai_akhir;
                $alatid2 = $peminjamanALAT2->AlatID;
                $namaalat2 = Alat::where('AlatID', $alatid2)->first();
                $namaAlat2 = $namaalat2->Nama;
                $jumlahPinjam2 = $peminjamanALAT2->Jumlah_pinjam;

                $datetimeawal = new DateTime($tanggalawal);
                $datetimeakhir = new DateTime($tanggalakhir);

                $tanggalawal_baru = $datetimeawal->format('Y-m-d H:i');
                $tanggalakhir_baru = $datetimeakhir->format('Y-m-d H:i');

                $persetujuan2 = Persetujuan::where('Peminjaman_Alat_ID', $peminjamanALAT2->Peminjaman_Alat_ID)->first();
                if ($persetujuan2) {
                    $petugas2 = $persetujuan2->Petugas_Approve;
                    $koordinator2 = $persetujuan2->Koordinator_Approve;
                    $kepala2 = $persetujuan2->Kepala_Approve;
                    $wd22 = $persetujuan2->WD2_Approve;
                    $wd32 = $persetujuan2->WD3_Approve;
                    $dekan2 = $persetujuan2->Dekan_Approve;

                    if ($petugas2 === true && $koordinator2 === true && $kepala2 === true && $wd22 === true) {
                        $daftarAlat = [];

                        if (count($peminjamanALAT) > 1) {
                            foreach ($peminjamanALAT as $peralat) {
                                $id = $peralat->Peminjaman_Alat_ID;
                                $jumlahPinjam = $peralat->Jumlah_pinjam;
                                $toolss = $peralat->AlatID;
                                $alat = Alat::where('AlatID', $toolss)->first();
                                $namaalat = $alat->Nama;

                                $record = [
                                    'namaalat' => $namaalat,
                                    'jumlahPinjam' => $jumlahPinjam,
                                    'peminjamanid' => $peminjamanid
                                ];

                                $daftarAlat[] = $record;
                            };

                            $collection = collect($daftarAlat);
                            $desc = $collection->map(function ($item) {
                                return $item['namaalat'] . ' : ' . $item['jumlahPinjam'];
                            })->implode(', ');

                            $record = [
                                'id' => $peminjamanALAT2->Peminjaman_Alat_ID,
                                'title' => $peminjamanALAT2->Keterangan,
                                'start' => $tanggalawal_baru,
                                'end' => $tanggalakhir_baru,
                                'desc' => $desc,
                                'calendarId' => 'alat'
                            ];

                            $peminjamanalat[] = $record;
                        } else {
                            $desc = $namaAlat2 . ' : ' . $jumlahPinjam2;
                            $record = [
                                'id' => $peminjamanALAT2->Peminjaman_Alat_ID,
                                'title' => $peminjamanALAT2->Keterangan,
                                'start' => $tanggalawal_baru,
                                'end' => $tanggalakhir_baru,
                                'desc' => $desc,
                                'peminjamanid' => $peminjamanid,
                                'calendarId' => 'alat'
                            ];

                            $peminjamanalat[] = $record;
                        }
                    } elseif ($petugas2 === true && $koordinator2 === true && $kepala2 === true && $dekan2 === true) {
                        $daftarAlat = [];

                        if (count($peminjamanALAT) > 1) {
                            foreach ($peminjamanALAT as $peralat) {
                                $id = $peralat->Peminjaman_Alat_ID;
                                $jumlahPinjam = $peralat->Jumlah_pinjam;
                                $toolss = $peralat->AlatID;
                                $alat = Alat::where('AlatID', $toolss)->first();
                                $namaalat = $alat->Nama;

                                $petugas = $persetujuan->Petugas_Approve;
                                $koordinator = $persetujuan->Koordinator_Approve;
                                $kepala = $persetujuan->Kepala_Approve;
                                $wd2 = $persetujuan->WD2_Approve;
                                $wd3 = $persetujuan->WD3_Approve;
                                $dekan = $persetujuan->Dekan_Approve;

                                $record = [
                                    'namaalat' => $namaalat,
                                    'jumlahPinjam' => $jumlahPinjam,
                                    'peminjamanid' => $peminjamanid
                                ];

                                $daftarAlat[] = $record;
                            };

                            $collection = collect($daftarAlat);
                            $desc = $collection->map(function ($item) {
                                return $item['namaalat'] . ' : ' . $item['jumlahPinjam'];
                            })->implode(', ');

                            $record = [
                                'id' => $peminjamanALAT2->Peminjaman_Alat_ID,
                                'title' => $peminjamanALAT2->Keterangan,
                                'start' => $tanggalawal_baru,
                                'end' => $tanggalakhir_baru,
                                'desc' => $desc,
                                'calendarId' => 'alat'
                            ];

                            $peminjamanalat[] = $record;
                        } else {
                            $desc = $namaAlat2 . ' : ' . $jumlahPinjam2;
                            $record = [
                                'id' => $peminjamanALAT2->Peminjaman_Alat_ID,
                                'title' => $peminjamanALAT2->Keterangan,
                                'start' => $tanggalawal_baru,
                                'end' => $tanggalakhir_baru,
                                'desc' => $desc,
                                'peminjamanid' => $peminjamanid,
                                'calendarId' => 'alat'
                            ];

                            $peminjamanalat[] = $record;
                        }
                    } elseif ($petugas2 === true && $koordinator2 === true && $kepala2 === true && $wd32 === true) {
                        $daftarAlat = [];

                        if (count($peminjamanALAT) > 1) {
                            foreach ($peminjamanALAT as $peralat) {
                                $id = $peralat->Peminjaman_Alat_ID;
                                $jumlahPinjam = $peralat->Jumlah_pinjam;
                                $toolss = $peralat->AlatID;
                                $alat = Alat::where('AlatID', $toolss)->first();
                                $namaalat = $alat->Nama;

                                $petugas = $persetujuan->Petugas_Approve;
                                $koordinator = $persetujuan->Koordinator_Approve;
                                $kepala = $persetujuan->Kepala_Approve;
                                $wd2 = $persetujuan->WD2_Approve;
                                $wd3 = $persetujuan->WD3_Approve;
                                $dekan = $persetujuan->Dekan_Approve;

                                $record = [
                                    'namaalat' => $namaalat,
                                    'jumlahPinjam' => $jumlahPinjam,
                                    'peminjamanid' => $peminjamanid
                                ];

                                $daftarAlat[] = $record;
                            };

                            $collection = collect($daftarAlat);
                            $desc = $collection->map(function ($item) {
                                return $item['namaalat'] . ' : ' . $item['jumlahPinjam'];
                            })->implode(', ');

                            $record = [
                                'id' => $peminjamanALAT2->Peminjaman_Alat_ID,
                                'title' => $peminjamanALAT2->Keterangan,
                                'start' => $tanggalawal_baru,
                                'end' => $tanggalakhir_baru,
                                'desc' => $desc,
                                'calendarId' => 'alat'
                            ];

                            $peminjamanalat[] = $record;
                        } else {
                            $desc = $namaAlat2 . ' : ' . $jumlahPinjam2;
                            $record = [
                                'id' => $peminjamanALAT2->Peminjaman_Alat_ID,
                                'title' => $peminjamanALAT2->Keterangan,
                                'start' => $tanggalawal_baru,
                                'end' => $tanggalakhir_baru,
                                'desc' => $desc,
                                'peminjamanid' => $peminjamanid,
                                'calendarId' => 'alat'
                            ];

                            $peminjamanalat[] = $record;
                        }
                    } elseif ($petugas2 === true && $koordinator2 === true && $kepala2 === true) {
                        $daftarAlat = [];

                        if (count($peminjamanALAT) > 1) {
                            foreach ($peminjamanALAT as $peralat) {
                                $id = $peralat->Peminjaman_Alat_ID;
                                $jumlahPinjam = $peralat->Jumlah_pinjam;
                                $toolss = $peralat->AlatID;
                                $alat = Alat::where('AlatID', $toolss)->first();
                                $namaalat = $alat->Nama;

                                $petugas = $persetujuan->Petugas_Approve;
                                $koordinator = $persetujuan->Koordinator_Approve;
                                $kepala = $persetujuan->Kepala_Approve;
                                $wd2 = $persetujuan->WD2_Approve;
                                $wd3 = $persetujuan->WD3_Approve;
                                $dekan = $persetujuan->Dekan_Approve;

                                $record = [
                                    'namaalat' => $namaalat,
                                    'jumlahPinjam' => $jumlahPinjam,
                                    'peminjamanid' => $peminjamanid
                                ];

                                $daftarAlat[] = $record;
                            };

                            $collection = collect($daftarAlat);
                            $desc = $collection->map(function ($item) {
                                return $item['namaalat'] . ' : ' . $item['jumlahPinjam'];
                            })->implode(', ');

                            $record = [
                                'id' => $peminjamanALAT2->Peminjaman_Alat_ID,
                                'title' => $peminjamanALAT2->Keterangan,
                                'start' => $tanggalawal_baru,
                                'end' => $tanggalakhir_baru,
                                'desc' => $desc,
                                'calendarId' => 'alat'
                            ];

                            $peminjamanalat[] = $record;
                        } else {
                            $desc = $namaAlat2 . ' : ' . $jumlahPinjam2;
                            $record = [
                                'id' => $peminjamanALAT2->Peminjaman_Alat_ID,
                                'title' => $peminjamanALAT2->Keterangan,
                                'start' => $tanggalawal_baru,
                                'end' => $tanggalakhir_baru,
                                'desc' => $desc,
                                'peminjamanid' => $peminjamanid,
                                'calendarId' => 'alat'
                            ];

                            $peminjamanalat[] = $record;
                        }
                    } elseif ($petugas2 === true) {
                        $daftarAlat = [];

                        if (count($peminjamanALAT) > 1) {
                            foreach ($peminjamanALAT as $peralat) {
                                $id = $peralat->Peminjaman_Alat_ID;
                                $jumlahPinjam = $peralat->Jumlah_pinjam;
                                $toolss = $peralat->AlatID;
                                $alat = Alat::where('AlatID', $toolss)->first();
                                $namaalat = $alat->Nama;

                                $petugas = $persetujuan->Petugas_Approve;
                                $koordinator = $persetujuan->Koordinator_Approve;
                                $kepala = $persetujuan->Kepala_Approve;
                                $wd2 = $persetujuan->WD2_Approve;
                                $wd3 = $persetujuan->WD3_Approve;
                                $dekan = $persetujuan->Dekan_Approve;

                                $record = [
                                    'namaalat' => $namaalat,
                                    'jumlahPinjam' => $jumlahPinjam,
                                    'peminjamanid' => $peminjamanid
                                ];

                                $daftarAlat[] = $record;
                            };

                            $collection = collect($daftarAlat);
                            $desc = $collection->map(function ($item) {
                                return $item['namaalat'] . ' : ' . $item['jumlahPinjam'];
                            })->implode(', ');

                            $record = [
                                'id' => $peminjamanALAT2->Peminjaman_Alat_ID,
                                'title' => $peminjamanALAT2->Keterangan,
                                'start' => $tanggalawal_baru,
                                'end' => $tanggalakhir_baru,
                                'desc' => $desc,
                                'calendarId' => 'alat'
                            ];

                            $peminjamanalat[] = $record;
                        } else {
                            $desc = $namaAlat2 . ' : ' . $jumlahPinjam2;
                            $record = [
                                'id' => $peminjamanALAT2->Peminjaman_Alat_ID,
                                'title' => $peminjamanALAT2->Keterangan,
                                'start' => $tanggalawal_baru,
                                'end' => $tanggalakhir_baru,
                                'desc' => $desc,
                                'peminjamanid' => $peminjamanid,
                                'calendarId' => 'alat'
                            ];

                            $peminjamanalat[] = $record;
                        }
                    }
                }
            };
        }

        $allData = [
            'peminjamanruangan' => $peminjamanruangan,
            'peminjamanalat' => $peminjamanalat
        ];

        return $allData;
    }

    // cek jadwal tabrakan untuk dosen
    public function jadwalPeminjamanforDosen($Tanggal_pakai_awal, $Tanggal_pakai_akhir)
    {
        //$statuspeminjaman = Status_Peminjaman::whereNot('StatusID', 7)->get();
        /* $peminjamanruangan = Peminjaman_Ruangan_Bridge::orWhere(function ($query) {
            $query->where('Prioritas', 1)->whereNotNull('DokumenID');
        })
            ->orWhere(function ($query) {
                $query->where('Prioritas', 2);
            })->where('Tanggal_pakai_awal', "<=", $Tanggal_pakai_awal)
            ->where('Tanggal_pakai_akhir', ">=", $Tanggal_pakai_akhir)
            ->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '>', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '<=', $Tanggal_pakai_akhir);
            })->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '>', $Tanggal_pakai_akhir);
            })
            ->pluck('RuanganID')->unique(); */
        $peminjamanruanganpertama = Peminjaman_Ruangan_Bridge::where('Prioritas', 2)
            ->where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_akhir)
            ->where('Tanggal_pakai_akhir', '>=', $Tanggal_pakai_awal)
            ->pluck('RuanganID')->unique();

        $peminjamanruanganlagi = Peminjaman_Ruangan_Bridge::where('Prioritas', 1)->whereNotNull('DokumenID')
            ->where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_akhir)
            ->where('Tanggal_pakai_akhir', '>=', $Tanggal_pakai_awal)
            ->pluck('RuanganID')->unique();

        $peminjamanruangan = $peminjamanruanganpertama->merge($peminjamanruanganlagi);

        //return response()->json([$peminjamanruangan, $peminjamanruanganpertama, $peminjamanruanganlagi]);

        $dataruangan = Ruangan::pluck('RuanganID', 'Nama_ruangan');
        $ruangan = $dataruangan->diff($peminjamanruangan);

        $room = $ruangan->toArray();
        $array = array_keys($room);
        $detailRoom = [];

        foreach ($array as $availableRoom) {
            $ambildata = Ruangan::where('Nama_ruangan', $availableRoom)->first();
            $detailRoom[] = $ambildata;
        }

        $peminjamanruangan2 = Peminjaman_Ruangan_Bridge::where('Tanggal_pakai_awal', "<=", $Tanggal_pakai_awal)
            ->where('Tanggal_pakai_akhir', ">=", $Tanggal_pakai_akhir)
            ->where('Prioritas', 1)
            ->where('DokumenID', null)
            ->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '>', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '<=', $Tanggal_pakai_akhir);
            })->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '>', $Tanggal_pakai_akhir);
            })
            ->get();

        $datatabrak = [];
        foreach ($peminjamanruangan2 as $record) {
            $ruanganid = $record->RuanganID;
            $ruangan = Ruangan::where('RuanganID', $ruanganid)->first();
            $namaruangan = $ruangan->Nama_ruangan;
            $data = [
                "Peminjaman_Ruangan_ID" => $record->Peminjaman_Ruangan_ID,
                "PeminjamanID" => $record->PeminjamanID,
                "RuanganID" => $record->RuanganID,
                "Tanggal_pakai_awal" => $record->Tanggal_pakai_awal,
                "Tanggal_pakai_akhir" => $record->Tanggal_pakai_akhir,
                "DokumenID" => $record->DokumenID,
                "Keterangan" => $record->Keterangan,
                "Is_Personal" => $record->Is_Personal,
                "Is_Organisation" => $record->Is_Organisation,
                "Is_Eksternal" => $record->Is_Eksternal,
                "Nama_ruangan" => $namaruangan
            ];
            $datatabrak[] = $data;
        }

        usort($detailRoom, function ($a, $b) {
            return strcmp($a['Nama_ruangan'], $b['Nama_ruangan']);
        });

        return response()->json(['availableRoom' => $array, 'detailRuangan' => $detailRoom, 'datatabrak' => $datatabrak, 'peminjamanruangan' => $peminjamanruangan]);
    }

    // cek jadwal tabrakan untuk rekomendasi ruangan
    public function jadwalPeminjamanforRekomendasiDosen($Tanggal_pakai_awal, $Tanggal_pakai_akhir, $Kapasitas, $Kategori, $Lokasi)
    {
        /* $peminjamanruangan = Peminjaman_Ruangan_Bridge::orWhere(function ($query) {
            $query->where('Prioritas', 1)->whereNotNull('DokumenID');
        })
            ->orWhere(function ($query) {
                $query->where('Prioritas', 2);
            })->where('Tanggal_pakai_awal', "<=", $Tanggal_pakai_awal)
            ->where('Tanggal_pakai_akhir', ">=", $Tanggal_pakai_akhir)
            ->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '>', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '<=', $Tanggal_pakai_akhir);
            })->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '>', $Tanggal_pakai_akhir);
            })
            ->pluck('RuanganID')->unique(); */

        $peminjamanruanganpertama = Peminjaman_Ruangan_Bridge::where('Prioritas', 2)
            ->where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_akhir)
            ->where('Tanggal_pakai_akhir', '>=', $Tanggal_pakai_awal)
            ->pluck('RuanganID')->unique();

        $peminjamanruanganlagi = Peminjaman_Ruangan_Bridge::where('Prioritas', 1)->whereNotNull('DokumenID')
            ->where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_akhir)
            ->where('Tanggal_pakai_akhir', '>=', $Tanggal_pakai_awal)
            ->pluck('RuanganID')->unique();

        $peminjamanruangan = $peminjamanruanganpertama->merge($peminjamanruanganlagi);

        $dataruangan = Ruangan::pluck('RuanganID', 'Nama_ruangan');
        $ruangan = $dataruangan->diff($peminjamanruangan);

        $room = $ruangan->toArray();
        $array = array_keys($room);
        $detailRoom = [];

        foreach ($array as $availableRoom) {
            $ambildata = Ruangan::where('Nama_ruangan', $availableRoom)->first();
            $gambar = explode(':', $ambildata->Foto);
            $ambildata->Foto = $gambar;
            $detailRoom[] = $ambildata;
        }

        $filteredDetailRoom = array_filter($detailRoom, function ($room) use ($Lokasi, $Kategori, $Kapasitas) {
            return $room->Lokasi === $Lokasi &&
                $room->Kategori === $Kategori &&
                $room->Kapasitas === $Kapasitas;
        });

        $peminjamanruangan2 = Peminjaman_Ruangan_Bridge::where('Tanggal_pakai_awal', "<=", $Tanggal_pakai_awal)
            ->where('Tanggal_pakai_akhir', ">=", $Tanggal_pakai_akhir)
            ->where('Prioritas', 1)
            ->where('DokumenID', null)
            ->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '>', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '<=', $Tanggal_pakai_akhir);
            })->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '>', $Tanggal_pakai_akhir);
            })
            ->get();

        $datatabrak = [];
        foreach ($peminjamanruangan2 as $record) {
            $ruanganid = $record->RuanganID;
            $ruangan = Ruangan::where('RuanganID', $ruanganid)->first();
            $namaruangan = $ruangan->Nama_ruangan;
            $data = [
                "Peminjaman_Ruangan_ID" => $record->Peminjaman_Ruangan_ID,
                "PeminjamanID" => $record->PeminjamanID,
                "RuanganID" => $record->RuanganID,
                "Tanggal_pakai_awal" => $record->Tanggal_pakai_awal,
                "Tanggal_pakai_akhir" => $record->Tanggal_pakai_akhir,
                "DokumenID" => $record->DokumenID,
                "Keterangan" => $record->Keterangan,
                "Is_Personal" => $record->Is_Personal,
                "Is_Organisation" => $record->Is_Organisation,
                "Is_Eksternal" => $record->Is_Eksternal,
                "Nama_ruangan" => $namaruangan
            ];
            $datatabrak[] = $data;
        }

        usort($detailRoom, function ($a, $b) {
            return strcmp($a['Nama_ruangan'], $b['Nama_ruangan']);
        });

        return response()->json(['availableRoom' => $array, 'detailRuangan' => $detailRoom, 'fixRoom' => $filteredDetailRoom, 'datatabrak' => $datatabrak]);
    }

    // cek jadwal tabrakan untuk rekomendasi 
    public function jadwalPeminjamanforRekomendasi($Tanggal_pakai_awal, $Tanggal_pakai_akhir, $Kapasitas, $Kategori, $Lokasi)
    {
        $peminjamanruangan = Peminjaman_Ruangan_Bridge::where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_akhir)
            ->where('Tanggal_pakai_akhir', '>=', $Tanggal_pakai_awal)->pluck('RuanganID')->unique();

        $dataruangan = Ruangan::pluck('RuanganID', 'Nama_ruangan');
        $ruangan = $dataruangan->diff($peminjamanruangan);

        $room = $ruangan->toArray();
        $array = array_keys($room);
        $detailRoom = [];

        foreach ($array as $availableRoom) {
            $ambildata = Ruangan::where('Nama_ruangan', $availableRoom)->first();
            $gambar = explode(':', $ambildata->Foto);
            $ambildata->Foto = $gambar;
            $detailRoom[] = $ambildata;
        }

        $filteredDetailRoom = array_filter($detailRoom, function ($room) use ($Lokasi, $Kategori, $Kapasitas) {
            return $room->Lokasi === $Lokasi &&
                $room->Kategori === $Kategori &&
                $room->Kapasitas === $Kapasitas;
        });

        usort($detailRoom, function ($a, $b) {
            return strcmp($a['Nama_ruangan'], $b['Nama_ruangan']);
        });

        return response()->json(['availableRoom' => $array, 'detailRuangan' => $detailRoom, 'fixRoom' => $filteredDetailRoom]);
    }

    // cancel peminjaman tanpa dokumen pendukung (kebutuhan personal)
    public function cancelPeminjaman($Peminjaman_Ruangan_ID)
    {
        $peminjamanruangan = Peminjaman_Ruangan_Bridge::where('Peminjaman_Ruangan_ID', $Peminjaman_Ruangan_ID)->first();
        $peminjamanid = $peminjamanruangan->PeminjamanID;
        $peminjaman = Peminjaman::where('PeminjamanID', $peminjamanid)->first();
        $peminjamid = $peminjaman->PeminjamID;
        $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
        $userid = $peminjam->UserID;
        $user = User::where('UserID', $userid)->first();
        $email = $user->Email;
        $ruanganid = $peminjamanruangan->RuanganID;
        $ruangan = Ruangan::where('RuanganID', $ruanganid)->first();
        $namaruangan = $ruangan->Nama_ruangan;
        $tanggalAwal = $peminjamanruangan->Tanggal_pakai_awal;
        $tanggalSelesai = $peminjamanruangan->Tanggal_pakai_akhir;

        $dataEmail = [
            'subject' => 'Pembatalan Peminjaman Ruangan',
            'Nama_ruangan' => $namaruangan,
            'tanggalAwal' => $tanggalAwal,
            'tanggalSelesai' => $tanggalSelesai
        ];

        Mail::to($email)->send(new CancelBooking($dataEmail));

        $title = 'Pembatalan Peminjaman Ruangan';
        $body = 'Mohon maaf, peminjaman ruangan ' . $namaruangan . ' pada ' . $tanggalAwal . ' sampai dengan ' . $tanggalSelesai . ' telah dibatalkan. Silahkan melakukan peminjamanruangan yang lain. Terima kasih.';

        $peminjam->notify(new NewMessage($title, $body));
        $peminjam->notify(new CancelNotification($dataEmail));

        /*         $peminjamanruangan->delete();
 */
        return response()->json(['message' => 'Peminjaman ruangan telah dibatalkan.']);
    }
}
