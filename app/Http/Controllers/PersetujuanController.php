<?php

namespace App\Http\Controllers;

use App\Models\Persetujuan;
use App\Http\Requests\StorePersetujuanRequest;
use App\Http\Requests\UpdatePersetujuanRequest;
use App\Mail\ConfirmAcc;
use App\Models\Activity_Log;
use App\Models\Alat;
use App\Models\Peminjam;
use App\Models\Peminjaman;
use App\Models\Peminjaman_Alat_Bridge;
use App\Models\Peminjaman_Ruangan_Bridge;
use App\Models\Ruangan;
use App\Models\Status;
use App\Models\Status_Peminjaman;
use App\Models\User;
use App\Notifications\NewMessage;
use App\Notifications\SendNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PersetujuanController extends Controller
{
    public function confirmBookingRuangan($Peminjaman_Ruangan_ID, $User_role, $NamaStatus, $Catatan)
    {
        $persetujuan = Persetujuan::where('Peminjaman_Ruangan_ID', $Peminjaman_Ruangan_ID)->first();

        $peminjamanruangan = Peminjaman_Ruangan_Bridge::where('Peminjaman_Ruangan_ID', $Peminjaman_Ruangan_ID)->first();
        $tanggalawal = $peminjamanruangan->Tanggal_pakai_awal;
        $tanggalakhir = $peminjamanruangan->Tanggal_pakai_akhir;
        $ruanganid = $peminjamanruangan->RuanganID;

        $ruangan = Ruangan::where('RuanganID', $ruanganid)->first();
        $namaruang = $ruangan->Nama_ruangan;
        $peminjamanid = $peminjamanruangan->PeminjamanID;
        $peminjaman = Peminjaman::where('PeminjamanID', $peminjamanid)->first();
        $peminjamid = $peminjaman->PeminjamID;
        $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
        $userid = $peminjam->UserID;
        $nama = $peminjam->Nama;
        $user = User::where('UserID', $userid)->first();
        $email = $user->Email;
        $daftarAlat = [];
        $detailRuangan = [
            'namaruangan' => $namaruang,
            'tanggalawal' => $tanggalawal,
            'tanggalakhir' => $tanggalakhir
        ];

        $peminjamanalat = Peminjaman_Alat_Bridge::where('PeminjamanID', $peminjamanid)->get();

        if ($User_role === 'Dekan') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->Dekan_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $persetujuan->Dekan_Approve = false;
                $persetujuan->save();
            }
        } else if ($User_role === 'WD2') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->WD2_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $persetujuan->WD2_Approve = false;
                $persetujuan->save();
            }
        } else if ($User_role === 'WD3') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->WD3_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $persetujuan->WD3_Approve = false;
                $persetujuan->save();
            }
        } else if ($User_role === 'Kepala Lab') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->Kepala_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $persetujuan->Kepala_Approve = false;
                $persetujuan->save();
            }
        } else if ($User_role === 'Koordinator Lab') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->Koordinator_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $persetujuan->Koordinator_Approve = false;
                $persetujuan->save();
            }
        } else if ($User_role === 'Petugas') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->Petugas_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $persetujuan->Petugas_Approve = false;
                $persetujuan->save();
            } else if ($NamaStatus === 'Selesai') {
                $newtanggalakhir = now();
                $peminjamanruangan->Tanggal_pakai_akhir = $newtanggalakhir;
            } else if ($NamaStatus === 'Dibatalkan') {
                $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Ruangan_ID', $Peminjaman_Ruangan_ID)->first();
                $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
                $deletedCount = DB::table('activity_log')
                    ->where('Status_PeminjamanID', $statuspeminjamanid)
                    ->delete();
                $statuspeminjaman->delete();
                $persetujuan->delete();
                $peminjamanruangan->delete();
            }
        }

        $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Ruangan_ID', $Peminjaman_Ruangan_ID)->first();
        if ($statuspeminjaman !== null) {
            $status = Status::where('Nama_status', $NamaStatus)->first();
            $statusid = $status->StatusID;
            $tanggalacc = now();
            $statuspeminjaman->StatusID = $statusid;
            $statuspeminjaman->Tanggal_Acc = $tanggalacc;
            $statuspeminjaman->save();

            $activitylog = Activity_Log::create([
                'Status_PeminjamanID' => $statuspeminjaman->Status_PeminjamanID,
                'Nama_status' => $NamaStatus,
                'Tanggal_Acc' => now(),
                'Acc_by' => $User_role,
                'Catatan' => $Catatan
            ]);
        }

        if ($peminjamanalat != null) {
            foreach ($peminjamanalat as $alat) {

                $alatid = $alat->AlatID;
                $ALAT = Alat::where('AlatID', $alatid)->first();
                $namaalat = $ALAT->Nama;
                $jumlahPinjam = $alat->Jumlah_pinjam;
                $recordAlat = [
                    'namaalat' => $namaalat,
                    'jumlahPinjam' => $jumlahPinjam
                ];
                $daftarAlat[] = $recordAlat;

                $persetujuan = Persetujuan::where('Peminjaman_Alat_ID', $alat->Peminjaman_Alat_ID)->first();
                if ($User_role === 'Dekan') {
                    if ($NamaStatus === 'Diterima') {
                        $persetujuan->Dekan_Approve = true;
                        $persetujuan->save();
                    } else if ($NamaStatus === 'Ditolak') {
                        $persetujuan->Dekan_Approve = false;
                        $persetujuan->save();
                    }
                } else if ($User_role === 'WD2') {
                    if ($NamaStatus === 'Diterima') {
                        $persetujuan->WD2_Approve = true;
                        $persetujuan->save();
                    } else if ($NamaStatus === 'Ditolak') {
                        $persetujuan->WD2_Approve = false;
                        $persetujuan->save();
                    }
                } else if ($User_role === 'WD3') {
                    if ($NamaStatus === 'Diterima') {
                        $persetujuan->WD3_Approve = true;
                        $persetujuan->save();
                    } else if ($NamaStatus === 'Ditolak') {
                        $persetujuan->WD3_Approve = false;
                        $persetujuan->save();
                    }
                } else if ($User_role === 'Kepala Lab') {
                    if ($NamaStatus === 'Diterima') {
                        $persetujuan->Kepala_Approve = true;
                        $persetujuan->save();
                    } else if ($NamaStatus === 'Ditolak') {
                        $persetujuan->Kepala_Approve = false;
                        $persetujuan->save();
                    }
                } else if ($User_role === 'Koordinator Lab') {
                    if ($NamaStatus === 'Diterima') {
                        $persetujuan->Koordinator_Approve = true;
                        $persetujuan->save();
                    } else if ($NamaStatus === 'Ditolak') {
                        $persetujuan->Koordinator_Approve = false;
                        $persetujuan->save();
                    }
                } else if ($User_role === 'Petugas') {
                    if ($NamaStatus === 'Diterima') {
                        $persetujuan->Petugas_Approve = true;
                        $persetujuan->save();
                    } else if ($NamaStatus === 'Ditolak') {
                        $persetujuan->Petugas_Approve = false;
                        $persetujuan->save();
                    } else if ($NamaStatus === 'Selesai') {
                        $newtanggalakhir = now();
                        $alat->Tanggal_pakai_akhir = $newtanggalakhir;
                        $alat->save();
                    } else if ($NamaStatus === 'Dibatalkan') {
                        $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Alat_ID', $alat->Peminjaman_Alat_ID)->first();
                        $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
                        $persetujuan->delete();
                        $deletedCount = DB::table('activity_log')
                            ->where('Status_PeminjamanID', $statuspeminjamanid)
                            ->delete();
                        $statuspeminjaman->delete();
                        $alat->delete();
                    }
                }

                $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Alat_ID', $alat->Peminjaman_Alat_ID)->first();
                if ($statuspeminjaman !== null) {
                    $status = Status::where('Nama_status', $NamaStatus)->first();
                    $statusid = $status->StatusID;
                    $tanggalacc = now();
                    $statuspeminjaman->StatusID = $statusid;
                    $statuspeminjaman->Tanggal_Acc = $tanggalacc;
                    $statuspeminjaman->save();

                    $activitylog = Activity_Log::create([
                        'Status_PeminjamanID' => $statuspeminjaman->Status_PeminjamanID,
                        'Nama_status' => $NamaStatus,
                        'Tanggal_Acc' => now(),
                        'Acc_by' => $User_role,
                        'Catatan' => $Catatan
                    ]);
                }
            }
        }

        $dataEmail = [
            'email' => $email,
            'nama' => $nama,
            'detailruangan' => $detailRuangan,
            'addOnAlat' => $daftarAlat,
            'namastatus' => $NamaStatus,
            'catatan' => $Catatan,
            'role' => $User_role,
            'subject' => 'Persetujuan Peminjaman Ruangan dan Alat LAB FTI UKDW',
        ];

        $dataNotifikasi = [
            'subject' => 'Status Peminjaman Ruangan',
            'detailruangan' => $detailRuangan,
            'detailalat' => $daftarAlat,
            'namastatus' => $NamaStatus,
            'accby' => $User_role,
            'catatan' => $Catatan,
        ];

        $title = 'Konfirmasi Peminjaman Ruangan';
        $body = 'Peminjaman ruangan' . ' ' . $detailRuangan['namaruangan'] . ' ' . 'telah' . ' ' . $NamaStatus . ' ' . 'oleh' . ' ' . $User_role;

        Mail::to($email)->send(new ConfirmAcc($dataEmail));
        $peminjam->notify(new SendNotification($dataNotifikasi));
        $peminjam->notify(new NewMessage($title, $body));

        return response()->json([
            'message' => 'Persetujuan ruangan berhasil diperbarui', 'data' => $Peminjaman_Ruangan_ID,
            'status' => $statusid, 'activitylog' => $activitylog
        ]);
    }

    public function confirmBookingAlat2($Peminjaman_Alat_ID, $User_role, $NamaStatus, $Catatan)
    {
        $peminjamanAlat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
        $peminjamanid = $peminjamanAlat->PeminjamanID;
        $peminjaman = Peminjaman::where('PeminjamanID', $peminjamanid)->first();
        $peminjamid = $peminjaman->PeminjamID;
        $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
        $userid = $peminjam->UserID;
        $nama = $peminjam->Nama;
        $user = User::where('UserID', $userid)->first();
        $email = $user->Email;
        $daftarAlat = [];
        $detailRuangan = [];
        $semuapeminjamanAlat = Peminjaman_Alat_Bridge::where('PeminjamanID', $peminjamanid)->get();

        foreach ($semuapeminjamanAlat as $alat) {

            $alatid = $alat->AlatID;
            $ALAT = Alat::where('AlatID', $alatid)->first();
            $namaalat = $ALAT->Nama;
            $jumlahPinjam = $alat->Jumlah_pinjam;
            $tanggalawal = $alat->Tanggal_pakai_awal;
            $tanggalakhir = $alat->Tanggal_pakai_akhir;
            $recordAlat = [
                'namaalat' => $namaalat,
                'jumlahPinjam' => $jumlahPinjam,
                'tanggalawal' => $tanggalawal,
                'tanggalakhir' => $tanggalakhir
            ];
            $daftarAlat[] = $recordAlat;

            $persetujuan = Persetujuan::where('Peminjaman_Alat_ID', $alat->Peminjaman_Alat_ID)->first();
            if ($User_role === 'Dekan') {
                if ($NamaStatus === 'Diterima') {
                    $persetujuan->Dekan_Approve = true;
                    $persetujuan->save();
                } else if ($NamaStatus === 'Ditolak') {
                    $persetujuan->Dekan_Approve = false;
                    $persetujuan->save();
                }
            } else if ($User_role === 'WD2') {
                if ($NamaStatus === 'Diterima') {
                    $persetujuan->WD2_Approve = true;
                    $persetujuan->save();
                } else if ($NamaStatus === 'Ditolak') {
                    $persetujuan->WD2_Approve = false;
                    $persetujuan->save();
                }
            } else if ($User_role === 'WD3') {
                if ($NamaStatus === 'Diterima') {
                    $persetujuan->WD3_Approve = true;
                    $persetujuan->save();
                } else if ($NamaStatus === 'Ditolak') {
                    $persetujuan->WD3_Approve = false;
                    $persetujuan->save();
                }
            } else if ($User_role === 'Kepala Lab') {
                if ($NamaStatus === 'Diterima') {
                    $persetujuan->Kepala_Approve = true;
                    $persetujuan->save();
                } else if ($NamaStatus === 'Ditolak') {
                    $persetujuan->Kepala_Approve = false;
                    $persetujuan->save();
                }
            } else if ($User_role === 'Koordinator Lab') {
                if ($NamaStatus === 'Diterima') {
                    $persetujuan->Koordinator_Approve = true;
                    $persetujuan->save();
                } else if ($NamaStatus === 'Ditolak') {
                    $persetujuan->Koordinator_Approve = false;
                    $persetujuan->save();
                }
            } else if ($User_role === 'Petugas') {
                if ($NamaStatus === 'Diterima') {
                    $persetujuan->Petugas_Approve = true;
                    $persetujuan->save();
                } else if ($NamaStatus === 'Ditolak') {
                    $persetujuan->Petugas_Approve = false;
                    $persetujuan->save();
                } else if ($NamaStatus === 'Selesai') {
                    $peminjamanalat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $alat->Peminjaman_Alat_ID)->first();
                    $newtanggalakhir = now();
                    $peminjamanalat->Tanggal_pakai_akhir = $newtanggalakhir;
                    $peminjamanalat->save();
                } else if ($NamaStatus === 'Dibatalkan') {
                    $peminjamanalat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $alat->Peminjaman_Alat_ID)->first();
                    $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Alat_ID', $alat->Peminjaman_Alat_ID)->first();
                    $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
                    $persetujuan->delete();
                    $deletedCount = DB::table('activity_log')
                        ->where('Status_PeminjamanID', $statuspeminjamanid)
                        ->delete();
                    $statuspeminjaman->delete();
                    $peminjamanalat->delete();
                }
            }

            $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Alat_ID', $alat->Peminjaman_Alat_ID)->first();
            $status = Status::where('Nama_status', $NamaStatus)->first();
            $statusid = $status->StatusID;
            $tanggalacc = now();
            $statuspeminjaman->StatusID = $statusid;
            $statuspeminjaman->Tanggal_Acc = $tanggalacc;
            $statuspeminjaman->save();

            $activitylog = Activity_Log::create([
                'Status_PeminjamanID' => $statuspeminjaman->Status_PeminjamanID,
                'Nama_status' => $NamaStatus,
                'Tanggal_Acc' => now(),
                'Acc_by' => $User_role,
                'Catatan' => $Catatan
            ]);
        }

        $dataEmail = [
            'email' => $email,
            'nama' => $nama,
            'detailruangan' => $detailRuangan,
            'addOnAlat' => $daftarAlat,
            'namastatus' => $NamaStatus,
            'catatan' => $Catatan,
            'role' => $User_role,
            'subject' => 'Persetujuan Peminjaman Ruangan dan Alat LAB FTI UKDW',
        ];

        $dataNotifikasi = [
            'subject' => 'Status Peminjaman Ruangan',
            'detailruangan' => $detailRuangan,
            'detailalat' => $daftarAlat,
            'namastatus' => $NamaStatus,
            'accby' => $User_role,
            'catatan' => $Catatan,
        ];

        $title = 'Konfirmasi Peminjaman Alat';
        $body = 'Peminjaman alat' . ' ' . $daftarAlat[0]['namaalat'] . ' ' . 'sebanyak' . ' '. $daftarAlat[0]['jumlahPinjam'] . ' ' . 'telah' . ' ' . 
        $NamaStatus . ' ' . 'oleh' . ' ' . $User_role;

        Mail::to($email)->send(new ConfirmAcc($dataEmail));
        $peminjam->notify(new SendNotification($dataNotifikasi));
        $peminjam->notify(new NewMessage($title, $body));

        return response()->json([
            'message' => 'Persetujuan alat berhasil diperbarui'
        ]);
    }

    public function confirmBookingAlat($Peminjaman_Alat_ID, $User_role, $NamaStatus, $Catatan)
    {
        $peminjamanAlat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
        $peminjamanid = $peminjamanAlat->PeminjamanID;
        $peminjaman = Peminjaman::where('PeminjamanID', $peminjamanid)->first();
        $peminjamid = $peminjaman->PeminjamID;
        $peminjam = Peminjam::where('PeminjamID', $peminjamid)->first();
        $userid = $peminjam->UserID;
        $nama = $peminjam->Nama;
        $user = User::where('UserID', $userid)->first();
        $email = $user->Email;
        $daftarAlat = [];
        $detailRuangan = [];

        $alatid = $peminjamanAlat->AlatID;
        $ALAT = Alat::where('AlatID', $alatid)->first();
        $namaalat = $ALAT->Nama;
        $jumlahPinjam = $peminjamanAlat->Jumlah_pinjam;
        $tanggalawal = $peminjamanAlat->Tanggal_pakai_awal;
        $tanggalakhir = $peminjamanAlat->Tanggal_pakai_akhir;
        $recordAlat = [
            'namaalat' => $namaalat,
            'jumlahPinjam' => $jumlahPinjam,
            'tanggalawal' => $tanggalawal,
            'tanggalakhir' => $tanggalakhir
        ];
        $daftarAlat[] = $recordAlat;

        $persetujuan = Persetujuan::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
        if ($User_role === 'Dekan') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->Dekan_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $persetujuan->Dekan_Approve = false;
                $persetujuan->save();
            }
        } else if ($User_role === 'WD2') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->WD2_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $persetujuan->WD2_Approve = false;
                $persetujuan->save();
            }
        } else if ($User_role === 'WD3') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->WD3_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $persetujuan->WD3_Approve = false;
                $persetujuan->save();
            }
        } else if ($User_role === 'Kepala Lab') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->Kepala_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $persetujuan->Kepala_Approve = false;
                $persetujuan->save();
            }
        } else if ($User_role === 'Koordinator Lab') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->Koordinator_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $persetujuan->Koordinator_Approve = false;
                $persetujuan->save();
            }
        } else if ($User_role === 'Petugas') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->Petugas_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $persetujuan->Petugas_Approve = false;
                $persetujuan->save();
            } else if ($NamaStatus === 'Selesai') {
                $peminjamanalat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
                $newtanggalakhir = now();
                $peminjamanalat->Tanggal_pakai_akhir = $newtanggalakhir;
                $peminjamanalat->save();
            } else if ($NamaStatus === 'Dibatalkan') {
                $peminjamanalat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
                $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
                $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
                $persetujuan->delete();
                $deletedCount = DB::table('activity_log')
                    ->where('Status_PeminjamanID', $statuspeminjamanid)
                    ->delete();
                $statuspeminjaman->delete();
                $peminjamanalat->delete();
            }
        }

        $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
        $status = Status::where('Nama_status', $NamaStatus)->first();
        $statusid = $status->StatusID;
        $tanggalacc = now();
        $statuspeminjaman->StatusID = $statusid;
        $statuspeminjaman->Tanggal_Acc = $tanggalacc;
        $statuspeminjaman->save();

        $activitylog = Activity_Log::create([
            'Status_PeminjamanID' => $statuspeminjaman->Status_PeminjamanID,
            'Nama_status' => $NamaStatus,
            'Tanggal_Acc' => now(),
            'Acc_by' => $User_role,
            'Catatan' => $Catatan
        ]);

        $dataEmail = [
            'email' => $email,
            'nama' => $nama,
            'detailruangan' => $detailRuangan,
            'addOnAlat' => $daftarAlat,
            'namastatus' => $NamaStatus,
            'catatan' => $Catatan,
            'role' => $User_role,
            'subject' => 'Persetujuan Peminjaman Ruangan dan Alat LAB FTI UKDW',
        ];

        $dataNotifikasi = [
            'subject' => 'Status Peminjaman Ruangan',
            'detailruangan' => $detailRuangan,
            'detailalat' => $daftarAlat,
            'namastatus' => $NamaStatus,
            'accby' => $User_role,
            'catatan' => $Catatan,
        ];

        $title = 'Konfirmasi Peminjaman Alat';
        $body = 'Peminjaman alat';

        Mail::to($email)->send(new ConfirmAcc($dataEmail));
        $peminjam->notify(new SendNotification($dataNotifikasi));

        return response()->json([
            'message' => 'Persetujuan alat berhasil diperbarui', 'data' => $Peminjaman_Alat_ID,
            'activitylog' => $activitylog
        ]);
    }

    public function coba(){
        $peminjam = Peminjam::where('UserID', '338ffdaf-f4cc-4304-b463-20735996b588')->first();
        $title = 'halo';
        $body = 'is it work?';
        $peminjam->notify(new NewMessage($title, $body));

        return 'oke';
    }
}
