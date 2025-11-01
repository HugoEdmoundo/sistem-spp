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

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    // Accessor untuk format bulan
    public function getNamaBulanAttribute()
    {
        if ($this->bulan) {
            $monthNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            return $monthNames[$this->bulan] ?? null;
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

    // Method untuk cek apakah bisa diedit
    public function getCanEditAttribute()
    {
        return !$this->pembayaran && $this->status === 'unpaid';
    }

    // Method untuk cek apakah bisa dihapus
    public function getCanDeleteAttribute()
    {
        return !$this->pembayaran && $this->status === 'unpaid';
    }

    // Format jumlah ke Rupiah
    public function getJumlahFormattedAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }
}