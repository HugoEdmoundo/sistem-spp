<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role',
        'nama',
        'email',
        'username',
        'password',
        'nip',
        'foto',
        'aktif'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'aktif' => 'boolean'
    ];

    // Relasi
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isMurid()
    {
        return $this->role === 'murid';
    }
}