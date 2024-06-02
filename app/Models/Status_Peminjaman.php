<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status_Peminjaman extends Model
{
    use HasFactory;

    public function peminjaman_ruangan_bridge(): HasMany
    {
        return $this->hasMany(Peminjaman_Ruangan_Bridge::class);
    }

    public function peminjaman_alat_bridge(): HasMany
    {
        return $this->hasMany(Peminjaman_Alat_Bridge::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function activity_log(): HasMany
    {
        return $this->hasMany(Activity_Log::class);
    }

    public $timestamps = false;
    protected $table = 'status_peminjaman';
    protected $primaryKey = 'Status_PeminjamanID';
    protected $fillable = ['Peminjaman_Ruangan_ID', 'Peminjaman_Alat_ID', 'StatusID', 'Tanggal_Acc'];
}
