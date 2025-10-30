<?php
// app/Exports/LaporanPengeluaranExport.php
namespace App\Exports;

use App\Models\Pengeluaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanPengeluaranExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function collection()
    {
        return Pengeluaran::whereYear('tanggal', $this->tahun)
            ->orderBy('tanggal')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kategori',
            'Keterangan',
            'Jumlah',
            'Admin'
        ];
    }

    public function map($pengeluaran): array
    {
        return [
            $pengeluaran->tanggal->format('d/m/Y'),
            $pengeluaran->kategori,
            $pengeluaran->keterangan,
            $pengeluaran->jumlah,
            $pengeluaran->admin->nama ?? '-'
        ];
    }

    public function title(): string
    {
        return 'Pengeluaran ' . $this->tahun;
    }
}