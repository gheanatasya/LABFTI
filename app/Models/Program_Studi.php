<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program_Studi extends Model
{
    use HasFactory;

    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function peminjam(): HasMany
    {
        return $this->hasMany(Peminjam::class);
    }

    protected $table = 'program_studi';
    protected $primaryKey = 'ProdiID';
    protected $fillable = ['Nama_prodi', 'FakultasID'];
    public $timestamps = false;
}
