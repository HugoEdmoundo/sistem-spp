<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LAPORAN PENGELUARAN {{ $tahun }}</title>
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
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
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
        <h1>LAPORAN PENGELUARAN KEUANGAN</h1>
        <p class="subtitle">TAHUN {{ $tahun }}</p>
        <p class="info">SMP NEGERI 1 CONTOH - Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="12%">Tanggal</th>
                <th width="18%">Kategori</th>
                <th width="40%">Keterangan</th>
                <th width="15%" class="text-right">Jumlah</th>
                <th width="10%">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengeluaran as $index => $p)
            <tr>
                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                <td>{{ $p->kategori }}</td>
                <td>{{ $p->keterangan }}</td>
                <td class="text-right fw-bold" style="color: #e74c3c;">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                <td class="text-center">{{ $p->admin->nama ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa;">
                <td colspan="4" class="text-right fw-bold" style="border: 1px solid #bdc3c7; padding: 8px;">
                    TOTAL PENGELUARAN:
                </td>
                <td class="text-right fw-bold" style="border: 1px solid #bdc3c7; padding: 8px; color: #e74c3c; font-size: 11pt;">
                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #bdc3c7;"></td>
            </tr>
        </tfoot>
    </table>

    <!-- Summary by Category -->
    <div class="summary">
        <div style="font-weight: bold; margin-bottom: 10px; color: #2c3e50;">RINCIAN PENGELUARAN PER KATEGORI</div>
        @php
            $categories = $pengeluaran->groupBy('kategori')->map(function($items, $category) {
                return $items->sum('jumlah');
            });
        @endphp
        @foreach($categories as $category => $total)
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span>{{ $category }}:</span>
            <span class="fw-bold" style="color: #e74c3c;">Rp {{ number_format($total, 0, ',', '.') }}</span>
        </div>
        @endforeach
        <div style="border-top: 1px solid #ddd; margin: 8px 0; padding-top: 8px;">
            <div style="display: flex; justify-content: space-between; font-weight: bold;">
                <span>GRAND TOTAL:</span>
                <span style="color: #e74c3c;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Informasi Keuangan Sekolah</p>
        <p>SMP Negeri 1 Contoh - Jl. Pendidikan No. 123, Kota Contoh</p>
        <p>Telp: (021) 1234567 | Email: info@smpn1contoh.sch.id</p>
    </div>
</body>
</html>