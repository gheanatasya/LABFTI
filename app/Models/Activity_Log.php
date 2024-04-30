<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity_Log extends Model
{
    use HasFactory;

    public function status_peminjaman()
    {
        return $this->belongsTo(Status_Peminjaman::class);
    }
}
