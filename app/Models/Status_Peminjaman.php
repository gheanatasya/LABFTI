<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status_Peminjaman extends Model
{
    use HasFactory;

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function activity_log(): HasMany
    {
        return $this->hasMany(Activity_Log::class);
    }
}
