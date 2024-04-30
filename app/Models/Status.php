<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    public function status_peminjaman()
    {
        return $this->hasOne(Status_Peminjaman::class);
    }
}
