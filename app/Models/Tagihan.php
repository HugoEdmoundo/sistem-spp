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
        'status' // unpaid, pending, success
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'jumlah' => 'decimal:2'
    ];

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    // Scopes
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

    public function scopeBelumLunas($query)
    {
        return $query->whereIn('status', ['unpaid', 'pending']);
    }

    public static function generateTagihanSpp($userId, $tahun, $bulanMulai, $bulanAkhir)
    {
        $sppSetting = SppSetting::latest()->first();
        if (!$sppSetting) {
            throw new \Exception('Setting SPP belum diatur');
        }

        $nominalSpp = $sppSetting->nominal;
        $jumlahBulan = ($bulanAkhir - $bulanMulai) + 1;
        $totalTagihan = $nominalSpp * $jumlahBulan;

        // Buat tagihan SPP
        $tagihan = self::create([
            'user_id' => $userId,
            'jenis' => 'spp',
            'keterangan' => 'SPP ' . $jumlahBulan . ' bulan (' . 
                        User::getNamaBulanStatic($bulanMulai) . ' - ' . 
                        User::getNamaBulanStatic($bulanAkhir) . ' ' . $tahun . ')',
            'jumlah' => $totalTagihan,
            'status' => 'unpaid'
        ]);

        return $tagihan;
    }

    // Scope untuk SPP
    public function scopeSpp($query)
    {
        return $query->where('jenis', 'spp');
    }

    public function scopeNonSpp($query)
    {
        return $query->where('jenis', '!=', 'spp');
    }

    // Accessors
    public function getJumlahFormattedAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    public function getTotalDibayarAttribute()
    {
        return $this->pembayaran()
            ->where('status', 'accepted')
            ->sum('jumlah');
    }

    public function getTotalDibayarFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_dibayar, 0, ',', '.');
    }

    public function getSisaTagihanAttribute()
    {
        return max(0, $this->jumlah - $this->total_dibayar);
    }

    public function getSisaTagihanFormattedAttribute()
    {
        return 'Rp ' . number_format($this->sisa_tagihan, 0, ',', '.');
    }

    public function getPersentaseDibayarAttribute()
    {
        if ($this->jumlah == 0) return 0;
        return ($this->total_dibayar / $this->jumlah) * 100;
    }

    public function getStatusLabelAttribute()
    {
        return [
            'unpaid' => 'Belum Lunas',
            'pending' => 'Menunggu',
            'success' => 'Lunas'
        ][$this->status] ?? $this->status;
    }

    // Cek status
    public function getIsLunasAttribute()
    {
        return $this->status === 'success';
    }

    public function getIsUnpaidAttribute()
    {
        return $this->status === 'unpaid';
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    // Cek apakah masih bisa bayar (cicilan)
    public function getIsCicilanAttribute()
    {
        return $this->total_dibayar > 0 && $this->total_dibayar < $this->jumlah;
    }

    // Methods
    public function bisaBayar()
    {
        return !$this->is_lunas;
    }

    public function getMinimalPembayaranBerikutnya()
    {
        $sisa = $this->sisa_tagihan;
        $minimal = max(1000, $sisa * 0.1); // 10% dari sisa atau 1000
        return min($minimal, $sisa);
    }

    // Update status otomatis berdasarkan TOTAL pembayaran
    public function updateStatus()
    {
        if ($this->total_dibayar >= $this->jumlah) {
            $this->update(['status' => 'success']);
        } elseif ($this->pembayaran()->where('status', 'pending')->exists()) {
            $this->update(['status' => 'pending']);
        } else {
            $this->update(['status' => 'unpaid']);
        }
    }

    // Cek apakah dengan jumlah tertentu akan melunasi
    public function akanLunasDenganJumlah($jumlah)
    {
        return ($this->total_dibayar + $jumlah) >= $this->jumlah;
    }
}