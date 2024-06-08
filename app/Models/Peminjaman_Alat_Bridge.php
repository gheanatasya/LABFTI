<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman_Alat_Bridge extends Model
{
    use HasFactory;

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'PeminjamanID', 'PeminjamanID');
    }

    public function alat()
    {
        return $this->belongsTo(Alat::class, 'AlatID', 'AlatID');
    }

    /* public function detailalat() */
    /* { */
    /*     return $this->belongsTo(Detail_Alat::class, 'DetailAlatID', 'DetailAlatID'); */
    /* } */

    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class);
    }

    public function status_peminjaman()
    {
        return $this->hasOne(Status_Peminjaman::class);
    }

    public function persetujuan()
    {
        return $this->hasOne(Persetujuan::class);
    }

    public $timestamps = false;
    protected $table = 'peminjaman_alat_bridge';
    protected $primaryKey = 'Peminjaman_Alat_ID';
    protected $fillable = [
        'PeminjamanID', 'AlatID', 'Tanggal_pakai_awal', 'Tanggal_pakai_akhir', 'Waktu_pakai', 'Waktu_selesai', 'Waktu_pengambilan', 'Tanggal_pengembalian', 'Waktu_pengembalian', 'Jumlah_pinjam',
        'Is_Personal', 'Is_Organisation', 'Is_Eksternal', 'Keterangan', 'DokumenID', 'RuanganID'
    ];
}
