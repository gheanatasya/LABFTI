<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use \LaravelFCM\Traits\HasPushToken;

class Peminjam extends Model
{
    use HasFactory, Notifiable;

    public function program_studi(): BelongsTo
    {
        return $this->belongsTo(Program_Studi::class);
    }

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'PeminjamID','PeminjamID');
    }

    public function routeNotificationForFCM()
    {
        return $this->web_token;
    }

    public $timestamps = false;
    protected $table = 'peminjam';
    protected $primaryKey = 'PeminjamID';
    protected $fillable = ['Nama', 'UserID', 'ProdiID', 'InstansiID', 'Total_batal', 'Tanggal_batal_terakhir', 'web_token', 'resetpass_token', 'validTime_token', 'Bataspersonal', 'Tanggal_bataspersonal'];
}
