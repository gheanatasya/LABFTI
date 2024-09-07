<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peminjaman extends Model
{
    use HasFactory;

    public function peminjam(): BelongsTo
    {
        return $this->belongsTo(Peminjam::class,'PeminjamID','PeminjamID');
    }

    /* public function persetujuan()
    {
        return $this->hasOne(Persetujuan::class);
    } */

    /* public function status_peminjaman()
    {
        return $this->hasOne(Status_Peminjaman::class);
    } */

    public function peminjaman_ruangan_bridge(): HasMany
    {
        return $this->hasMany(Peminjaman_Ruangan_Bridge::class, 'PeminjamanID','PeminjamanID');
    }

    public function peminjaman_alat_bridge(): HasMany
    {
        return $this->hasMany(Peminjaman_Alat_Bridge::class,'PeminjamanID','PeminjamanID');
    }

    public $timestamps = false;
    protected $table = 'peminjaman';
    protected $primaryKey = 'PeminjamanID';
    protected $fillable = ['Tanggal_pinjam', 'PeminjamID', 'No_HP'];
}
