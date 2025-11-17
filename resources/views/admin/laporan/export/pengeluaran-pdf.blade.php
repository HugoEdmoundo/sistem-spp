<!-- resources/views/admin/laporan/export/pengeluaran-pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengeluaran {{ $tahun }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #2c3e50; }
        .header p { margin: 5px 0; color: #7f8c8d; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PENGELUARAN</h2>
        <p>Tahun: {{ $tahun }}</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @if($pengeluaran->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 35%;">Keterangan</th>
                <th style="width: 15%; text-align: right;">Jumlah</th>
                <th style="width: 18%;">Dibuat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengeluaran as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                <td>{{ $p->kategori }}</td>
                <td>{{ $p->keterangan }}</td>
                <td style="text-align: right; color: #dc3545;">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                <td>{{ $p->admin->nama ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="4" style="text-align: right;">TOTAL PENGELUARAN:</td>
                <td style="text-align: right; color: #dc3545;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Ringkasan per Kategori -->
    <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
        <h4 style="margin: 0 0 10px 0; color: #2c3e50;">Ringkasan Pengeluaran per Kategori</h4>
        @php
            $pengeluaranPerKategori = $pengeluaran->groupBy('kategori')->map(function($items) {
                return $items->sum('jumlah');
            });
        @endphp
        @foreach($pengeluaranPerKategori as $kategori => $total)
        <p style="margin: 5px 0;">{{ $kategori }}: <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></p>
        @endforeach
    </div>
    @else
    <div style="text-align: center; padding: 40px; color: #6c757d;">
        <p>Tidak ada data pengeluaran untuk tahun {{ $tahun }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Dicetak oleh Sistem Pembayaran SPP</p>
    </div>
</body>
</html>