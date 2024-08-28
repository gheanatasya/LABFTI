<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Detail_Alat extends Model
{
    use HasFactory;

    public function alat()
    {
        return $this->belongsTo(Alat::class, 'AlatID', 'AlatID');
    }

    /* public function peminjaman_alat_bridge(): HasMany */
    /* { */
    /*     return $this->hasMany(Peminjaman_Alat_Bridge::class, 'DetailAlatID', 'DetailAlatID'); */
    /* } */

    public $timestamps = false;
    protected $table = 'detail_alat';
    protected $primaryKey = 'DetailAlatID';
    protected $fillable = ['DetailAlatID', 'AlatID', 'Nama_alat', 'Status_Kebergunaan', 'Status_Peminjaman', 'Foto', 'KodeDetailAlat'];
}
