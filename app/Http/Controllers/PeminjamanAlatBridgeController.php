<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman_Alat_Bridge;
use App\Http\Requests\StorePeminjaman_Alat_BridgeRequest;
use App\Http\Requests\UpdatePeminjaman_Alat_BridgeRequest;
use App\Models\Activity_Log;
use App\Models\Alat;
use App\Models\Detail_Alat;
use App\Models\Peminjam;
use App\Models\Peminjaman;
use App\Models\Peminjaman_Ruangan_Bridge;
use App\Models\Persetujuan;
use App\Models\Status_Peminjaman;
use App\Models\User;
use App\Notifications\NewBookingNotification;
use App\Notifications\NewMessage;
use Illuminate\Support\Facades\DB;


class PeminjamanAlatBridgeController extends Controller
{
    // mengambil semua data pada db
    public function getAllPeminjamanAlat()
    {
        return Peminjaman_Alat_Bridge::all();
    }
    //ambil data sesuai id
    public function show($Peminjaman_Alat_ID)
    {
        return Peminjaman_Alat_Bridge::find($Peminjaman_Alat_ID);
    }
    //tambah data
    public function store(StorePeminjaman_Alat_BridgeRequest $request)
    {
        setlocale(LC_ALL, 'id_ID');
        $input = $request->all();
        //dd($input);
        $user = Peminjam::where('UserID', $request['UserID'])->first();
        $peminjamID = $user->PeminjamID;
        $USER = User::where('UserID', $request['UserID'])->first();
        $userrole = $USER->User_role;
        $email = $USER->Email;
        $prodiid = $user->ProdiID;
        $instansiid = $user->InstansiID;
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
        //dd($peminjaman);
        $peminjamanid = $peminjaman->PeminjamanID;
        $totalpinjamalat = [];
        $persetujuanAlat = [];
        $statuspeminjamanAlat = [];
        $detailalat = [];
        $detailruangan = [];

        if (count($input['alat']) > 0) {
            foreach ($input['alat'] as $tool) {
                if (!empty($tool['nama']) && $tool['jumlahPinjam'] > 0) {
                    $detail = Alat::where('Nama', $tool['nama'])->first();
                    $jumlahpinjam = $tool['jumlahPinjam'];
                    $alatID = $detail->AlatID;
                    $datanotif = [
                        'namaalat' => $tool['nama'],
                        'jumlahPinjam' => $tool['jumlahPinjam'],
                        'tanggalawal' => $input['tanggalAwal'],
                        'tanggalakhir' => $input['tanggalSelesai'],
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
                        'RuanganID' => null,
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
            'subject' => 'Peminjaman Alat Baru',
            'detailruangan' => $detailruangan,
            'detailalat' => $detailalat,
        ];

        $title = 'Peminjaman Alat Baru';

        if (count($detailalat) >  1) {
            $body = $detailalat[0]['namaalat'] . ' dan lainnya dipinjam. Segera berikan konfirmasi.';
        } else {
            $body = $detailalat[0]['namaalat'] . ' dipinjam. Segera berikan konfirmasi.';
        }

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
            'peminjaman_alat_bridge' => $totalpinjamalat,
            'persetujuan' => $persetujuanAlat,
            'statuspeminjamanalat' => $statuspeminjamanAlat,
            'notifikasi berhasil dikirim' => $dataNotifikasi
        ]);
    }
    //mengubah data semua row
    public function updateSemuaRow(UpdatePeminjaman_Alat_BridgeRequest $request, Peminjaman_Alat_Bridge $Peminjaman_Alat_ID)
    {
        $Peminjaman_Alat_ID->update($request->all());
        return response()->json(['message' => 'Peminjaman Alat berhasil diperbarui', 'data' => $Peminjaman_Alat_ID]);
    }
    //hapus data
    public function delete($Peminjaman_Alat_ID)
    {
        $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
        if ($statuspeminjaman != null) {
            $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
            $activitylog = Activity_Log::where('Status_PeminjamanID', $statuspeminjamanid)->first();

            if ($activitylog != null) {
                $activitylog->delete();
            }

            $statuspeminjaman->delete();
        }
        $persetujuan = Persetujuan::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
        $peminjamanalat = Peminjaman_Alat_Bridge::find($Peminjaman_Alat_ID);
        $peminjamanid = $peminjamanalat->PeminjamanID;
        $peminjaman = Peminjaman::where('PeminjamanID', $peminjamanid)->first();
        $peminjamid = $peminjaman->PeminjamID;

        if ($persetujuan != null) {
            $organisasi = $peminjamanalat->Is_Organisation;
            $personal = $peminjamanalat->Is_Personal;
            $eksternal = $peminjamanalat->Is_Eksternal;
            $tanggalpenggunaan = $peminjamanalat->Tanggal_pakai_awal;
            $tanggalbatal = date('d-m-Y');
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
                    $peminjam = Peminjam::where('PeminjamID', $peminjamanid)->first();
                    $peminjam->Total_batal + 1;
                    $peminjam->Tanggal_batal_terakhir->$tanggalbatal;
                    $peminjam->save();
                } elseif ($wd3 === true || $kepala === true || $koordinator === true || $petugas === true) {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamanid)->first();
                    $peminjam->Total_batal + 1;
                    $peminjam->Tanggal_batal_terakhir->$tanggalbatal;
                    $peminjam->save();
                } /* else {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamanid)->first();
                    $peminjam->Total_batal + 1;
                    $peminjam->Tanggal_batal_terakhir->$tanggalbatal;
                    $peminjam->save();
                } */
            } elseif ($eksternal === true) {
                if ($dekan === true && $kepala === true && $koordinator === true && $petugas === true) {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamanid)->first();
                    $peminjam->Total_batal + 1;
                    $peminjam->Tanggal_batal_terakhir->$tanggalbatal;
                    $peminjam->save();
                } elseif ($dekan === true || $kepala === true || $koordinator === true || $petugas === true) {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamanid)->first();
                    $peminjam->Total_batal + 1;
                    $peminjam->Tanggal_batal_terakhir->$tanggalbatal;
                    $peminjam->save();
                } /* else {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamanid)->first();
                    $peminjam->Total_batal + 1;
                    $peminjam->Tanggal_batal_terakhir->$tanggalbatal;
                    $peminjam->save();
                } */
            } elseif ($personal === true) {
                if ($petugas === true) {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamanid)->first();
                    $peminjam->Total_batal + 1;
                    $peminjam->Tanggal_batal_terakhir->$tanggalbatal;
                    $peminjam->save();
                } /* elseif ($petugas === false) {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamanid)->first();
                    $peminjam->Total_batal + 1;
                    $peminjam->Tanggal_batal_terakhir->$tanggalbatal;
                    $peminjam->save();
                }  *//* else {
                    $peminjam = Peminjam::where('PeminjamID', $peminjamanid)->first();
                    $peminjam->Total_batal + 1;
                    $peminjam->Tanggal_batal_terakhir->$tanggalbatal;
                    $peminjam->save();
                } */
            } else {
                $peminjam = Peminjam::where('PeminjamID', $peminjamanid)->first();
                $userid = $peminjam->UserID;
                $user = User::where('UserID', $userid)->first();
                $role = $user->User_role;
                if ($role === 'Staff' || $role === 'Dosen' || $role === 'Wakil Dekan 2' || $role === 'Wakil Dekan 3' || $role === 'Dekan' || $role === 'Kepala Lab' || $role === 'Koordinator Lab') {
                    if ($wd2 === true && $kepala === true && $koordinator === true && $petugas === true) {
                        $peminjam = Peminjam::where('PeminjamID', $peminjamanid)->first();
                        $peminjam->Total_batal + 1;
                        $peminjam->Tanggal_batal_terakhir->$tanggalbatal;
                        $peminjam->save();
                    } elseif ($wd2 === true || $kepala === true || $koordinator === true || $petugas === true) {
                        $peminjam = Peminjam::where('PeminjamID', $peminjamanid)->first();
                        $peminjam->Total_batal + 1;
                        $peminjam->Tanggal_batal_terakhir->$tanggalbatal;
                        $peminjam->save();
                    } /* else {
                        $peminjam = Peminjam::where('PeminjamID', $peminjamanid)->first();
                        $peminjam->Total_batal + 1;
                        $peminjam->Tanggal_batal_terakhir->$tanggalbatal;
                        $peminjam->save();
                    } */
                }
            }
            $persetujuan->delete();
        }

        $peminjamanalat->delete();
        return response()->json(['message' => 'peminjamanruangan berhasil dihapus'], 204);
    }

    //menghapus peminjaman berdasarkan peminjamanID
    public function deletePeminjamanAlat($PeminjamanID, $AlatID)
    {
        $peminjaman = Peminjaman_Alat_Bridge::where('PeminjamanID', $PeminjamanID)
            ->where('AlatID', $AlatID)
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
        $peminjaman = Peminjaman_Ruangan_Bridge::where('PeminjamanID', $PeminjamanID)->first();
        if ($peminjaman) {
            return response()->json(['relasi' => true]);
        } else {
            return response()->json(['relasi' => false]);
        }
    }

    public function getNameByID($AlatID)
    {
        $alat = Alat::where('AlatID', $AlatID)->first();
        if ($alat) {
            return response()->json(['Nama' => $alat->Nama]);
        } else {
            return response()->json(['Error' => 'Alat not found'], 404);
        }
    }

    //mengambil data berdasarkan peminjamanID
    public function getAllPeminjamanAlatByPeminjamanID($PeminjamanID)
    {
        $peminjaman = Peminjaman_Alat_Bridge::where('PeminjamanID', $PeminjamanID)->get();
        if ($peminjaman->isEmpty()) {
            return null;
        } else {
            return $peminjaman;
        }
    }

    //mengambil data-data peminjaman alat
    public function getPeminjamanAlat($UserID)
    {
        $peminjam = Peminjam::where('UserID', $UserID)->first();
        $peminjamID = $peminjam->PeminjamID;
        $peminjaman = Peminjaman::where('PeminjamID', $peminjamID)->get();
        $alltoolsbooking = [];

        foreach ($peminjaman as $booking) {
            $keterangan = $booking->Keterangan;
            $peminjamanID = $booking->PeminjamanID;
            $tanggalpinjam = $booking->Tanggal_pinjam;
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

                $recordData = [
                    'peminjamanalatid' => $peminjamanalatid,
                    'peminjamanid' => $peminjamanid,
                    'tanggalawal' => $tanggalawal,
                    'tanggalakhir' => $tanggalakhir,
                    'keterangan' => $keterangan,
                    'namaalat' => $namaalat,
                    'status' => $namastatus,
                    'jumlahPinjam' => $jumlahPinjam,
                    'histori' => $histori,
                    'tanggalpinjam' => $tanggalpinjam
                ];

                $alltoolsbooking[] = $recordData;
            }
        }

        usort($alltoolsbooking, function ($a, $b) {
            return strcmp($b['tanggalawal'], $a['tanggalawal']);
        });
        return $alltoolsbooking;
    }

    //cek jadwal tabrakan
    public function jadwalAlat($Tanggal_pakai_awal, $Tanggal_pakai_akhir)
    {
        /* $peminjamanalat = Peminjaman_Alat_Bridge::where('Tanggal_pakai_awal', "<=", $Tanggal_pakai_awal)
            ->where('Tanggal_pakai_akhir', ">=", $Tanggal_pakai_akhir)
            ->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '>', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '<=', $Tanggal_pakai_akhir);
            })->orWhere(function ($query) use ($Tanggal_pakai_awal, $Tanggal_pakai_akhir) {
                $query->where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_awal)
                    ->where('Tanggal_pakai_akhir', '>', $Tanggal_pakai_akhir);
            })->get(); */

        $peminjamanalat = Peminjaman_Alat_Bridge::where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_akhir)
            ->where('Tanggal_pakai_akhir', '>=', $Tanggal_pakai_awal)->get();

        $daftarAlatTabrakan = [];

        foreach ($peminjamanalat as $tool) {
            $alatid = $tool->AlatID;
            $jumlahpinjam = $tool->Jumlah_pinjam;
            $found = false;
            $ALAT = Alat::where('AlatID', $alatid)->first();
            $namaAlat = $ALAT->Nama;
            $surat = $ALAT->WajibSurat;
            $ketersediaan = $ALAT->Status;

            if ($ketersediaan === 'Tersedia') {
                foreach ($daftarAlatTabrakan as &$existingAlat) {
                    if ($existingAlat['AlatID'] === $alatid) {
                        $found = true;
                        $newtotal = $existingAlat['Jumlah_pinjam'] + $jumlahpinjam;
                        $existingAlat['Jumlah_pinjam'] = $newtotal;
                        break;
                    }
                }

                if ($found === false) {
                    $daftarAlatTabrakan[] = [
                        'AlatID' => $alatid,
                        'Jumlah_pinjam' => $jumlahpinjam,
                        'NamaAlat' => $namaAlat,
                        'WajibSurat' => $surat
                    ];
                }
            }
        }

        $allAlat = Alat::where('Status', 'Tersedia')->get();
        $daftarAlat = [];
        foreach ($allAlat as $alat) {
            $alatid = $alat->AlatID;
            $jumlah = $alat->Jumlah_ketersediaan;
            $nama = $alat->Nama;
            $daftarAlat[] = [
                'AlatID' => $alatid,
                'Jumlah_ketersediaan' => $jumlah,
                'NamaAlat' => $nama,
                'WajibSurat' => $alat->WajibSurat,
                'Status' => $alat->Status
            ];
        }

        $fixAlat = [];
        foreach ($daftarAlat as $tool1) {
            $fixAlat[] = [
                'AlatID' => $tool1['AlatID'],
                'Jumlah_ketersediaan' => $tool1['Jumlah_ketersediaan'],
                'NamaAlat' => $tool1['NamaAlat'],
                'WajibSurat' => $tool1['WajibSurat'],
                'Status' => $tool1['Status']
            ];

            foreach ($daftarAlatTabrakan as $tool2) {
                if ($tool1['AlatID'] === $tool2['AlatID']) {
                    $newJumlahPinjam = $tool1['Jumlah_ketersediaan'] - $tool2['Jumlah_pinjam'];
                    if ($newJumlahPinjam >= 0) {
                        $fixAlat[count($fixAlat) - 1]['Jumlah_ketersediaan'] = $newJumlahPinjam;
                    } else {
                        $fixAlat[count($fixAlat) - 1]['Jumlah_ketersediaan'] = 0;
                    }
                }
            }
        }

        usort($fixAlat, function ($a, $b) {
            return strcmp($a['NamaAlat'], $b['NamaAlat']);
        });

        return response()->json(['daftarAlat' => $daftarAlat, 'daftaralattabrakan' => $daftarAlatTabrakan, 'daftarAlatfix' => $fixAlat]);
    }

    //cek jadwal tabrakan untuk dosen
    public function jadwalAlatforDosen($Tanggal_pakai_awal, $Tanggal_pakai_akhir)
    {
        /* $peminjamanalat = Peminjaman_Alat_Bridge::orWhere(function ($query) {
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
            })->get(); */

        $peminjamanalatpertama = Peminjaman_Alat_Bridge::where('Prioritas', 2)
            ->where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_akhir)
            ->where('Tanggal_pakai_akhir', '>=', $Tanggal_pakai_awal)
            ->get();

        $peminjamanalatlagi = Peminjaman_Alat_Bridge::where('Prioritas', 1)->whereNotNull('DokumenID')
            ->where('Tanggal_pakai_awal', '<=', $Tanggal_pakai_akhir)
            ->where('Tanggal_pakai_akhir', '>=', $Tanggal_pakai_awal)
            ->get();

        $peminjamanalat = $peminjamanalatpertama->merge($peminjamanalatlagi);

        //return response()->json([$peminjamanalatpertama, $peminjamanalatlagi, $peminjamanalat]);

        $daftarAlatTabrakan = [];

        foreach ($peminjamanalat as $tool) {
            $alatid = $tool->AlatID;
            $jumlahpinjam = $tool->Jumlah_pinjam;
            $found = false;
            $ALAT = Alat::where('AlatID', $alatid)->first();
            $namaAlat = $ALAT->Nama;
            $surat = $ALAT->WajibSurat;
            $ketersediaan = $ALAT->Status;

            if ($ketersediaan === 'Tersedia') {
                foreach ($daftarAlatTabrakan as &$existingAlat) {
                    if ($existingAlat['AlatID'] === $alatid) {
                        $found = true;
                        $newtotal = $existingAlat['Jumlah_pinjam'] + $jumlahpinjam;
                        $existingAlat['Jumlah_pinjam'] = $newtotal;
                        break;
                    }
                }

                if ($found === false) {
                    $daftarAlatTabrakan[] = [
                        'AlatID' => $alatid,
                        'Jumlah_pinjam' => $jumlahpinjam,
                        'NamaAlat' => $namaAlat,
                        'WajibSurat' => $surat
                    ];
                }
            }
        }

        $allAlat = Alat::where('Status', 'Tersedia')->get();
        $daftarAlat = [];
        foreach ($allAlat as $alat) {
            $alatid = $alat->AlatID;
            $jumlah = $alat->Jumlah_ketersediaan;
            $nama = $alat->Nama;
            $daftarAlat[] = [
                'AlatID' => $alatid,
                'Jumlah_ketersediaan' => $jumlah,
                'NamaAlat' => $nama,
                'WajibSurat' => $alat->WajibSurat,
                'Status' => $alat->Status
            ];
        }

        $fixAlat = [];
        foreach ($daftarAlat as $tool1) {
            $fixAlat[] = [
                'AlatID' => $tool1['AlatID'],
                'Jumlah_ketersediaan' => $tool1['Jumlah_ketersediaan'],
                'NamaAlat' => $tool1['NamaAlat'],
                'WajibSurat' => $tool1['WajibSurat']
            ];

            foreach ($daftarAlatTabrakan as $tool2) {
                if ($tool1['AlatID'] === $tool2['AlatID']) {
                    $newJumlahPinjam = $tool1['Jumlah_ketersediaan'] - $tool2['Jumlah_pinjam'];
                    if ($newJumlahPinjam >= 0) {
                        $fixAlat[count($fixAlat) - 1]['Jumlah_ketersediaan'] = $newJumlahPinjam;
                    }
                }
            }
        }

        usort($fixAlat, function ($a, $b) {
            return strcmp($a['NamaAlat'], $b['NamaAlat']);
        });

        return response()->json(['daftarAlat' => $daftarAlat, 'daftaralattabrakan' => $daftarAlatTabrakan, 'daftarAlatfix' => $fixAlat]);
    }

    //lihat apakah peminjaman alat lebih dari satu alat dalam satu form 
    public function checkMoreTools($peminjamanid)
    {
        $peminjaman = Peminjaman_Alat_Bridge::where('PeminjamanID', $peminjamanid)->get();
        $toolsmoreone = [];

        if (count($peminjaman) > 1) {
            foreach ($peminjaman as $alat) {
                $alatid = $alat->AlatID;
                $ALAT = Alat::where('AlatID', $alatid)->first();
                $namaalat = $ALAT->Nama;
                $jumlahPinjam = $alat->Jumlah_pinjam;
                $tool = [
                    'namaalat' => $namaalat,
                    'jumlahPinjam' => $jumlahPinjam
                ];
                $toolsmoreone[] = $tool;
            }
            return $toolsmoreone;
        } else {
            return $toolsmoreone;
        }
    }
}
