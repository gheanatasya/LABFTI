<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman_Ruangan_Bridge extends Model
{
    use HasFactory;

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
}
