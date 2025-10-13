<?php
namespace App\Exports;

use App\Models\Pembayaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PembayaranExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
        $query = Pembayaran::with(['user', 'tagihan', 'admin']);
        
        if ($this->startDate) {
            $query->whereDate('tanggal_upload', '>=', $this->startDate);
        }
        
        if ($this->endDate) {
            $query->whereDate('tanggal_upload', '<=', $this->endDate);
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nama Murid',
            'Tagihan',
            'Metode Pembayaran',
            'Jumlah',
            'Status',
            'Bukti',
            'Tanggal Upload',
            'Tanggal Proses',
            'Admin'
        ];
    }

    public function map($pembayaran): array
    {
        return [
            $pembayaran->user->nama,
            $pembayaran->tagihan->keterangan,
            $pembayaran->metode,
            'Rp ' . number_format($pembayaran->jumlah, 0, ',', '.'),
            $this->getStatusText($pembayaran->status),
            $pembayaran->bukti ? 'Ada' : 'Tidak Ada',
            $pembayaran->tanggal_upload->format('d/m/Y H:i'),
            $pembayaran->tanggal_proses ? $pembayaran->tanggal_proses->format('d/m/Y H:i') : '-',
            $pembayaran->admin ? $pembayaran->admin->nama : '-'
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
            'pending' => 'Menunggu Verifikasi',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak'
        ];
        
        return $statuses[$status] ?? $status;
    }
}