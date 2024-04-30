<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instansi extends Model
{
    use HasFactory;

    public function peminjam(): HasMany
    {
        return $this->hasMany(Peminjam::class);
    }

    protected $table = 'instansi';
    protected $primaryKey = 'InstansiID';
    protected $fillable = ['Nama_instansi', 'Jenis_instansi'];
}
