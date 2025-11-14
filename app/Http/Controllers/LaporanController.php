<?php
// app/Http/Controllers/LaporanController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\SppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\TahunTrait;

class LaporanController extends Controller
{
    use TahunTrait;

    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $tahunUntukSelect = $this->getTahunUntukSelect();
        
        // Data untuk Laporan SPP (KHUSUS SPP)
        $dataSpp = $this->getLaporanSpp($tahun);
        
        // Data untuk Laporan Tagihan (KHUSUS TAGIHAN)
        $dataTagihan = $this->getLaporanTagihan($tahun);
        
        // Data untuk Laporan Pengeluaran
        $pengeluaran = Pengeluaran::with('admin')
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();
            
        $totalPengeluaran = $pengeluaran->sum('jumlah');

        return view('admin.laporan.index', compact(
            'dataSpp',
            'dataTagihan',
            'pengeluaran', 
            'totalPengeluaran',
            'tahun',
            'tahunUntukSelect'
        ));
    }

    /**
     * Method untuk Laporan SPP KHUSUS
     */
    private function getLaporanSpp($tahun)
    {
        $muridAktif = User::where('role', 'murid')
            ->where('aktif', true)
            ->get();

        $dataSpp = [];

        foreach ($muridAktif as $murid) {
            $dataMurid = [
                'murid' => $murid,
                'bulan' => []
            ];

            // Inisialisasi semua bulan sebagai belum bayar
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $dataMurid['bulan'][$bulan] = [
                    'nama_bulan' => $this->getNamaBulan($bulan),
                    'status' => 'BELUM BAYAR',
                    'pembayaran' => [],
                    'total_dibayar' => 0,
                    'jenis_bayar' => null
                ];
            }

            // Ambil SEMUA pembayaran SPP untuk murid ini di tahun ini
            $pembayaranSpp = Pembayaran::where('user_id', $murid->id)
                ->where('status', 'accepted')
                ->where(function($query) {
                    // SPP murni (tanpa tagihan_id) ATAU SPP yang via tagihan
                    $query->whereNull('tagihan_id')
                          ->orWhereHas('tagihan', function($q) {
                              $q->where('jenis', 'spp');
                          });
                })
                ->where('tahun', $tahun)
                ->with(['tagihan'])
                ->get();

            // Proses setiap pembayaran SPP
            foreach ($pembayaranSpp as $pembayaran) {
                if ($pembayaran->bulan_mulai && $pembayaran->bulan_akhir) {
                    // Pembayaran dengan range bulan
                    for ($bulan = $pembayaran->bulan_mulai; $bulan <= $pembayaran->bulan_akhir; $bulan++) {
                        if ($bulan >= 1 && $bulan <= 12) {
                            $dataMurid['bulan'][$bulan]['pembayaran'][] = $pembayaran;
                            $dataMurid['bulan'][$bulan]['total_dibayar'] += $pembayaran->jumlah;
                            $dataMurid['bulan'][$bulan]['jenis_bayar'] = $pembayaran->jenis_bayar;
                            
                            // Update status berdasarkan jenis bayar
                            if ($pembayaran->jenis_bayar === 'lunas') {
                                $dataMurid['bulan'][$bulan]['status'] = 'LUNAS';
                            } elseif ($pembayaran->jenis_bayar === 'cicilan') {
                                $dataMurid['bulan'][$bulan]['status'] = 'CICILAN';
                            }
                        }
                    }
                }
            }

            $dataSpp[] = $dataMurid;
        }

        return $dataSpp;
    }

    /**
     * Method untuk Laporan Tagihan KHUSUS (non-SPP)
     */
    private function getLaporanTagihan($tahun)
    {
        $tagihan = Tagihan::with(['user', 'pembayaran' => function($query) {
                $query->where('status', 'accepted');
            }])
            ->where('jenis', '!=', 'spp') // Hanya tagihan non-SPP
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($tagihan) {
                // Hitung total dibayar dan sisa
                $tagihan->total_dibayar = $tagihan->pembayaran->sum('jumlah');
                $tagihan->sisa_tagihan = $tagihan->jumlah - $tagihan->total_dibayar;
                $tagihan->persentase = $tagihan->jumlah > 0 ? ($tagihan->total_dibayar / $tagihan->jumlah) * 100 : 0;
                
                // Tentukan status detail
                if ($tagihan->status === 'success') {
                    $tagihan->status_detail = 'LUNAS';
                } elseif ($tagihan->total_dibayar > 0) {
                    $tagihan->status_detail = 'CICILAN';
                } else {
                    $tagihan->status_detail = 'BELUM BAYAR';
                }
                
                return $tagihan;
            });

        return $tagihan;
    }

    /**
     * Get nama bulan
     */
    private function getNamaBulan($bulan)
    {
        $bulanArr = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulanArr[$bulan] ?? 'Bulan ' . $bulan;
    }

    // Export Laporan SPP Excel
    public function exportSppExcel($tahun)
    {
        $dataSpp = $this->getLaporanSpp($tahun);
        
        return Excel::download(new class($dataSpp, $tahun) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle {
            private $dataSpp;
            private $tahun;

            public function __construct($dataSpp, $tahun)
            {
                $this->dataSpp = $dataSpp;
                $this->tahun = $tahun;
            }

            public function collection()
            {
                $data = [];
                $no = 1;

                foreach ($this->dataSpp as $itemMurid) {
                    $murid = $itemMurid['murid'];
                    
                    foreach ($itemMurid['bulan'] as $bulan => $dataBulan) {
                        $detailTransaksi = [];
                        
                        // Detail pembayaran SPP
                        foreach ($dataBulan['pembayaran'] as $pembayaran) {
                            $rangeBulan = $pembayaran->bulan_mulai == $pembayaran->bulan_akhir ? 
                                $this->getNamaBulan($pembayaran->bulan_mulai) : 
                                $this->getNamaBulan($pembayaran->bulan_mulai) . ' - ' . $this->getNamaBulan($pembayaran->bulan_akhir);
                            
                            $detailTransaksi[] = "SPP {$rangeBulan}: Rp " . number_format($pembayaran->jumlah, 0, ',', '.') . 
                                                " (" . $pembayaran->metode . ")" .
                                                ($pembayaran->jenis_bayar === 'cicilan' ? " [Cicilan]" : "");
                        }
                        
                        $transaksiText = implode("; ", $detailTransaksi);
                        if (empty($transaksiText)) {
                            $transaksiText = "Tidak ada pembayaran";
                        }

                        $data[] = [
                            'No' => $no++,
                            'Nama Siswa' => $murid->nama,
                            'Bulan' => $dataBulan['nama_bulan'],
                            'Status' => $dataBulan['status'],
                            'Total Dibayar' => 'Rp ' . number_format($dataBulan['total_dibayar'], 0, ',', '.'),
                            'Jenis Bayar' => $dataBulan['jenis_bayar'] ?? '-',
                            'Detail Pembayaran' => $transaksiText
                        ];
                    }
                }

                return collect($data);
            }

            private function getNamaBulan($bulan)
            {
                $bulanArr = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                return $bulanArr[$bulan] ?? 'Bulan ' . $bulan;
            }

            public function headings(): array
            {
                return [
                    'No',
                    'Nama Siswa', 
                    'Bulan',
                    'Status',
                    'Total Dibayar',
                    'Jenis Bayar',
                    'Detail Pembayaran'
                ];
            }

            public function title(): string
            {
                return 'Laporan SPP ' . $this->tahun;
            }
        }, 'laporan-spp-' . $tahun . '.xlsx');
    }

    // Export Laporan SPP PDF
    public function exportSppPdf($tahun)
    {
        $dataSpp = $this->getLaporanSpp($tahun);
        
        $pdf = Pdf::loadView('admin.laporan.export-spp', [
            'dataSpp' => $dataSpp,
            'tahun' => $tahun
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-spp-' . $tahun . '.pdf');
    }

    // Export Laporan Tagihan PDF
    public function exportTagihanPdf($tahun)
    {
        $dataTagihan = $this->getLaporanTagihan($tahun);
        
        $pdf = Pdf::loadView('admin.laporan.export-tagihan-pdf', [
            'dataTagihan' => $dataTagihan,
            'tahun' => $tahun
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-tagihan-' . $tahun . '.pdf');
    }

    // Export Laporan Tagihan Excel
    public function exportTagihanExcel($tahun)
    {
        $dataTagihan = $this->getLaporanTagihan($tahun);
        
        return Excel::download(new class($dataTagihan, $tahun) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle {
            private $dataTagihan;
            private $tahun;

            public function __construct($dataTagihan, $tahun)
            {
                $this->dataTagihan = $dataTagihan;
                $this->tahun = $tahun;
            }

            public function collection()
            {
                $data = [];
                $no = 1;

                foreach ($this->dataTagihan as $tagihan) {
                    $data[] = [
                        'No' => $no++,
                        'Nama Siswa' => $tagihan->user->nama,
                        'Jenis Tagihan' => $tagihan->jenis,
                        'Keterangan' => $tagihan->keterangan,
                        'Total Tagihan' => 'Rp ' . number_format($tagihan->jumlah, 0, ',', '.'),
                        'Total Dibayar' => 'Rp ' . number_format($tagihan->total_dibayar, 0, ',', '.'),
                        'Sisa Tagihan' => 'Rp ' . number_format($tagihan->sisa_tagihan, 0, ',', '.'),
                        'Progress' => number_format($tagihan->persentase, 1) . '%',
                        'Status' => $tagihan->status_detail
                    ];
                }

                return collect($data);
            }

            public function headings(): array
            {
                return [
                    'No',
                    'Nama Siswa', 
                    'Jenis Tagihan',
                    'Keterangan',
                    'Total Tagihan',
                    'Total Dibayar', 
                    'Sisa Tagihan',
                    'Progress',
                    'Status'
                ];
            }

            public function title(): string
            {
                return 'Laporan Tagihan ' . $this->tahun;
            }
        }, 'laporan-tagihan-' . $tahun . '.xlsx');
    }

    // Export Laporan Pengeluaran Excel
    public function exportPengeluaranExcel($tahun)
    {
        $pengeluaran = Pengeluaran::with('admin')
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        return Excel::download(new class($pengeluaran, $tahun) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle {
            private $pengeluaran;
            private $tahun;

            public function __construct($pengeluaran, $tahun)
            {
                $this->pengeluaran = $pengeluaran;
                $this->tahun = $tahun;
            }

            public function collection()
            {
                $data = [];
                $no = 1;

                foreach ($this->pengeluaran as $p) {
                    $data[] = [
                        'No' => $no++,
                        'Tanggal' => $p->tanggal->format('d/m/Y'),
                        'Kategori' => $p->kategori,
                        'Keterangan' => $p->keterangan,
                        'Jumlah' => 'Rp ' . number_format($p->jumlah, 0, ',', '.'),
                        'Petugas' => $p->admin->nama ?? '-'
                    ];
                }

                return collect($data);
            }

            public function headings(): array
            {
                return [
                    'No',
                    'Tanggal',
                    'Kategori', 
                    'Keterangan',
                    'Jumlah',
                    'Petugas'
                ];
            }

            public function title(): string
            {
                return 'Laporan Pengeluaran ' . $this->tahun;
            }
        }, 'laporan-pengeluaran-' . $tahun . '.xlsx');
    }

    // Export Laporan Pengeluaran PDF
    public function exportPengeluaranPdf($tahun)
    {
        $pengeluaran = Pengeluaran::with('admin')
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalPengeluaran = $pengeluaran->sum('jumlah');

        $pdf = Pdf::loadView('admin.laporan.export-pengeluaran-pdf', [
            'pengeluaran' => $pengeluaran,
            'totalPengeluaran' => $totalPengeluaran,
            'tahun' => $tahun
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-pengeluaran-' . $tahun . '.pdf');
    }
}