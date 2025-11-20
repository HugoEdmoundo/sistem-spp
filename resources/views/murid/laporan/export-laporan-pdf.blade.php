<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - {{ $user->nama }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #2c3e50;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            color: #7f8c8d;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 5px 15px;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        .table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            font-size: 11px;
        }
        .table-sm td {
            padding: 4px 8px;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-secondary { background-color: #e9ecef; color: #495057; }
        .badge-info { background-color: #d1ecf1; color: #0c5460; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .mb-3 { margin-bottom: 15px; }
        .mt-3 { margin-top: 15px; }
        .section-title {
            background-color: #2c3e50;
            color: white;
            padding: 8px 12px;
            margin: 20px 0 10px 0;
            font-size: 13px;
            font-weight: bold;
            border-radius: 4px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
        }
        .page-break {
            page-break-after: always;
        }
        .monospace {
            font-family: 'DejaVu Sans Mono', monospace;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN KEUANGAN SISWA</h1>
        <h2>Tahun Ajaran {{ $tahun }}</h2>
    </div>

    <!-- Informasi Siswa -->
    <div class="info-section">
        <div class="info-grid">
            <div class="info-label">Nama Siswa:</div>
            <div>{{ $user->nama }}</div>
            
            <div class="info-label">NIS/Username:</div>
            <div>{{ $user->username }}</div>
            
            <div class="info-label">Tanggal Export:</div>
            <div>{{ $tanggalExport->format('d F Y H:i') }}</div>
            
            <div class="info-label">Tahun Laporan:</div>
            <div>{{ $tahun }}</div>
        </div>
    </div>

    <!-- Section 1: Laporan SPP -->
    <div class="section-title">A. LAPORAN PEMBAYARAN SPP</div>
    
    <table class="table">
        <thead>
            <tr>
                <th width="12%">Bulan</th>
                <th width="15%">Status</th>
                <th width="20%" class="text-right">Total Dibayar</th>
                <th width="20%" class="text-right">Nominal SPP</th>
                <th width="33%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDibayarSpp = 0;
                $totalHarusDibayarSpp = 0;
            @endphp
            @for($i = 1; $i <= 12; $i++)
            @php 
                $bulanData = $dataSpp['bulan'][$i];
                $totalDibayarSpp += $bulanData['total_dibayar'];
                $totalHarusDibayarSpp += $dataSpp['nominal_spp'];
            @endphp
            <tr>
                <td class="text-bold">{{ \App\Models\User::getNamaBulanStatic($i) }}</td>
                <td>
                    @if($bulanData['status'] === 'LUNAS')
                        <span class="badge badge-success">LUNAS</span>
                    @elseif($bulanData['status'] === 'CICILAN')
                        <span class="badge badge-warning">CICILAN</span>
                    @else
                        <span class="badge badge-secondary">BELUM BAYAR</span>
                    @endif
                </td>
                <td class="text-right monospace">Rp {{ number_format($bulanData['total_dibayar'], 0, ',', '.') }}</td>
                <td class="text-right monospace">Rp {{ number_format($dataSpp['nominal_spp'], 0, ',', '.') }}</td>
                <td>
                    @if($bulanData['status'] === 'LUNAS')
                        Sudah lunas
                    @elseif($bulanData['status'] === 'CICILAN')
                        Masih cicilan ({{ number_format(($bulanData['total_dibayar'] / $dataSpp['nominal_spp']) * 100, 0) }}%)
                    @else
                        Belum ada pembayaran
                    @endif
                </td>
            </tr>
            @endfor
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa;">
                <td colspan="2" class="text-bold">TOTAL</td>
                <td class="text-right text-bold monospace">Rp {{ number_format($totalDibayarSpp, 0, ',', '.') }}</td>
                <td class="text-right text-bold monospace">Rp {{ number_format($totalHarusDibayarSpp, 0, ',', '.') }}</td>
                <td class="text-bold">
                    @php
                        $persentaseSpp = $totalHarusDibayarSpp > 0 ? ($totalDibayarSpp / $totalHarusDibayarSpp) * 100 : 0;
                    @endphp
                    Total Progress: {{ number_format($persentaseSpp, 1) }}%
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Detail Pembayaran SPP per Bulan -->
    <div class="mb-3">
        <div style="font-size: 11px; font-weight: bold; margin-bottom: 8px; color: #2c3e50;">
            DETAIL PEMBAYARAN SPP:
        </div>
        @for($i = 1; $i <= 12; $i++)
        @php $bulanData = $dataSpp['bulan'][$i]; @endphp
        @if(count($bulanData['pembayaran']) > 0)
        <div style="margin-bottom: 12px; padding: 8px; border: 1px solid #dee2e6; border-radius: 4px;">
            <div style="font-weight: bold; margin-bottom: 5px; color: #2c3e50;">
                {{ \App\Models\User::getNamaBulanStatic($i) }} - 
                @if($bulanData['status'] === 'LUNAS')
                    <span style="color: #155724;">LUNAS</span>
                @elseif($bulanData['status'] === 'CICILAN')
                    <span style="color: #856404;">CICILAN</span>
                @endif
            </div>
            @foreach($bulanData['pembayaran'] as $index => $pembayaran)
            <div style="display: grid; grid-template-columns: 80px 1fr auto; gap: 5px; font-size: 10px; margin-bottom: 3px;">
                <span class="monospace">{{ $pembayaran['tanggal']->format('d/m/Y') }}</span>
                <span>{{ $pembayaran['metode'] }} - {{ strtoupper($pembayaran['jenis_bayar']) }}</span>
                <span class="text-right monospace">Rp {{ number_format($pembayaran['jumlah'], 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>
        @endif
        @endfor
    </div>

    <!-- Section 2: Laporan Tagihan Non-SPP -->
    <div class="section-title">B. LAPORAN TAGIHAN NON-SPP</div>
    
    @if($dataTagihan && $dataTagihan->count() > 0)
    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Jenis Tagihan</th>
                <th width="30%">Keterangan</th>
                <th width="15%" class="text-right">Total Tagihan</th>
                <th width="15%" class="text-right">Dibayar</th>
                <th width="15%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalTagihan = 0;
                $totalDibayarTagihan = 0;
            @endphp
            @foreach($dataTagihan as $index => $tagihan)
            @php
                $totalTagihan += $tagihan->jumlah;
                $totalDibayarTagihan += $tagihan->total_dibayar;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <span class="badge badge-info">{{ $tagihan->jenis }}</span>
                </td>
                <td>{{ $tagihan->keterangan }}</td>
                <td class="text-right monospace">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</td>
                <td class="text-right monospace">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($tagihan->status_detail === 'LUNAS')
                        <span class="badge badge-success">LUNAS</span>
                    @elseif($tagihan->status_detail === 'CICILAN')
                        <span class="badge badge-warning">CICILAN</span>
                    @else
                        <span class="badge badge-secondary">BELUM</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa;">
                <td colspan="3" class="text-bold">TOTAL TAGIHAN NON-SPP</td>
                <td class="text-right text-bold monospace">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                <td class="text-right text-bold monospace">Rp {{ number_format($totalDibayarTagihan, 0, ',', '.') }}</td>
                <td class="text-center text-bold">
                    @php
                        $sisaTagihan = $totalTagihan - $totalDibayarTagihan;
                    @endphp
                    Sisa: Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
    @else
    <div style="text-align: center; padding: 20px; color: #7f8c8d; font-style: italic;">
        Tidak ada data tagihan non-SPP untuk tahun {{ $tahun }}
    </div>
    @endif

    <!-- Ringkasan Total -->
    <div style="margin-top: 25px; padding: 15px; border: 2px solid #2c3e50; border-radius: 6px; background-color: #f8f9fa;">
        <div style="text-align: center; font-weight: bold; margin-bottom: 10px; font-size: 13px; color: #2c3e50;">
            RINGKASAN TOTAL KEUANGAN
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 11px;">
            <div>
                <strong>Total SPP:</strong><br>
                Dibayar: Rp {{ number_format($totalDibayarSpp, 0, ',', '.') }}<br>
                Harus Bayar: Rp {{ number_format($totalHarusDibayarSpp, 0, ',', '.') }}<br>
                Progress: {{ number_format($persentaseSpp, 1) }}%
            </div>
            <div>
                <strong>Total Tagihan Non-SPP:</strong><br>
                Dibayar: Rp {{ number_format($totalDibayarTagihan ?? 0, 0, ',', '.') }}<br>
                Total Tagihan: Rp {{ number_format($totalTagihan ?? 0, 0, ',', '.') }}<br>
                Sisa: Rp {{ number_format(($totalTagihan ?? 0) - ($totalDibayarTagihan ?? 0), 0, ',', '.') }}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>Dokumen ini dicetak secara otomatis pada {{ $tanggalExport->format('d F Y H:i:s') }}</div>
        <div>Laporan Keuangan Siswa - {{ config('app.name', 'Laravel') }}</div>
    </div>
</body>
</html>