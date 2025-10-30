<?php
// app/Http/Controllers/LaporanController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pengeluaran;
use App\Models\Pembayaran;
use App\Exports\LaporanSppExport;
use App\Exports\LaporanPengeluaranExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{   
    public function index()
    {
        $tahun = request('tahun', date('Y'));
        
        \Log::info("LaporanController diakses - Tahun: {$tahun}");

        $murid = User::where('role', 'murid')->where('aktif', true)->get();
        
        $dataMurid = [];
        foreach ($murid as $m) {
            // Gunakan method yang sudah diperbaiki dari Model User
            $statusSpp = $m->getStatusSppTahunan($tahun);
            
            $dataMurid[] = [
                'murid' => $m,
                'sudah_bayar' => $statusSpp['sudah_bayar'],
                'belum_bayar' => $statusSpp['belum_bayar'],
                'total_bulan_bayar' => count($statusSpp['sudah_bayar']),
                'total_bulan_belum_bayar' => count($statusSpp['belum_bayar'])
            ];
        }

        // Data pengeluaran
        $pengeluaran = Pengeluaran::whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalPengeluaran = $pengeluaran->sum('jumlah');
        
        $pembayaranPendingCount = Pembayaran::where('status', 'pending')->count();

        \Log::info("Data akhir untuk tahun {$tahun}:", [
            'dataMurid_count' => count($dataMurid),
            'pengeluaran_count' => $pengeluaran->count(),
            'totalPengeluaran' => $totalPengeluaran
        ]);

        return view('admin.laporan.index', compact(
            'dataMurid', 
            'pengeluaran', 
            'totalPengeluaran', 
            'tahun',
            'pembayaranPendingCount'
        ));
    }

    /**
     * Helper method untuk LaporanController
     */
    private function getNamaBulanSederhana($bulan): string
    {
        return User::getNamaBulanStatic($bulan);
    }

    public function exportSppExcel($tahun)
    {
        $filename = 'laporan-spp-' . $tahun . '-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new LaporanSppExport($tahun), $filename);
    }

    public function exportSppPdf($tahun)
    {
        $murid = User::where('role', 'murid')->where('aktif', true)->get();
        
        $dataMurid = [];
        foreach ($murid as $m) {
            $statusSpp = $m->getStatusSppTahunan($tahun);
            $dataMurid[] = [
                'murid' => $m,
                'sudah_bayar' => $statusSpp['sudah_bayar'],
                'belum_bayar' => $statusSpp['belum_bayar']
            ];
        }

        $pdf = Pdf::loadView('admin.laporan.export-spp-pdf', compact('dataMurid', 'tahun'));
        return $pdf->download('laporan-spp-' . $tahun . '-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportPengeluaranExcel($tahun)
    {
        $filename = 'laporan-pengeluaran-' . $tahun . '-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new LaporanPengeluaranExport($tahun), $filename);
    }

    public function exportPengeluaranPdf($tahun)
    {
        $pengeluaran = Pengeluaran::whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->get();

        $totalPengeluaran = $pengeluaran->sum('jumlah');

        $pdf = Pdf::loadView('admin.laporan.export-pengeluaran-pdf', 
            compact('pengeluaran', 'totalPengeluaran', 'tahun')
        );
        
        return $pdf->download('laporan-pengeluaran-' . $tahun . '-' . now()->format('Y-m-d') . '.pdf');
    }
}