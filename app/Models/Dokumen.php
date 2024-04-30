<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    public function peminjaman()
    {
        return $this->hasOne(Peminjaman::class);
    }

    public $timestamps = false;
    protected $table = 'dokumen';
    protected $primaryKey = 'DokumenID';
    protected $fillable = ['Nama_dokumen'];
}
