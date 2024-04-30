<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail_Alat extends Model
{
    use HasFactory;

    public function alat()
    {
        return $this->belongsTo(Alat::class);
    }

    public $timestamps = false;
    protected $table = 'detail_alat';
    protected $primaryKey = 'DetailAlatID';
    protected $fillable = ['AlatID', 'Nama_alat', 'Status_Kebergunaan', 'Status_Peminjaman', 'Foto'];
}
