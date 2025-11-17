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
        // Cek pembayaran SPP murni (tanpa tagihan_id)
        $pembayaranSppMurni = $this->pembayaran()
            ->where('status', 'accepted')
            ->whereNull('tagihan_id')
            ->where('tahun', $tahun)
            ->where(function($query) use ($bulan) {
                $query->where(function($q) use ($bulan) {
                    $q->whereNotNull('bulan_mulai')
                    ->whereNotNull('bulan_akhir')
                    ->where('bulan_mulai', '<=', $bulan)
                    ->where('bulan_akhir', '>=', $bulan);
                });
            })
            ->exists();

        if ($pembayaranSppMurni) {
            return true;
        }

        // Cek pembayaran via tagihan SPP
        $pembayaranViaTagihan = $this->pembayaran()
            ->where('status', 'accepted')
            ->whereHas('tagihan', function($query) {
                $query->where('jenis', 'spp');
            })
            ->where('tahun', $tahun)
            ->where(function($query) use ($bulan) {
                $query->where(function($q) use ($bulan) {
                    $q->whereNotNull('bulan_mulai')
                    ->whereNotNull('bulan_akhir')
                    ->where('bulan_mulai', '<=', $bulan)
                    ->where('bulan_akhir', '>=', $bulan);
                });
            })
            ->exists();

        return $pembayaranViaTagihan;
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
     * Get SPP payment status for a specific year - VERSI DIPERBAIKI
     */
    // app/Models/User.php
    public function getStatusSppTahunan($tahun): array
    {
        $bulanStatus = [];
        
        // Inisialisasi semua bulan sebagai unpaid
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $bulanStatus[$bulan] = [
                'bulan' => $bulan,
                'nama_bulan' => $this->getNamaBulan($bulan),
                'status' => 'unpaid', // unpaid, cicilan, paid
                'jenis_bayar' => null,
                'total_dibayar' => 0,
                'pembayaran' => []
            ];
        }
        
        // Ambil SEMUA pembayaran SPP untuk tahun tertentu (baik murni maupun via tagihan)
        $pembayaranSpp = $this->pembayaran()
            ->where('status', 'accepted')
            ->where('tahun', $tahun)
            ->where(function($query) {
                // SPP murni ATAU SPP via tagihan
                $query->whereNull('tagihan_id')
                    ->orWhereHas('tagihan', function($q) {
                        $q->where('jenis', 'spp');
                    });
            })
            ->with(['tagihan'])
            ->orderBy('tanggal_proses', 'asc')
            ->get();

        // Update status bulan berdasarkan pembayaran
        foreach ($pembayaranSpp as $pembayaran) {
            if ($pembayaran->bulan_mulai && $pembayaran->bulan_akhir) {
                for ($bulan = $pembayaran->bulan_mulai; $bulan <= $pembayaran->bulan_akhir; $bulan++) {
                    if ($bulan >= 1 && $bulan <= 12) {
                        $bulanStatus[$bulan]['pembayaran'][] = $pembayaran;
                        $bulanStatus[$bulan]['total_dibayar'] += $pembayaran->jumlah;
                        $bulanStatus[$bulan]['jenis_bayar'] = $pembayaran->jenis_bayar;
                        
                        // Update status
                        if ($pembayaran->jenis_bayar === 'lunas') {
                            $bulanStatus[$bulan]['status'] = 'paid';
                        } elseif ($pembayaran->jenis_bayar === 'cicilan') {
                            $bulanStatus[$bulan]['status'] = 'cicilan';
                        }
                    }
                }
            }
        }

        // Pisahkan menjadi sudah bayar dan belum bayar
        $sudahBayar = array_filter($bulanStatus, function($item) {
            return in_array($item['status'], ['paid', 'cicilan']);
        });
        
        $belumBayar = array_filter($bulanStatus, function($item) {
            return $item['status'] === 'unpaid';
        });

        return [
            'sudah_bayar' => array_values($sudahBayar),
            'belum_bayar' => array_values($belumBayar),
            'semua_bulan' => array_values($bulanStatus),
            'total_sudah_bayar' => array_sum(array_column($sudahBayar, 'total_dibayar')),
            'total_belum_bayar' => (count($belumBayar) * (SppSetting::latest()->first()->nominal ?? 0))
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

    /**
     * Cek apakah bisa bayar SPP dengan cicilan
     */
    public function bisaBayarSppCicilan($tahun, $bulanMulai, $bulanAkhir, $jumlahBayar): array
    {
        // Validasi input
        if ($bulanMulai < 1 || $bulanMulai > 12 || $bulanAkhir < 1 || $bulanAkhir > 12) {
            return [
                'bisa_proses' => false,
                'error' => 'Bulan harus antara 1-12'
            ];
        }

        if ($bulanMulai > $bulanAkhir) {
            return [
                'bisa_proses' => false,
                'error' => 'Bulan mulai tidak boleh lebih besar dari bulan akhir'
            ];
        }

        $sppSetting = SppSetting::latest()->first();
        $nominalSppPerBulan = $sppSetting ? $sppSetting->nominal : 0;
        $jumlahBulan = ($bulanAkhir - $bulanMulai) + 1;
        $totalHarusBayar = $nominalSppPerBulan * $jumlahBulan;

        // Untuk cicilan, jumlah bayar bisa kurang dari total
        if ($jumlahBayar <= 0) {
            return [
                'bisa_proses' => false,
                'error' => 'Jumlah bayar harus lebih dari 0'
            ];
        }

        // Cek bulan yang sudah lunas (tidak bisa dicicil lagi)
        $bulanSudahLunas = [];
        for ($bulan = $bulanMulai; $bulan <= $bulanAkhir; $bulan++) {
            if ($this->isBulanSudahLunas($tahun, $bulan)) {
                $bulanSudahLunas[] = $bulan;
            }
        }

        if (!empty($bulanSudahLunas)) {
            $bulanNames = array_map(function($bulan) {
                return $this->getNamaBulan($bulan);
            }, $bulanSudahLunas);
            
            return [
                'bisa_proses' => false,
                'error' => 'Bulan ' . implode(', ', $bulanNames) . ' sudah lunas. Tidak bisa dicicil lagi.'
            ];
        }

        return [
            'bisa_proses' => true,
            'total_harus_bayar' => $totalHarusBayar,
            'jumlah_bulan' => $jumlahBulan,
            'sisa' => $totalHarusBayar - $jumlahBayar,
            'persentase' => ($jumlahBayar / $totalHarusBayar) * 100
        ];
    }

    /**
     * Cek apakah bulan sudah lunas (bukan cicilan)
     */
    public function isBulanSudahLunas($tahun, $bulan): bool
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
                      ->where('bulan_akhir', '>=', $bulan)
                      ->where('jenis_bayar', 'lunas');
                });
            })
            ->first();

        return (bool) $pembayaran;
    }

    /**
     * Get status SPP dengan detail cicilan
     */
    public function getStatusSppTahunanDetail($tahun): array
    {
        $bulanStatus = [];
        
        // Inisialisasi semua bulan
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $bulanStatus[$bulan] = [
                'bulan' => $bulan,
                'nama_bulan' => $this->getNamaBulan($bulan),
                'status' => 'unpaid', // unpaid, cicilan, paid
                'total_tagihan' => 0,
                'total_dibayar' => 0,
                'sisa' => 0,
                'persentase' => 0,
                'pembayaran' => []
            ];
        }
        
        // Ambil nominal SPP
        $sppSetting = SppSetting::latest()->first();
        $nominalSpp = $sppSetting ? $sppSetting->nominal : 0;

        // Set total tagihan untuk semua bulan
        foreach ($bulanStatus as $bulan => $status) {
            $bulanStatus[$bulan]['total_tagihan'] = $nominalSpp;
            $bulanStatus[$bulan]['sisa'] = $nominalSpp;
        }

        // Ambil semua pembayaran SPP untuk tahun tertentu
        $pembayaranSpp = $this->pembayaran()
            ->where('status', 'accepted')
            ->whereNull('tagihan_id')
            ->where('tahun', $tahun)
            ->orderBy('tanggal_proses', 'asc')
            ->get();

        // Proses setiap pembayaran
        foreach ($pembayaranSpp as $pembayaran) {
            if ($pembayaran->bulan_mulai && $pembayaran->bulan_akhir) {
                $jumlahBulan = ($pembayaran->bulan_akhir - $pembayaran->bulan_mulai) + 1;
                $jumlahPerBulan = $pembayaran->jumlah / $jumlahBulan;
                
                for ($bulan = $pembayaran->bulan_mulai; $bulan <= $pembayaran->bulan_akhir; $bulan++) {
                    if ($bulan >= 1 && $bulan <= 12) {
                        $bulanStatus[$bulan]['total_dibayar'] += $jumlahPerBulan;
                        $bulanStatus[$bulan]['sisa'] = $bulanStatus[$bulan]['total_tagihan'] - $bulanStatus[$bulan]['total_dibayar'];
                        $bulanStatus[$bulan]['persentase'] = ($bulanStatus[$bulan]['total_dibayar'] / $bulanStatus[$bulan]['total_tagihan']) * 100;
                        
                        // Tentukan status
                        if ($bulanStatus[$bulan]['sisa'] <= 0) {
                            $bulanStatus[$bulan]['status'] = 'paid';
                        } elseif ($bulanStatus[$bulan]['total_dibayar'] > 0) {
                            $bulanStatus[$bulan]['status'] = 'cicilan';
                        } else {
                            $bulanStatus[$bulan]['status'] = 'unpaid';
                        }
                        
                        $bulanStatus[$bulan]['pembayaran'][] = [
                            'id' => $pembayaran->id,
                            'jumlah' => $jumlahPerBulan,
                            'tanggal' => $pembayaran->tanggal_proses,
                            'metode' => $pembayaran->metode,
                            'jenis_bayar' => $pembayaran->jenis_bayar
                        ];
                    }
                }
            }
        }

        return [
            'sudah_lunas' => array_values(array_filter($bulanStatus, function($item) {
                return $item['status'] === 'paid';
            })),
            'masih_cicilan' => array_values(array_filter($bulanStatus, function($item) {
                return $item['status'] === 'cicilan';
            })),
            'belum_bayar' => array_values(array_filter($bulanStatus, function($item) {
                return $item['status'] === 'unpaid';
            })),
            'semua_bulan' => array_values($bulanStatus)
        ];
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