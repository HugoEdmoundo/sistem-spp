<!-- resources/views/admin/laporan/export/spp-pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan SPP {{ $tahun }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #2c3e50; }
        .header p { margin: 5px 0; color: #7f8c8d; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 10px; }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-secondary { background-color: #e9ecef; color: #495057; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PEMBAYARAN SPP</h2>
        <p>Tahun Akademik: {{ $tahun }}</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @if(is_array($dataSpp) && count($dataSpp) > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Nama Siswa</th>
                @for($i = 1; $i <= 12; $i++)
                <th style="width: 6.66%; text-align: center;">{{ \App\Models\User::getNamaBulanStatic($i) }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($dataSpp as $dataMurid)
            <tr>
                <td style="font-weight: bold;">{{ $dataMurid['murid']->nama }}</td>
                @for($i = 1; $i <= 12; $i++)
                @php $bulanData = $dataMurid['bulan'][$i]; @endphp
                <td style="text-align: center; vertical-align: middle;">
                    @if($bulanData['status'] === 'LUNAS')
                        <span style="background-color: #d4edda; color: #155724; padding: 2px 6px; border-radius: 3px; font-size: 9px;">LUNAS</span>
                    @elseif($bulanData['status'] === 'CICILAN')
                        <span style="background-color: #fff3cd; color: #856404; padding: 2px 6px; border-radius: 3px; font-size: 9px;">CICIL</span>
                    @else
                        <span style="background-color: #e9ecef; color: #495057; padding: 2px 6px; border-radius: 3px; font-size: 9px;">BELUM</span>
                    @endif
                    <br>
                    <small style="color: #6c757d; font-size: 8px;">
                        Rp {{ number_format($bulanData['total_dibayar'], 0, ',', '.') }}
                    </small>
                </td>
                @endfor
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Ringkasan -->
    <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
        <h4 style="margin: 0 0 10px 0; color: #2c3e50;">Ringkasan SPP {{ $tahun }}</h4>
        @php
            $totalMurid = count($dataSpp);
            $totalBulanLunas = 0;
            $totalBulanCicilan = 0;
            $totalBulanBelum = 0;
            $totalPendapatan = 0;

            foreach($dataSpp as $dataMurid) {
                foreach($dataMurid['bulan'] as $bulanData) {
                    $totalPendapatan += $bulanData['total_dibayar'];
                    if($bulanData['status'] === 'LUNAS') $totalBulanLunas++;
                    elseif($bulanData['status'] === 'CICILAN') $totalBulanCicilan++;
                    else $totalBulanBelum++;
                }
            }
        @endphp
        <p style="margin: 5px 0;">Total Murid: <strong>{{ $totalMurid }}</strong></p>
        <p style="margin: 5px 0;">Bulan Lunas: <strong>{{ $totalBulanLunas }}</strong></p>
        <p style="margin: 5px 0;">Bulan Cicilan: <strong>{{ $totalBulanCicilan }}</strong></p>
        <p style="margin: 5px 0;">Bulan Belum Bayar: <strong>{{ $totalBulanBelum }}</strong></p>
        <p style="margin: 5px 0; font-weight: bold; color: #28a745;">
            Total Pendapatan SPP: Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
        </p>
    </div>
    @else
    <div style="text-align: center; padding: 40px; color: #6c757d;">
        <p>Tidak ada data SPP untuk tahun {{ $tahun }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Dicetak oleh Sistem Pembayaran SPP</p>
    </div>
</body>
</html>