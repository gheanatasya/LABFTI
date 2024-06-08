<?php

namespace App\Http\Controllers;

use App\Models\Persetujuan;
use App\Http\Requests\StorePersetujuanRequest;
use App\Http\Requests\UpdatePersetujuanRequest;
use App\Models\Activity_Log;
use App\Models\Peminjaman_Alat_Bridge;
use App\Models\Peminjaman_Ruangan_Bridge;
use App\Models\Status;
use App\Models\Status_Peminjaman;

class PersetujuanController extends Controller
{
    public function confirmBookingRuangan($Peminjaman_Ruangan_ID, $User_role, $NamaStatus, $Catatan)
    {
        $persetujuan = Persetujuan::where('Peminjaman_Ruangan_ID', $Peminjaman_Ruangan_ID)->first();
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
            }
        }

        $peminjamanruangan = Peminjaman_Ruangan_Bridge::where('Peminjaman_Ruangan_ID', $Peminjaman_Ruangan_ID)->first();
        $tanggalawal = $peminjamanruangan->Tanggal_pakai_awal;
        $tanggalakhir = $peminjamanruangan->Tanggal_pakai_akhir;
        $ruanganid = $peminjamanruangan->RuanganID;
        $peminjamanalat = Peminjaman_Alat_Bridge::where('RuanganID', $ruanganid)
            ->where('Tanggal_pakai_awal', $tanggalawal)
            ->where('Tanggal_pakai_akhir', $tanggalakhir)
            ->get();

        if ($peminjamanalat != null) {
            foreach ($peminjamanalat as $alat) {
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
        }
    
        $statuspeminjaman = Status_Peminjaman::where('Peminjaman_Ruangan_ID', $Peminjaman_Ruangan_ID)->first();
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
