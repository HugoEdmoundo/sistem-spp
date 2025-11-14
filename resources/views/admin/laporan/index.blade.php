{{-- resources/views/admin/laporan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="bg-primary rounded p-2 me-3">
                <i class="bi bi-file-earmark-text text-white fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">Laporan Keuangan</h4>
                <p class="text-muted mb-0">Laporan SPP, Tagihan, dan Pengeluaran</p>
            </div>
        </div>
        <div class="text-end">
            <small class="text-muted">{{ now()->translatedFormat('l, d F Y') }}</small>
        </div>
    </div>

    <!-- Filter Tahun -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                    <select name="tahun" class="form-select" id="tahun" required>
                        <option value="">Pilih Tahun</option>
                        @foreach($tahunUntukSelect as $tahunItem)
                            <option value="{{ $tahunItem }}" {{ $tahunItem == $tahun ? 'selected' : '' }}>
                                {{ $tahunItem }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Laporan SPP -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="bi bi-file-earmark-text me-2"></i>Laporan Pembayaran SPP
                            </h5>
                            <small class="opacity-75">Tahun {{ $tahun }} - Status Per Bulan</small>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('admin.laporan.export.spp.excel', $tahun) }}" 
                               class="btn btn-light btn-sm" 
                               title="Export Excel">
                                <i class="bi bi-file-earmark-excel text-success me-1"></i> Excel
                            </a>
                            <a href="{{ route('admin.laporan.export.spp.pdf', $tahun) }}" 
                               class="btn btn-light btn-sm" 
                               title="Export PDF">
                                <i class="bi bi-file-earmark-pdf text-danger me-1"></i> PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(is_array($dataSpp) && count($dataSpp) > 0)
                        @foreach($dataSpp as $dataMurid)
                        <div class="mb-5">
                            <!-- Header Murid -->
                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                                <div>
                                    <h6 class="mb-1 fw-bold text-primary">{{ $dataMurid['murid']->nama }}</h6>
                                    <small class="text-muted">{{ $dataMurid['murid']->email }} | {{ $dataMurid['murid']->username }}</small>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">Total SPP Tahun {{ $tahun }}</small>
                                    <div class="fw-bold text-success">
                                        Rp {{ number_format(collect($dataMurid['bulan'])->sum('total_dibayar'), 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Tabel Bulan SPP -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-4">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="10%" class="text-center">Bulan</th>
                                            <th width="15%" class="text-center">Status</th>
                                            <th width="15%" class="text-center">Total Dibayar</th>
                                            <th width="15%" class="text-center">Jenis Bayar</th>
                                            <th width="45%">Detail Pembayaran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dataMurid['bulan'] as $bulan => $dataBulan)
                                        <tr>
                                            <td class="text-center fw-semibold">
                                                {{ $dataBulan['nama_bulan'] }}
                                            </td>
                                            <td class="text-center">
                                                @if($dataBulan['status'] === 'LUNAS')
                                                    <span class="badge bg-success">LUNAS</span>
                                                @elseif($dataBulan['status'] === 'CICILAN')
                                                    <span class="badge bg-warning">CICILAN</span>
                                                @else
                                                    <span class="badge bg-secondary">BELUM BAYAR</span>
                                                @endif
                                            </td>
                                            <td class="text-center fw-bold 
                                                @if($dataBulan['total_dibayar'] > 0) text-success @else text-muted @endif">
                                                Rp {{ number_format($dataBulan['total_dibayar'], 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge 
                                                    @if($dataBulan['jenis_bayar'] === 'lunas') bg-success
                                                    @elseif($dataBulan['jenis_bayar'] === 'cicilan') bg-warning
                                                    @else bg-secondary @endif">
                                                    {{ $dataBulan['jenis_bayar'] ?? 'BELUM BAYAR' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(count($dataBulan['pembayaran']) > 0)
                                                    <div class="small">
                                                        @foreach($dataBulan['pembayaran'] as $pembayaran)
                                                        <div class="mb-1">
                                                            @php
                                                                $rangeBulan = $pembayaran->bulan_mulai == $pembayaran->bulan_akhir ? 
                                                                    $dataBulan['nama_bulan'] : 
                                                                    getNamaBulan($pembayaran->bulan_mulai) . ' - ' . getNamaBulan($pembayaran->bulan_akhir);
                                                            @endphp
                                                            <span class="badge bg-success me-1">SPP {{ $rangeBulan }}</span>
                                                            <span>Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</span>
                                                            <small class="text-muted">({{ $pembayaran->metode }})</small>
                                                            @if($pembayaran->tagihan_id)
                                                                <small class="text-info">Via Tagihan</small>
                                                            @endif
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">Belum ada pembayaran</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-group-divider">
                                        <tr class="fw-bold bg-light">
                                            <td class="text-center">TOTAL</td>
                                            <td class="text-center">
                                                @php
                                                    $bulanLunas = collect($dataMurid['bulan'])->where('status', 'LUNAS')->count();
                                                    $bulanCicilan = collect($dataMurid['bulan'])->where('status', 'CICILAN')->count();
                                                    $bulanBelum = collect($dataMurid['bulan'])->where('status', 'BELUM BAYAR')->count();
                                                @endphp
                                                <small>Lunas: {{ $bulanLunas }}, Cicilan: {{ $bulanCicilan }}, Belum: {{ $bulanBelum }}</small>
                                            </td>
                                            <td class="text-center text-success">
                                                Rp {{ number_format(collect($dataMurid['bulan'])->sum('total_dibayar'), 0, ',', '.') }}
                                            </td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <hr class="my-4">
                        @endforeach
                    @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-file-earmark-text display-1 text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted mb-2">Tidak ada data SPP</h5>
                        <p class="text-muted mb-0">Tidak ada data pembayaran SPP untuk tahun {{ $tahun }}.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Laporan Tagihan (Non-SPP) -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="bi bi-receipt me-2"></i>Laporan Tagihan (Non-SPP)
                            </h5>
                            <small class="opacity-75">Tahun {{ $tahun }}</small>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('admin.laporan.export.tagihan.excel', $tahun) }}" 
                               class="btn btn-light btn-sm" 
                               title="Export Excel">
                                <i class="bi bi-file-earmark-excel text-success me-1"></i> Excel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($dataTagihan) && $dataTagihan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tableTagihan">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="20%">Nama Siswa</th>
                                    <th width="15%">Jenis Tagihan</th>
                                    <th width="25%">Keterangan</th>
                                    <th width="10%" class="text-end">Total Tagihan</th>
                                    <th width="10%" class="text-end">Dibayar</th>
                                    <th width="10%" class="text-end">Sisa</th>
                                    <th width="5%" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataTagihan as $index => $tagihan)
                                <tr>
                                    <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-medium text-dark">{{ $tagihan->user->nama ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $tagihan->user->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $tagihan->jenis }}</span>
                                    </td>
                                    <td class="text-muted">{{ $tagihan->keterangan }}</td>
                                    <td class="text-end fw-bold text-dark">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold text-success">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold text-danger">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge 
                                            @if($tagihan->status_detail === 'LUNAS') bg-success
                                            @elseif($tagihan->status_detail === 'CICILAN') bg-warning
                                            @else bg-secondary @endif">
                                            {{ $tagihan->status_detail }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-receipt display-1 text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted mb-2">Tidak ada data tagihan</h5>
                        <p class="text-muted mb-0">Tidak ada data tagihan non-SPP untuk tahun {{ $tahun }}.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Laporan Pengeluaran -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="bi bi-receipt me-2"></i>Laporan Pengeluaran
                            </h5>
                            <small class="opacity-75">Tahun {{ $tahun }}</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-25 px-3 py-1 rounded">
                                <span class="fw-medium">Total: <strong>Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</strong></span>
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('admin.laporan.export.pengeluaran.excel', $tahun) }}" 
                                   class="btn btn-light btn-sm" 
                                   title="Export Excel">
                                    <i class="bi bi-file-earmark-excel text-success me-1"></i> Excel
                                </a>
                                <a href="{{ route('admin.laporan.export.pengeluaran.pdf', $tahun) }}" 
                                   class="btn btn-light btn-sm" 
                                   title="Export PDF">
                                    <i class="bi bi-file-earmark-pdf text-danger me-1"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($pengeluaran) && $pengeluaran->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tablePengeluaran">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="12%">Tanggal</th>
                                    <th width="15%">Kategori</th>
                                    <th width="35%">Keterangan</th>
                                    <th width="15%" class="text-end">Jumlah</th>
                                    <th width="18%">Dibuat Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengeluaran as $index => $p)
                                <tr>
                                    <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="fw-medium">{{ $p->tanggal->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $p->kategori }}</span>
                                    </td>
                                    <td class="text-muted">{{ $p->keterangan }}</td>
                                    <td class="text-end fw-bold text-danger">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($p->admin->foto)
                                                <img src="{{ Storage::url($p->admin->foto) }}" 
                                                     alt="{{ $p->admin->nama }}" 
                                                     class="rounded-circle me-2" 
                                                     width="24" 
                                                     height="24">
                                            @else
                                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                     style="width: 24px; height: 24px;">
                                                    <span class="text-white fw-bold small">{{ substr($p->admin->nama ?? 'A', 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <span class="small">{{ $p->admin->nama ?? '-' }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-group-divider">
                                <tr class="fw-bold bg-light">
                                    <td colspan="4" class="text-end">TOTAL PENGELUARAN:</td>
                                    <td class="text-end text-danger fs-6">Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-receipt display-1 text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted mb-2">Tidak ada data pengeluaran</h5>
                        <p class="text-muted mb-0">Tidak ada data pengeluaran untuk tahun {{ $tahun }}.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- @php
function getNamaBulan($bulan) {
    $bulanArr = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    return $bulanArr[$bulan] ?? 'Bulan ' . $bulan;
}
@endphp --}}