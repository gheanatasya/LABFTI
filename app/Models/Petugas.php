<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public $timestamps = false;
    protected $table = 'petugas';
    protected $primaryKey = 'PetugasID';
    // protected $guarded = ['PetugasID'];
    protected $fillable = ['Email', 'Nama', 'NIM', 'Foto', 'Tgl_Bekerja', 'Tgl_Berhenti', 'UserID'];
}
