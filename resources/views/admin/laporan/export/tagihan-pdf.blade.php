<!-- resources/views/admin/laporan/export/tagihan-pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tagihan {{ $tahun }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #2c3e50; }
        .header p { margin: 5px 0; color: #7f8c8d; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 10px; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN TAGIHAN (NON-SPP)</h2>
        <p>Tahun: {{ $tahun }}</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @if($dataTagihan->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Nama Siswa</th>
                <th style="width: 15%;">Jenis Tagihan</th>
                <th style="width: 25%;">Keterangan</th>
                <th style="width: 10%; text-align: right;">Total Tagihan</th>
                <th style="width: 10%; text-align: right;">Dibayar</th>
                <th style="width: 10%; text-align: right;">Sisa</th>
                <th style="width: 5%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataTagihan as $index => $tagihan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $tagihan->user->nama ?? 'N/A' }}</td>
                <td>{{ $tagihan->jenis }}</td>
                <td>{{ $tagihan->keterangan }}</td>
                <td style="text-align: right;">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</td>
                <td style="text-align: right; color: #28a745;">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</td>
                <td style="text-align: right; color: #dc3545;">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</td>
                <td style="text-align: center;">
                    @if($tagihan->status_detail === 'LUNAS')
                        <span style="background-color: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 10px;">LUNAS</span>
                    @elseif($tagihan->status_detail === 'CICILAN')
                        <span style="background-color: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 10px;">CICIL</span>
                    @else
                        <span style="background-color: #e9ecef; color: #495057; padding: 4px 8px; border-radius: 4px; font-size: 10px;">BELUM</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="4" style="text-align: right;">TOTAL:</td>
                <td style="text-align: right;">Rp {{ number_format($dataTagihan->sum('jumlah'), 0, ',', '.') }}</td>
                <td style="text-align: right; color: #28a745;">Rp {{ number_format($dataTagihan->sum('total_dibayar'), 0, ',', '.') }}</td>
                <td style="text-align: right; color: #dc3545;">Rp {{ number_format($dataTagihan->sum('sisa_tagihan'), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Ringkasan -->
    <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
        <h4 style="margin: 0 0 10px 0; color: #2c3e50;">Ringkasan Tagihan {{ $tahun }}</h4>
        @php
            $totalLunas = $dataTagihan->where('status_detail', 'LUNAS')->count();
            $totalCicilan = $dataTagihan->where('status_detail', 'CICILAN')->count();
            $totalBelum = $dataTagihan->where('status_detail', 'BELUM BAYAR')->count();
        @endphp
        <p style="margin: 5px 0;">Total Tagihan: <strong>{{ $dataTagihan->count() }}</strong></p>
        <p style="margin: 5px 0;">Status Lunas: <strong>{{ $totalLunas }}</strong></p>
        <p style="margin: 5px 0;">Status Cicilan: <strong>{{ $totalCicilan }}</strong></p>
        <p style="margin: 5px 0;">Status Belum Bayar: <strong>{{ $totalBelum }}</strong></p>
    </div>
    @else
    <div style="text-align: center; padding: 40px; color: #6c757d;">
        <p>Tidak ada data tagihan untuk tahun {{ $tahun }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Dicetak oleh Sistem Pembayaran SPP</p>
    </div>
</body>
</html>