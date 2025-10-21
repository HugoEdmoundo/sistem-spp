<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .summary { margin: 20px 0; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th, .table td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        .table th { background-color: #f5f5f5; }
        .total { font-weight: bold; background-color: #f0f0f0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN KEUANGAN</h2>
        <h3>Periode: {{ $startDate }} s/d {{ $endDate }}</h3>
    </div>

    <div class="summary">
        <h4>Ringkasan Keuangan</h4>
        <p>Total Pemasukan: Rp {{ number_format($pemasukan, 0, ',', '.') }}</p>
        <p>Total Pengeluaran: Rp {{ number_format($pengeluaran, 0, ',', '.') }}</p>
        <p>Saldo Akhir: Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</p>
    </div>

    <h4>Detail Pemasukan</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Siswa</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailPemasukan as $pemasukan)
            <tr>
                <td>{{ $pemasukan->tanggal_proses->format('d/m/Y') }}</td>
                <td>{{ $pemasukan->user->nama }}</td>
                <td>{{ $pemasukan->keterangan }}</td>
                <td>Rp {{ number_format($pemasukan->jumlah, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Detail Pengeluaran</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailPengeluaran as $pengeluaran)
            <tr>
                <td>{{ $pengeluaran->tanggal->format('d/m/Y') }}</td>
                <td>{{ $pengeluaran->kategori }}</td>
                <td>{{ $pengeluaran->keterangan }}</td>
                <td>Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>