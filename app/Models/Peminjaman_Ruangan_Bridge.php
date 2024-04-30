<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman_Ruangan_Bridge extends Model
{
    use HasFactory;

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'PeminjamanID','PeminjamanID');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class,'RuanganID','RuanganID');
    }

    public $timestamps = false;
    protected $table = 'peminjaman_ruangan_bridge';
    protected $primaryKey = 'Peminjaman_Ruangan_ID';
    protected $fillable = ['PeminjamanID', 'RuanganID', 'Tanggal_pakai_awal', 'Tanggal_pakai_akhir', 'Waktu_pakai', 'Waktu_selesai'];
}
