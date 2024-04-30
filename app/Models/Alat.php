<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alat extends Model
{
    use HasFactory;

    public function peminjaman_alat_bridge(): HasMany
    {
        return $this->hasMany(Peminjaman_Alat_Bridge::class);
    }

    public function detail_alat(): HasMany
    {
        return $this->hasMany(Detail_Alat::class);
    }

    public $timestamps = false;
    protected $table = 'alat';
    protected $primaryKey = 'AlatID';
    protected $fillable = ['Nama', 'Jumlah_ketersediaan'];
}
