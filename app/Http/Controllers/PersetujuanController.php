<?php

namespace App\Http\Controllers;

use App\Models\Persetujuan;
use App\Http\Requests\StorePersetujuanRequest;
use App\Http\Requests\UpdatePersetujuanRequest;
use App\Models\Activity_Log;
use App\Models\Alat;
use App\Models\Peminjaman_Alat_Bridge;
use App\Models\Peminjaman_Ruangan_Bridge;
use App\Models\Status;
use App\Models\Status_Peminjaman;
use Illuminate\Support\Facades\DB;

class PersetujuanController extends Controller
{
    public function confirmBookingRuangan($Peminjaman_Ruangan_ID, $User_role, $NamaStatus, $Catatan)
    {
        $persetujuan = Persetujuan::where('Peminjaman_Ruangan_ID', $Peminjaman_Ruangan_ID)->first();

        $peminjamanruangan = Peminjaman_Ruangan_Bridge::where('Peminjaman_Ruangan_ID', $Peminjaman_Ruangan_ID)->first();
        $tanggalawal = $peminjamanruangan->Tanggal_pakai_awal;
        $tanggalakhir = $peminjamanruangan->Tanggal_pakai_akhir;
        $ruanganid = $peminjamanruangan->RuanganID;
        $peminjamanalat = Peminjaman_Alat_Bridge::where('RuanganID', $ruanganid)
            ->where('Tanggal_pakai_awal', $tanggalawal)
            ->where('Tanggal_pakai_akhir', $tanggalakhir)
            ->get();

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
                $persetujuan = Persetujuan::where('Peminjaman_Alat_ID', $alat->Peminjaman_Alat_ID)->first();
                if ($User_role === 'Dekan') {
                    if ($NamaStatus === 'Diterima') {
                        $persetujuan->Dekan_Approve = true;
                        $persetujuan->save();
                    } else if ($NamaStatus === 'Ditolak') {
                        $alatid = $alat->AlatID;
                        $jumlahPinjam = $alat->Jumlah_pinjam;
                        $ALAT = Alat::where('AlatID', $alatid)->first();
                        $jumlahKetersediaan = $ALAT->Jumlah_ketersediaan;
                        $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                        $ALAT->Jumlah_ketersediaan = $newJumlahKetersediaan;
                        $ALAT->save();
                        $persetujuan->Dekan_Approve = false;
                        $persetujuan->save();
                    }
                } else if ($User_role === 'WD2') {
                    if ($NamaStatus === 'Diterima') {
                        $persetujuan->WD2_Approve = true;
                        $persetujuan->save();
                    } else if ($NamaStatus === 'Ditolak') {
                        $alatid = $alat->AlatID;
                        $jumlahPinjam = $alat->Jumlah_pinjam;
                        $ALAT = Alat::where('AlatID', $alatid)->first();
                        $jumlahKetersediaan = $ALAT->Jumlah_ketersediaan;
                        $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                        $ALAT->Jumlah_ketersediaan = $newJumlahKetersediaan;
                        $ALAT->save();
                        $persetujuan->WD2_Approve = false;
                        $persetujuan->save();
                    }
                } else if ($User_role === 'WD3') {
                    if ($NamaStatus === 'Diterima') {
                        $persetujuan->WD3_Approve = true;
                        $persetujuan->save();
                    } else if ($NamaStatus === 'Ditolak') {
                        $alatid = $alat->AlatID;
                        $jumlahPinjam = $alat->Jumlah_pinjam;
                        $ALAT = Alat::where('AlatID', $alatid)->first();
                        $jumlahKetersediaan = $ALAT->Jumlah_ketersediaan;
                        $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                        $ALAT->Jumlah_ketersediaan = $newJumlahKetersediaan;
                        $ALAT->save();
                        $persetujuan->WD3_Approve = false;
                        $persetujuan->save();
                    }
                } else if ($User_role === 'Kepala Lab') {
                    if ($NamaStatus === 'Diterima') {
                        $persetujuan->Kepala_Approve = true;
                        $persetujuan->save();
                    } else if ($NamaStatus === 'Ditolak') {
                        $alatid = $alat->AlatID;
                        $jumlahPinjam = $alat->Jumlah_pinjam;
                        $ALAT = Alat::where('AlatID', $alatid)->first();
                        $jumlahKetersediaan = $ALAT->Jumlah_ketersediaan;
                        $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                        $ALAT->Jumlah_ketersediaan = $newJumlahKetersediaan;
                        $ALAT->save();
                        $persetujuan->Kepala_Approve = false;
                        $persetujuan->save();
                    }
                } else if ($User_role === 'Koordinator Lab') {
                    if ($NamaStatus === 'Diterima') {
                        $persetujuan->Koordinator_Approve = true;
                        $persetujuan->save();
                    } else if ($NamaStatus === 'Ditolak') {
                        $alatid = $alat->AlatID;
                        $jumlahPinjam = $alat->Jumlah_pinjam;
                        $ALAT = Alat::where('AlatID', $alatid)->first();
                        $jumlahKetersediaan = $ALAT->Jumlah_ketersediaan;
                        $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                        $ALAT->Jumlah_ketersediaan = $newJumlahKetersediaan;
                        $ALAT->save();
                        $persetujuan->Koordinator_Approve = false;
                        $persetujuan->save();
                    }
                } else if ($User_role === 'Petugas') {
                    if ($NamaStatus === 'Diterima') {
                        $persetujuan->Petugas_Approve = true;
                        $persetujuan->save();
                    } else if ($NamaStatus === 'Ditolak') {
                        $alatid = $alat->AlatID;
                        $jumlahPinjam = $alat->Jumlah_pinjam;
                        $ALAT = Alat::where('AlatID', $alatid)->first();
                        $jumlahKetersediaan = $ALAT->Jumlah_ketersediaan;
                        $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                        $ALAT->Jumlah_ketersediaan = $newJumlahKetersediaan;
                        $ALAT->save();
                        $persetujuan->Petugas_Approve = false;
                        $persetujuan->save();
                    } else if ($NamaStatus === 'Selesai') {
                        $alatid = $alat->AlatID;
                        $jumlahPinjam = $alat->Jumlah_pinjam;
                        $ALAT = Alat::where('AlatID', $alatid)->first();
                        $jumlahKetersediaan = $ALAT->Jumlah_ketersediaan;
                        $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                        $ALAT->Jumlah_ketersediaan = $newJumlahKetersediaan;
                        $ALAT->save();

                        $newtanggalakhir = now();
                        $alat->Tanggal_pakai_akhir = $newtanggalakhir;
                        $alat->save();
                    } else if ($NamaStatus === 'Dibatalkan') {
                        $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Alat_ID', $alat->Peminjaman_Alat_ID)->first();
                        $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
                        $alatid = $alat->AlatID;
                        $jumlahPinjam = $alat->Jumlah_pinjam;
                        $ALAT = Alat::where('AlatID', $alatid)->first();
                        $jumlahKetersediaan = $ALAT->Jumlah_ketersediaan;
                        $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                        $ALAT->Jumlah_ketersediaan = $newJumlahKetersediaan;
                        $ALAT->save();

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

        return response()->json([
            'message' => 'Persetujuan ruangan berhasil diperbarui', 'data' => $Peminjaman_Ruangan_ID,
            'status' => $statusid, 'activitylog' => $activitylog
        ]);
    }

    public function confirmBookingAlat($Peminjaman_Alat_ID, $User_role, $NamaStatus, $Catatan)
    {
        $persetujuan = Persetujuan::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
        if ($User_role === 'Dekan') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->Dekan_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $peminjamanalat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
                $alatid = $peminjamanalat->AlatID;
                $jumlahPinjam = $peminjamanalat->Jumlah_pinjam;
                $alat = Alat::where('AlatID', $alatid)->first();
                $jumlahKetersediaan = $alat->Jumlah_ketersediaan;
                $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                $alat->Jumlah_ketersediaan = $newJumlahKetersediaan;
                $alat->save();
                $persetujuan->Dekan_Approve = false;
                $persetujuan->save();
            }
        } else if ($User_role === 'WD2') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->WD2_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $peminjamanalat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
                $alatid = $peminjamanalat->AlatID;
                $jumlahPinjam = $peminjamanalat->Jumlah_pinjam;
                $alat = Alat::where('AlatID', $alatid)->first();
                $jumlahKetersediaan = $alat->Jumlah_ketersediaan;
                $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                $alat->Jumlah_ketersediaan = $newJumlahKetersediaan;
                $alat->save();
                $persetujuan->WD2_Approve = false;
                $persetujuan->save();
            }
        } else if ($User_role === 'WD3') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->WD3_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $peminjamanalat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
                $alatid = $peminjamanalat->AlatID;
                $jumlahPinjam = $peminjamanalat->Jumlah_pinjam;
                $alat = Alat::where('AlatID', $alatid)->first();
                $jumlahKetersediaan = $alat->Jumlah_ketersediaan;
                $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                $alat->Jumlah_ketersediaan = $newJumlahKetersediaan;
                $alat->save();
                $persetujuan->WD3_Approve = false;
                $persetujuan->save();
            }
        } else if ($User_role === 'Kepala Lab') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->Kepala_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $peminjamanalat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
                $alatid = $peminjamanalat->AlatID;
                $jumlahPinjam = $peminjamanalat->Jumlah_pinjam;
                $alat = Alat::where('AlatID', $alatid)->first();
                $jumlahKetersediaan = $alat->Jumlah_ketersediaan;
                $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                $alat->Jumlah_ketersediaan = $newJumlahKetersediaan;
                $alat->save();
                $persetujuan->Kepala_Approve = false;
                $persetujuan->save();
            }
        } else if ($User_role === 'Koordinator Lab') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->Koordinator_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $peminjamanalat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
                $alatid = $peminjamanalat->AlatID;
                $jumlahPinjam = $peminjamanalat->Jumlah_pinjam;
                $alat = Alat::where('AlatID', $alatid)->first();
                $jumlahKetersediaan = $alat->Jumlah_ketersediaan;
                $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                $alat->Jumlah_ketersediaan = $newJumlahKetersediaan;
                $alat->save();
                $persetujuan->Koordinator_Approve = false;
                $persetujuan->save();
            }
        } else if ($User_role === 'Petugas') {
            if ($NamaStatus === 'Diterima') {
                $persetujuan->Petugas_Approve = true;
                $persetujuan->save();
            } else if ($NamaStatus === 'Ditolak') {
                $peminjamanalat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
                $alatid = $peminjamanalat->AlatID;
                $jumlahPinjam = $peminjamanalat->Jumlah_pinjam;
                $alat = Alat::where('AlatID', $alatid)->first();
                $jumlahKetersediaan = $alat->Jumlah_ketersediaan;
                $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                $alat->Jumlah_ketersediaan = $newJumlahKetersediaan;
                $alat->save();
                $persetujuan->Petugas_Approve = false;
                $persetujuan->save();
            } else if ($NamaStatus === 'Selesai') {
                $peminjamanalat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
                $alatid = $peminjamanalat->AlatID;
                $jumlahPinjam = $peminjamanalat->Jumlah_pinjam;
                $alat = Alat::where('AlatID', $alatid)->first();
                $jumlahKetersediaan = $alat->Jumlah_ketersediaan;
                $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                $alat->Jumlah_ketersediaan = $newJumlahKetersediaan;
                $newtanggalakhir = now();
                $peminjamanalat->Tanggal_pakai_akhir = $newtanggalakhir;
                $peminjamanalat->save();
            } else if ($NamaStatus === 'Dibatalkan') {
                $peminjamanalat = Peminjaman_Alat_Bridge::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
                $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Alat_ID', $Peminjaman_Alat_ID)->first();
                $statuspeminjamanid = $statuspeminjaman->Status_PeminjamanID;
                $alatid = $peminjamanalat->AlatID;
                $jumlahPinjam = $peminjamanalat->Jumlah_pinjam;
                $alat = Alat::where('AlatID', $alatid)->first();
                $jumlahKetersediaan = $alat->Jumlah_ketersediaan;
                $newJumlahKetersediaan = $jumlahKetersediaan + $jumlahPinjam;
                $alat->Jumlah_ketersediaan = $newJumlahKetersediaan;
                $alat->save();

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

        return response()->json([
            'message' => 'Persetujuan alat berhasil diperbarui', 'data' => $Peminjaman_Alat_ID,
            'activitylog' => $activitylog
        ]);
    }
}
