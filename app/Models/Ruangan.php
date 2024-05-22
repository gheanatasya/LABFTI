<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use HasFactory;

    public function peminjaman_ruangan_bridge(): HasMany
    {
        return $this->hasMany(Peminjaman_Ruangan_Bridge::class, 'RuanganID','RuanganID');
    }

    public $timestamps = false;
    protected $table = 'ruangan';
    protected $primaryKey = 'RuanganID';
    protected $fillable = ['RuanganID', 'Nama_ruangan', 'Kapasitas', 'Lokasi', 'Kategori', 'Fasilitas', 'Foto'];
}
