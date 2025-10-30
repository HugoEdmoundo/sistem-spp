<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * Cek apakah bulan sudah dibayar untuk tahun tertentu
     */
    public function isBulanSudahDibayar($tahun, $bulan): bool
    {
        $pembayaran = $this->pembayaran()
            ->where('status', 'accepted')
            ->whereNull('tagihan_id')
            ->where('tahun', $tahun)
            ->where(function($query) use ($bulan) {
                $query->where(function($q) use ($bulan) {
                        $q->whereNotNull('bulan_mulai')
                        ->whereNotNull('bulan_akhir')
                        ->where('bulan_mulai', '<=', $bulan)
                        ->where('bulan_akhir', '>=', $bulan);
                    })
                    ->orWhere(function($q) use ($bulan) {
                        // Untuk data yang tidak punya range bulan, cek dari keterangan
                        $q->whereNull('bulan_mulai')
                        ->orWhereNull('bulan_akhir');
                    });
            })
            ->first();

        if ($pembayaran) {
            // Jika tidak ada range bulan, cek dari keterangan
            if (!$pembayaran->bulan_mulai || !$pembayaran->bulan_akhir) {
                $bulanTerdeteksi = $this->deteksiBulanDariKeterangan($pembayaran->keterangan, $tahun);
                return in_array($bulan, $bulanTerdeteksi);
            }
            return true;
        }

        return false;
    }

    /**
     * Cek range bulan apakah sudah ada yang dibayar
     */
    public function getBulanSudahDibayar($tahun): array
    {
        $bulanSudahBayar = [];
        
        $pembayaranTahunIni = $this->pembayaran()
            ->where('status', 'accepted')
            ->whereNull('tagihan_id')
            ->where('tahun', $tahun)
            ->get();

        foreach ($pembayaranTahunIni as $pembayaran) {
            if ($pembayaran->bulan_mulai && $pembayaran->bulan_akhir) {
                for ($bulan = $pembayaran->bulan_mulai; $bulan <= $pembayaran->bulan_akhir; $bulan++) {
                    if ($bulan >= 1 && $bulan <= 12) {
                        $bulanSudahBayar[$bulan] = $bulan;
                    }
                }
            } else {
                // Deteksi dari keterangan
                $bulanTerdeteksi = $this->deteksiBulanDariKeterangan($pembayaran->keterangan, $tahun);
                foreach ($bulanTerdeteksi as $bulan) {
                    if ($bulan >= 1 && $bulan <= 12) {
                        $bulanSudahBayar[$bulan] = $bulan;
                    }
                }
            }
        }

        return array_values($bulanSudahBayar);
    }

    /**
     * Cek apakah bisa bayar untuk range bulan tertentu
     */
    public function bisaBayarSpp($tahun, $bulanMulai, $bulanAkhir): array
    {
        // Validasi input
        if ($bulanMulai < 1 || $bulanMulai > 12 || $bulanAkhir < 1 || $bulanAkhir > 12) {
            return [
                'bisa_dibayar' => [],
                'sudah_dibayar' => [],
                'bisa_proses' => false,
                'error' => 'Bulan harus antara 1-12'
            ];
        }

        if ($bulanMulai > $bulanAkhir) {
            return [
                'bisa_dibayar' => [],
                'sudah_dibayar' => [],
                'bisa_proses' => false,
                'error' => 'Bulan mulai tidak boleh lebih besar dari bulan akhir'
            ];
        }

        $bulanBisaDibayar = [];
        $bulanSudahDibayar = [];
        
        for ($bulan = $bulanMulai; $bulan <= $bulanAkhir; $bulan++) {
            if ($this->isBulanSudahDibayar($tahun, $bulan)) {
                $bulanSudahDibayar[] = $bulan;
            } else {
                $bulanBisaDibayar[] = $bulan;
            }
        }

        return [
            'bisa_dibayar' => $bulanBisaDibayar,
            'sudah_dibayar' => $bulanSudahDibayar,
            'bisa_proses' => empty($bulanSudahDibayar),
            'error' => null
        ];
    }

    /**
     * Get SPP payment status for a specific year - DIBAIKI
     */
    public function getStatusSppTahunan($tahun): array
    {
        $bulanSudahBayar = [];
        
        // Ambil pembayaran SPP untuk tahun tertentu
        $pembayaranSpp = $this->pembayaran()
            ->where('status', 'accepted')
            ->whereNull('tagihan_id')
            ->where('tahun', $tahun)
            ->orderBy('tanggal_proses', 'asc')
            ->get();

        // Proses bulan yang sudah bayar dengan benar
        foreach ($pembayaranSpp as $pembayaran) {
            if ($pembayaran->bulan_mulai && $pembayaran->bulan_akhir) {
                // Jika range bulan valid
                for ($bulan = $pembayaran->bulan_mulai; $bulan <= $pembayaran->bulan_akhir; $bulan++) {
                    if ($bulan >= 1 && $bulan <= 12) {
                        $bulanSudahBayar[$bulan] = [
                            'bulan' => $bulan,
                            'nama_bulan' => $this->getNamaBulan($bulan),
                            'status' => 'paid'
                        ];
                    }
                }
            } else {
                // Jika tidak ada range bulan, coba deteksi dari keterangan
                $bulanTerdeteksi = $this->deteksiBulanDariKeterangan($pembayaran->keterangan, $tahun);
                foreach ($bulanTerdeteksi as $bulan) {
                    if ($bulan >= 1 && $bulan <= 12) {
                        $bulanSudahBayar[$bulan] = [
                            'bulan' => $bulan,
                            'nama_bulan' => $this->getNamaBulan($bulan),
                            'status' => 'paid'
                        ];
                    }
                }
            }
        }

        // Tentukan bulan yang belum bayar
        $bulanBelumBayar = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            if (!isset($bulanSudahBayar[$bulan])) {
                $bulanBelumBayar[$bulan] = [
                    'bulan' => $bulan,
                    'nama_bulan' => $this->getNamaBulan($bulan),
                    'status' => 'unpaid'
                ];
            }
        }

        return [
            'sudah_bayar' => array_values($bulanSudahBayar),
            'belum_bayar' => array_values($bulanBelumBayar)
        ];
    }

    /**
     * Deteksi bulan dari keterangan - DIPERBAIKI
     */
    private function deteksiBulanDariKeterangan($keterangan, $tahun): array
    {
        $bulanTerdeteksi = [];
        $keterangan = strtolower($keterangan);
        
        $bulanMapping = [
            1 => ['januari', 'january', 'jan', 'bulan 1', 'bln 1', '01'],
            2 => ['februari', 'february', 'feb', 'bulan 2', 'bln 2', '02'],
            3 => ['maret', 'march', 'mar', 'bulan 3', 'bln 3', '03'],
            4 => ['april', 'apr', 'bulan 4', 'bln 4', '04'],
            5 => ['mei', 'may', 'bulan 5', 'bln 5', '05'],
            6 => ['juni', 'june', 'jun', 'bulan 6', 'bln 6', '06'],
            7 => ['juli', 'july', 'jul', 'bulan 7', 'bln 7', '07'],
            8 => ['agustus', 'august', 'aug', 'bulan 8', 'bln 8', '08'],
            9 => ['september', 'sep', 'bulan 9', 'bln 9', '09'],
            10 => ['oktober', 'october', 'oct', 'bulan 10', 'bln 10', '10'],
            11 => ['november', 'nov', 'bulan 11', 'bln 11', '11'],
            12 => ['desember', 'december', 'dec', 'bulan 12', 'bln 12', '12']
        ];
        
        // Cek berdasarkan nama bulan
        foreach ($bulanMapping as $bulan => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($keterangan, $keyword) !== false) {
                    $bulanTerdeteksi[] = $bulan;
                    break;
                }
            }
        }
        
        // Cek berdasarkan pattern "Bulan X-Y" atau "Bulan X s/d Y"
        if (preg_match('/bulan\s+(\d+)\s*-\s*(\d+)/', $keterangan, $matches) ||
            preg_match('/bulan\s+(\d+)\s*s\/d\s*(\d+)/', $keterangan, $matches) ||
            preg_match('/(\d+)\s*-\s*(\d+)/', $keterangan, $matches)) {
            $bulanMulai = (int)$matches[1];
            $bulanAkhir = (int)$matches[2];
            
            // Validasi range bulan
            if ($bulanMulai >= 1 && $bulanMulai <= 12 && $bulanAkhir >= 1 && $bulanAkhir <= 12 && $bulanMulai <= $bulanAkhir) {
                for ($bulan = $bulanMulai; $bulan <= $bulanAkhir; $bulan++) {
                    $bulanTerdeteksi[] = $bulan;
                }
            }
        }
        
        return array_unique($bulanTerdeteksi);
    }

    /**
     * Get nama bulan untuk instance
     */
    public function getNamaBulan($bulan): string
    {
        return self::getNamaBulanStatic($bulan);
    }

    /**
     * Static method untuk get nama bulan (bisa dipanggil di view)
     */
    public static function getNamaBulanStatic($bulan): string
    {
        $bulanArr = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulanArr[$bulan] ?? 'Bulan ' . $bulan;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMurid(): bool
    {
        return $this->role === 'murid';
    }
}