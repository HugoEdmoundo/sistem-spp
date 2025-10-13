<?php
namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MuridExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return User::where('role', 'murid')->get();
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Username',
            'NIP',
            'Status',
            'Tanggal Daftar'
        ];
    }

    public function map($user): array
    {
        return [
            $user->nama,
            $user->email,
            $user->username,
            $user->nip ?? '-',
            $user->aktif ? 'Aktif' : 'Nonaktif',
            $user->created_at->format('d/m/Y H:i')
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
}