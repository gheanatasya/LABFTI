<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\Authenticatable;
use \LaravelFCM\Traits\HasPushToken;

class User extends Model
{
    use HasFactory;
    use Notifiable, HasApiTokens;
    
    public function peminjam()
    {
        return $this->hasOne(Peminjam::class);
    }

    public function petugas()
    {
        return $this->hasOne(Petugas::class);
    }

    public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::uuid()->toString();
        });
    }

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'user';
    protected $primaryKey = 'UserID';
    protected $fillable = ['NIM_NIDN', 'Email', 'Password', 'User_role', 'User_priority'];

}
