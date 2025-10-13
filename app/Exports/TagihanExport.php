<?php
namespace App\Exports;

use App\Models\Tagihan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TagihanExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = Tagihan::with('user');
        
        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nama Murid',
            'Jenis Tagihan',
            'Keterangan',
            'Bulan/Tahun',
            'Jumlah',
            'Status',
            'Tanggal Tagihan'
        ];
    }

    public function map($tagihan): array
    {
        return [
            $tagihan->user->nama,
            ucfirst($tagihan->jenis),
            $tagihan->keterangan,
            $tagihan->periode,
            'Rp ' . number_format($tagihan->jumlah, 0, ',', '.'),
            $this->getStatusText($tagihan->status),
            $tagihan->created_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E8449']]
            ],
        ];
    }

    private function getStatusText($status)
    {
        $statuses = [
            'unpaid' => 'Belum Bayar',
            'pending' => 'Menunggu Verifikasi',
            'success' => 'Lunas',
            'rejected' => 'Ditolak'
        ];
        
        return $statuses[$status] ?? $status;
    }
}