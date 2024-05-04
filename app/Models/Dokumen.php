<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    public function peminjaman_alat_bridge()
    {
        return $this->hasOne(Peminjaman_Alat_Bridge::class);
    }

    public function peminjaman_ruangan_bridge()
    {
        return $this->hasOne(Peminjaman_Ruangan_Bridge::class);
    }

    public $timestamps = false;
    protected $table = 'dokumen';
    protected $primaryKey = 'DokumenID';
    protected $fillable = ['Nama_dokumen'];
}
