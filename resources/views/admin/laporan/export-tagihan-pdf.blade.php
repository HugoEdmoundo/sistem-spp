<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LAPORAN TAGIHAN {{ $tahun }}</title>
    <style>
        @page { margin: 1.5cm; }
        body { 
            font-family: "Arial", sans-serif; 
            font-size: 10pt; 
            line-height: 1.4;
            color: #333;
        }
        
        .header { 
            text-align: center; 
            margin-bottom: 25px; 
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
        }
        .header h1 { 
            font-size: 16pt; 
            font-weight: bold; 
            margin: 0 0 5px 0;
            color: #2c3e50;
        }
        .header .subtitle { 
            font-size: 11pt; 
            color: #7f8c8d;
            margin: 0;
        }
        
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 15px 0;
            font-size: 9pt;
        }
        .table th { 
            background-color: #34495e; 
            color: white;
            font-weight: bold;
            padding: 8px 6px;
            border: 1px solid #2c3e50;
            text-align: left;
        }
        .table td { 
            padding: 6px; 
            border: 1px solid #bdc3c7;
        }
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .summary { 
            margin-top: 20px;
            padding: 15px;
            background-color: #ecf0f1;
            border-radius: 5px;
        }
        
        .footer { 
            margin-top: 30px; 
            text-align: center; 
            font-size: 8pt; 
            color: #7f8c8d;
            border-top: 1px solid #bdc3c7;
            padding-top: 10px;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN TAGIHAN (NON-SPP)</h1>
        <p class="subtitle">TAHUN {{ $tahun }}</p>
        <p class="info">SMP NEGERI 1 CONTOH - Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="20%">Nama Siswa</th>
                <th width="15%">Jenis Tagihan</th>
                <th width="25%">Keterangan</th>
                <th width="10%" class="text-right">Total Tagihan</th>
                <th width="10%" class="text-right">Dibayar</th>
                <th width="10%" class="text-right">Sisa</th>
                <th width="5%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataTagihan as $index => $tagihan)
            <tr>
                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                <td>{{ $tagihan->user->nama ?? 'N/A' }}</td>
                <td>{{ $tagihan->jenis }}</td>
                <td>{{ $tagihan->keterangan }}</td>
                <td class="text-right">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($tagihan->status_detail === 'LUNAS')
                        <span style="color: #27ae60; font-weight: bold;">LUNAS</span>
                    @elseif($tagihan->status_detail === 'CICILAN')
                        <span style="color: #f39c12; font-weight: bold;">CICILAN</span>
                    @else
                        <span style="color: #e74c3c; font-weight: bold;">BELUM</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div style="font-weight: bold; margin-bottom: 10px; color: #2c3e50;">RINGKASAN LAPORAN</div>
        @php
            $totalTagihan = $dataTagihan->sum('jumlah');
            $totalDibayar = $dataTagihan->sum('total_dibayar');
            $totalSisa = $dataTagihan->sum('sisa_tagihan');
            $persentase = $totalTagihan > 0 ? ($totalDibayar / $totalTagihan) * 100 : 0;
        @endphp
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span>Total Tagihan:</span>
            <span class="fw-bold">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span>Total Dibayar:</span>
            <span class="fw-bold" style="color: #27ae60;">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span>Total Sisa:</span>
            <span class="fw-bold" style="color: #e74c3c;">Rp {{ number_format($totalSisa, 0, ',', '.') }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; font-weight: bold; border-top: 1px solid #ddd; padding-top: 8px;">
            <span>Progress Keseluruhan:</span>
            <span style="color: #3498db;">{{ number_format($persentase, 1) }}%</span>
        </div>
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Informasi Keuangan Sekolah</p>
        <p>SMP Negeri 1 Contoh - Jl. Pendidikan No. 123, Kota Contoh</p>
    </div>
</body>
</html>