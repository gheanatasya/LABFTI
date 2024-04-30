<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fakultas extends Model
{
    use HasFactory;

    public function program_studi(): HasMany
    {
        return $this->hasMany(Program_Studi::class);
    }

    protected $table = 'fakultas';
    protected $primaryKey = 'FakultasID';
    protected $fillable = ['Nama_fakultas', 'created_at', 'updated_at'];
}
