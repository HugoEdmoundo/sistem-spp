{{-- resources/views/admin/laporan/export-detail.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LAPORAN KEUANGAN {{ $tahun }}</title>
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
            vertical-align: top;
        }
        
        .student-header {
            background-color: #ecf0f1;
            padding: 10px;
            margin: 20px 0 10px 0;
            border-left: 4px solid #3498db;
            font-weight: bold;
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
        .mb-3 { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KEUANGAN SISWA</h1>
        <p class="subtitle">TAHUN AJARAN {{ $tahun }}</p>
        <p class="info">SMP NEGERI 1 CONTOH - Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>

    @foreach($dataLaporan as $dataMurid)
    <div class="student-header">
        {{ $dataMurid['murid']->nama }} - {{ $dataMurid['murid']->email }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th width="10%" class="text-center">Bulan</th>
                <th width="12%" class="text-center">Total SPP</th>
                <th width="12%" class="text-center">Total Tagihan</th>
                <th width="12%" class="text-center">Total Bulan</th>
                <th width="8%" class="text-center">Status</th>
                <th width="46%">Detail Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataMurid['bulan'] as $bulan => $dataBulan)
            <tr>
                <td class="text-center fw-bold">{{ $dataBulan['nama_bulan'] }}</td>
                <td class="text-right">{{ $dataBulan['total_spp'] > 0 ? 'Rp ' . number_format($dataBulan['total_spp'], 0, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $dataBulan['total_tagihan'] > 0 ? 'Rp ' . number_format($dataBulan['total_tagihan'], 0, ',', '.') : '-' }}</td>
                <td class="text-right fw-bold">{{ $dataBulan['total_bulan'] > 0 ? 'Rp ' . number_format($dataBulan['total_bulan'], 0, ',', '.') : '-' }}</td>
                <td class="text-center">
                    @if($dataBulan['status'] === 'LUNAS')
                        LUNAS
                    @elseif($dataBulan['status'] === 'CICILAN')
                        CICILAN
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($dataBulan['pembayaran_spp']->count() > 0 || $dataBulan['pembayaran_tagihan']->count() > 0)
                        <!-- Transaksi SPP -->
                        @foreach($dataBulan['pembayaran_spp'] as $spp)
                        <div style="margin-bottom: 3px;">
                            @php
                                $rangeBulan = $spp->bulan_mulai == $spp->bulan_akhir ? 
                                    $this->getNamaBulan($spp->bulan_mulai) : 
                                    $this->getNamaBulan($spp->bulan_mulai) . ' - ' . $this->getNamaBulan($spp->bulan_akhir);
                            @endphp
                            <strong>SPP {{ $rangeBulan }}:</strong> Rp {{ number_format($spp->jumlah, 0, ',', '.') }} 
                            ({{ $spp->metode }})
                            @if($spp->jenis_bayar === 'cicilan') - Cicilan @endif
                        </div>
                        @endforeach

                        <!-- Transaksi Tagihan -->
                        @foreach($dataBulan['pembayaran_tagihan'] as $tagihan)
                        <div style="margin-bottom: 3px;">
                            <strong>Tagihan {{ $tagihan->tagihan->jenis ?? 'Custom' }}:</strong> 
                            Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }} 
                            ({{ $tagihan->metode }})
                            @if($tagihan->jenis_bayar === 'cicilan') - Cicilan @endif
                        </div>
                        @endforeach
                    @else
                        <span style="color: #7f8c8d;">Tidak ada transaksi</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa;">
                <td class="text-center fw-bold">TOTAL</td>
                <td class="text-right fw-bold">Rp {{ number_format(collect($dataMurid['bulan'])->sum('total_spp'), 0, ',', '.') }}</td>
                <td class="text-right fw-bold">Rp {{ number_format(collect($dataMurid['bulan'])->sum('total_tagihan'), 0, ',', '.') }}</td>
                <td class="text-right fw-bold">Rp {{ number_format(collect($dataMurid['bulan'])->sum('total_bulan'), 0, ',', '.') }}</td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <div style="margin-bottom: 30px;"></div>
    @endforeach

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Informasi Keuangan Sekolah</p>
        <p>SMP Negeri 1 Contoh - Jl. Pendidikan No. 123, Kota Contoh</p>
    </div>
</body>
</html>