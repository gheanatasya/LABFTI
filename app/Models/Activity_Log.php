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

    public $timestamps = false;
    protected $table = 'activity_log';
    protected $primaryKey = 'Activity_LogID';
    protected $fillable = ['Status_PeminjamanID', 'Nama_status', 'Tanggal_Acc', 'Acc_by', 'Catatan'];
}
