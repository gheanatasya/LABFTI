<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persetujuan extends Model
{
    use HasFactory;

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function peminjaman_ruangan_bridge()
    {
        return $this->belongsTo(Peminjaman_Ruangan_Bridge::class);
    }

    public function peminjaman_alat_bridge()
    {
        return $this->belongsTo(Peminjaman_Alat_Bridge::class);
    }

    public $timestamps = false;
    protected $table = 'persetujuan';
    protected $primaryKey = 'PersetujuanID';
    protected $fillable = ['Peminjaman_Ruangan_ID', 'Peminjaman_Alat_ID','Dekan_Approve', 'WD2_Approve', 'WD3_Approve', 'Kepala_Approve', 'Koordinator_Approve', 'Petugas_Approve'];
}
