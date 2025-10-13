<?php
// app/Models/Tagihan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis',
        'keterangan',
        'bulan',
        'tahun',
        'jumlah',
        'status'
    ];

    // Casting untuk dates
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    // Scope
    public function scopeSpp($query)
    {
        return $query->where('jenis', 'spp');
    }

    public function scopeCustom($query)
    {
        return $query->where('jenis', 'custom');
    }

    // Accessor untuk format bulan
    public function getNamaBulanAttribute()
    {
        if ($this->bulan) {
            return \Carbon\Carbon::create()->month($this->bulan)->format('F');
        }
        return null;
    }

    // Accessor untuk periode
    public function getPeriodeAttribute()
    {
        if ($this->bulan && $this->tahun) {
            return $this->nama_bulan . ' ' . $this->tahun;
        }
        return '-';
    }
}