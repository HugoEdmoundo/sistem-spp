<?php
// app/Models/Pembayaran.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'tagihan_id',
        'user_id',
        'admin_id',
        'metode',
        'bukti',
        'jumlah',
        'status', // pending, accepted, rejected, partial
        'alasan_reject',
        'keterangan',
        'jenis_bayar', // 'lunas' atau 'cicilan'
        'tanggal_upload',
        'tanggal_bayar',
        'tanggal_proses',
        'catatan_admin',
        'tahun',
        'bulan_mulai',
        'bulan_akhir'
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
        'tanggal_proses' => 'datetime',
        'tanggal_bayar' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'jumlah' => 'decimal:2',
        'tahun' => 'integer',
        'bulan_mulai' => 'integer',
        'bulan_akhir' => 'integer'
    ];

    protected $attributes = [
        'status' => 'pending'
    ];

    // Relasi
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDiterima($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    public function scopeLunas($query)
    {
        return $query->where('jenis_bayar', 'lunas');
    }

    public function scopeCicilan($query)
    {
        return $query->where('jenis_bayar', 'cicilan');
    }

    public function scopeBelumLunas($query)
    {
        return $query->whereIn('status', ['pending', 'partial']);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Menunggu',
            'accepted' => 'Lunas', 
            'rejected' => 'Ditolak',
            'partial' => 'Cicilan'
        ][$this->status] ?? $this->status;
    }

    public function getJenisBayarLabelAttribute()
    {
        return [
            'lunas' => 'Lunas',
            'cicilan' => 'Cicilan'
        ][$this->jenis_bayar] ?? $this->jenis_bayar;
    }

    public function getJumlahFormattedAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    public function getTotalDibayarFormattedAttribute()
    {
        if ($this->tagihan) {
            return 'Rp ' . number_format($this->tagihan->total_dibayar, 0, ',', '.');
        }
        return 'Rp 0';
    }

    public function getSisaTagihanAttribute()
    {
        if ($this->tagihan) {
            // Hitung total yang sudah dibayar (exclude pembayaran yang sedang diproses)
            $totalDibayar = $this->tagihan->pembayaran()
                ->where('status', 'accepted')
                ->sum('jumlah');
                
            return max(0, $this->tagihan->jumlah - $totalDibayar);
        }
        return 0;
    }

        
    // Tambahkan method untuk cek apakah pembayaran ini akan melunasi tagihan
    public function akanMelunasiTagihan()
    {
        if ($this->tagihan) {
            $totalDibayarSebelumnya = $this->tagihan->pembayaran()
                ->where('status', 'accepted')
                ->where('id', '!=', $this->id) // Exclude pembayaran saat ini
                ->sum('jumlah');
                
            return ($totalDibayarSebelumnya + $this->jumlah) >= $this->tagihan->jumlah;
        }
        return false;
    }

    public function getAlasanRejectSingkatAttribute()
    {
        if (!$this->alasan_reject) return null;
        
        return strlen($this->alasan_reject) > 50 
            ? substr($this->alasan_reject, 0, 50) . '...'
            : $this->alasan_reject;
    }

    // Methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isAccepted()
    {
        return $this->status === 'accepted';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isPartial()
    {
        return $this->status === 'partial';
    }

    public function isCicilan()
    {
        return $this->jenis_bayar === 'cicilan';
    }

    public function isLunas()
    {
        return $this->jenis_bayar === 'lunas';
    }
    
    // Hitung progress pembayaran
    public function getProgressPercentage()
    {
        if ($this->tagihan && $this->tagihan->jumlah > 0) {
            return ($this->tagihan->total_dibayar / $this->tagihan->jumlah) * 100;
        }
        return 0;
    }
}