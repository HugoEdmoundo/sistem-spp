{{-- resources/views/admin/laporan/export-spp.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan SPP {{ $tahun }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16px; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .summary { background-color: #e8f4fd; font-weight: bold; }
        .paid { color: green; }
        .cicilan { color: orange; }
        .unpaid { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN SPP TAHUN {{ $tahun }}</h1>
        <p>Sistem Pembayaran Sekolah</p>
        <p>Periode: {{ date('d F Y') }}</p>
    </div>

    @foreach($dataSpp as $item)
    <div style="margin-bottom: 20px;">
        <h3>{{ $loop->iteration }}. {{ $item['murid']->nama }}</h3>
        
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Status</th>
                    <th>Nominal SPP</th>
                    <th>Total Dibayar</th>
                    <th>Tunggakan</th>
                    <th>Detail Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item['status_spp']['semua_bulan'] as $bulan)
                <tr>
                    <td>{{ $bulan['nama_bulan'] }}</td>
                    <td class="{{ $bulan['status'] }}">
                        {{ strtoupper($bulan['status']) }}
                    </td>
                    <td>Rp {{ number_format($nominalSpp, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($bulan['total_dibayar'], 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($nominalSpp - $bulan['total_dibayar'], 0, ',', '.') }}</td>
                    <td>
                        @if(count($bulan['pembayaran']) > 0)
                            @foreach($bulan['pembayaran'] as $pembayaran)
                                Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }} 
                                ({{ $pembayaran->metode }}) - {{ $pembayaran->jenis_bayar }}<br>
                            @endforeach
                        @else
                            Belum bayar
                        @endif
                    </td>
                </tr>
                @endforeach
                <tr class="summary">
                    <td colspan="2"><strong>TOTAL</strong></td>
                    <td><strong>Rp {{ number_format($nominalSpp * 12, 0, ',', '.') }}</strong></td>
                    <td><strong>Rp {{ number_format($item['total_dibayar_spp'], 0, ',', '.') }}</strong></td>
                    <td><strong>Rp {{ number_format($item['total_tunggakan_spp'], 0, ',', '.') }}</strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endforeach
</body>
</html>