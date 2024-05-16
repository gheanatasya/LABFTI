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

    public $timestamps = false;
    protected $table = 'persetujuan';
    protected $primaryKey = 'PersetujuanID';
    protected $fillable = ['PeminjamanID', 'Dekan_Approve', 'WD2_Approve', 'WD3_Approve', 'Kepala_Approve', 'Koordinator_Approve', 'Petugas_Approve'];
}
