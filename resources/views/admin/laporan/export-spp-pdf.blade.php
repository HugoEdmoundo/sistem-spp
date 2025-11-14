{{-- resources/views/admin/laporan/export-spp.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LAPORAN PEMBAYARAN SPP {{ $tahun }}</title>
    <style>
        /* Formal Business Style */
        @page { margin: 1.5cm; }
        body { 
            font-family: "Arial", sans-serif; 
            font-size: 10pt; 
            line-height: 1.4;
            color: #333;
        }
        
        /* Header */
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
        .header .info { 
            font-size: 9pt; 
            color: #95a5a6;
            margin: 5px 0 0 0;
        }
        
        /* Table Styling */
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
            vertical-align: top;
        }
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        /* Status Badges */
        .status-lunas { 
            background-color: #27ae60; 
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            display: inline-block;
            margin: 1px;
        }
        .status-belum { 
            background-color: #e74c3c; 
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            display: inline-block;
            margin: 1px;
        }
        .status-progress { 
            background-color: #f39c12; 
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            display: inline-block;
            margin: 1px;
        }
        
        /* Summary Section */
        .summary { 
            margin-top: 20px;
            padding: 15px;
            background-color: #ecf0f1;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        .summary-item { 
            display: inline-block; 
            margin-right: 30px;
            font-weight: bold;
        }
        .summary-value { 
            color: #2c3e50;
            font-size: 11pt;
        }
        
        /* Footer */
        .footer { 
            margin-top: 30px; 
            text-align: center; 
            font-size: 8pt; 
            color: #7f8c8d;
            border-top: 1px solid #bdc3c7;
            padding-top: 10px;
        }
        
        /* Utility Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN PEMBAYARAN SPP</h1>
        <p class="subtitle">TAHUN AJARAN {{ $tahun }}</p>
        <p class="info">SMP NEGERI 1 CONTOH - Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <!-- Data Table -->
    <table class="table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="25%">Nama Siswa</th>
                <th width="20%">Status Pembayaran</th>
                <th width="15%" class="text-center">Bulan Lunas</th>
                <th width="15%" class="text-center">Bulan Belum</th>
                <th width="20%" class="text-center">Progress</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataMurid as $index => $data)
            @php
                $bulanLunas = count($data['sudah_bayar'] ?? []);
                $bulanBelum = count($data['belum_bayar'] ?? []);
                $persentase = $bulanLunas > 0 ? ($bulanLunas / 12) * 100 : 0;
                $status = $bulanLunas == 12 ? 'LUNAS' : ($bulanLunas > 0 ? 'PROGRESS' : 'BELUM');
            @endphp
            <tr>
                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                <td>
                    <div class="fw-bold">{{ $data['murid']->nama }}</div>
                    <small style="color: #7f8c8d;">{{ $data['murid']->email }}</small>
                </td>
                <td>
                    @if($status === 'LUNAS')
                        <span class="status-lunas">LUNAS</span>
                    @elseif($status === 'PROGRESS')
                        <span class="status-progress">DALAM PROSES</span>
                    @else
                        <span class="status-belum">BELUM BAYAR</span>
                    @endif
                    <div class="mb-1" style="margin-top: 3px;">
                        @if($bulanLunas > 0)
                            <small><strong>Lunas:</strong> {{ $bulanLunas }} bulan</small>
                        @endif
                    </div>
                </td>
                <td class="text-center fw-bold" style="color: #27ae60;">{{ $bulanLunas }}</td>
                <td class="text-center fw-bold" style="color: #e74c3c;">{{ $bulanBelum }}</td>
                <td class="text-center">
                    <div style="font-weight: bold; color: #2c3e50; margin-bottom: 3px;">
                        {{ number_format($persentase, 1) }}%
                    </div>
                    <div style="background: #ecf0f1; height: 6px; border-radius: 3px;">
                        <div style="background: #3498db; height: 100%; width: {{ $persentase }}%; border-radius: 3px;"></div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    <div class="summary">
        <div style="font-weight: bold; margin-bottom: 10px; color: #2c3e50;">RINGKASAN LAPORAN</div>
        @php
            $totalSiswa = count($dataMurid);
            $totalLunas = collect($dataMurid)->sum(function($item) { return count($item['sudah_bayar'] ?? []); });
            $totalBelum = collect($dataMurid)->sum(function($item) { return count($item['belum_bayar'] ?? []); });
            $persentaseTotal = $totalSiswa > 0 ? ($totalLunas / ($totalSiswa * 12)) * 100 : 0;
        @endphp
        <div class="summary-item">
            Total Siswa: <span class="summary-value">{{ $totalSiswa }}</span>
        </div>
        <div class="summary-item">
            Bulan Lunas: <span class="summary-value" style="color: #27ae60;">{{ $totalLunas }}</span>
        </div>
        <div class="summary-item">
            Bulan Belum: <span class="summary-value" style="color: #e74c3c;">{{ $totalBelum }}</span>
        </div>
        <div class="summary-item">
            Progress: <span class="summary-value" style="color: #3498db;">{{ number_format($persentaseTotal, 1) }}%</span>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Informasi SPP</p>
        <p>SMP Negeri 1 Contoh - Jl. Pendidikan No. 123, Kota Contoh</p>
        <p>Telp: (021) 1234567 | Email: info@smpn1contoh.sch.id</p>
    </div>
</body>
</html>