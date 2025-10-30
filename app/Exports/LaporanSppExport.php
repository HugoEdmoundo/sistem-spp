<?php
// app/Exports/LaporanSppExport.php
namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanSppExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function collection()
    {
        return User::where('role', 'murid')->where('aktif', true)->get();
    }

    public function headings(): array
    {
        return [
            'Nama Murid',
            'Email',
            'Bulan Sudah Bayar',
            'Bulan Belum Bayar',
            'Total Bulan Bayar',
            'Total Bulan Belum Bayar'
        ];
    }

    public function map($murid): array
    {
        $statusSpp = $murid->getStatusSppTahunan($this->tahun);
        
        $bulanSudahBayar = array_map(function($item) {
            return $item['nama_bulan'];
        }, $statusSpp['sudah_bayar']);
        
        $bulanBelumBayar = array_map(function($item) {
            return $item['nama_bulan'];
        }, $statusSpp['belum_bayar']);

        return [
            $murid->nama,
            $murid->email,
            implode(', ', $bulanSudahBayar),
            implode(', ', $bulanBelumBayar),
            count($statusSpp['sudah_bayar']),
            count($statusSpp['belum_bayar'])
        ];
    }

    public function title(): string
    {
        return 'Laporan SPP ' . $this->tahun;
    }
}