@extends('layouts.app')

@section('title', 'Laporan & Bukti Pembayaran')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="bg-primary rounded p-2 me-3">
                <i class="bi bi-file-earmark-text text-white fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">Laporan & Bukti Pembayaran</h4>
                <p class="text-muted mb-0">Laporan SPP dan Tagihan Saya</p>
            </div>
        </div>
    </div>

    <!-- Filter Tahun -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('murid.laporan.index') }}" class="row g-3 align-items-center">
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
                            <a href="{{ route('murid.laporan.export', $tahun) }}" 
                            class="btn btn-light btn-sm" 
                            title="Export PDF">
                                <i class="bi bi-file-earmark-pdf text-danger me-1"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($dataSpp) && !empty($dataSpp['bulan']))
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Nama Siswa</th>
                                        @for($i = 1; $i <= 12; $i++)
                                        <th class="text-center" width="7%">
                                            {{ \App\Models\User::getNamaBulanStatic($i) }}
                                        </th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">{{ $dataSpp['murid']->nama }}</td>
                                        @for($i = 1; $i <= 12; $i++)
                                        @php $bulanData = $dataSpp['bulan'][$i]; @endphp
                                        <td class="text-center">
                                            @if($bulanData['status'] === 'LUNAS')
                                                <span class="badge bg-success">LUNAS</span>
                                            @elseif($bulanData['status'] === 'CICILAN')
                                                <span class="badge bg-warning">CICILAN</span>
                                            @else
                                                <span class="badge bg-secondary">BELUM</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                Rp {{ number_format($bulanData['total_dibayar'], 0, ',', '.') }}
                                            </small>
                                            @if($bulanData['status'] !== 'BELUM')
                                            <br>
                                            <small class="text-info">
                                                dari Rp {{ number_format($dataSpp['nominal_spp'], 0, ',', '.') }}
                                            </small>
                                            @endif
                                        </td>
                                        @endfor
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Detail Pembayaran per Bulan -->
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">Detail Pembayaran per Bulan:</h6>
                            <div class="row">
                                @for($i = 1; $i <= 12; $i++)
                                @php $bulanData = $dataSpp['bulan'][$i]; @endphp
                                @if(count($bulanData['pembayaran']) > 0)
                                <div class="col-md-6 mb-3">
                                    <div class="card border">
                                        <div class="card-header py-2 bg-light">
                                            <strong>{{ \App\Models\User::getNamaBulanStatic($i) }}</strong>
                                            <span class="badge float-end 
                                                @if($bulanData['status'] === 'LUNAS') bg-success
                                                @elseif($bulanData['status'] === 'CICILAN') bg-warning
                                                @else bg-secondary @endif">
                                                {{ $bulanData['status'] }}
                                            </span>
                                        </div>
                                        <div class="card-body p-3">
                                            @foreach($bulanData['pembayaran'] as $pembayaran)
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <small class="text-muted">
                                                        {{ $pembayaran['tanggal']->format('d/m/Y') }}
                                                    </small>
                                                    <br>
                                                    <span class="fw-medium">
                                                        Rp {{ number_format($pembayaran['jumlah'], 0, ',', '.') }}
                                                    </span>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted d-block">{{ $pembayaran['metode'] }}</small>
                                                    <span class="badge bg-info">{{ $pembayaran['jenis_bayar'] }}</span>
                                                </div>
                                            </div>
                                            @if(!$loop->last)<hr class="my-2">@endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endfor
                            </div>
                        </div>
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
        <div class="col-12">
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
                            <a href="{{ route('murid.laporan.export', $tahun) }}" 
                            class="btn btn-light btn-sm" 
                            title="Export PDF">
                                <i class="bi bi-file-earmark-pdf text-danger me-1"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($dataTagihan && $dataTagihan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Jenis Tagihan</th>
                                    <th width="35%">Keterangan</th>
                                    <th width="15%" class="text-end">Total Tagihan</th>
                                    <th width="15%" class="text-end">Dibayar</th>
                                    <th width="10%" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataTagihan as $index => $tagihan)
                                <tr>
                                    <td class="fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $tagihan->jenis }}</span>
                                    </td>
                                    <td class="text-muted">{{ $tagihan->keterangan }}</td>
                                    <td class="text-end fw-bold text-dark">
                                        Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}
                                    </td>
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
                            <tfoot class="table-group-divider">
                                <tr class="fw-bold bg-light">
                                    <td colspan="3" class="text-end">TOTAL:</td>
                                    <td class="text-end text-dark">
                                        Rp {{ number_format($dataTagihan->sum('jumlah'), 0, ',', '.') }}
                                    </td>
                                    <td class="text-end text-success">
                                        Rp {{ number_format($dataTagihan->sum('total_dibayar'), 0, ',', '.') }}
                                    </td>
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
                        <h5 class="text-muted mb-2">Tidak ada data tagihan</h5>
                        <p class="text-muted mb-0">Tidak ada data tagihan non-SPP untuk tahun {{ $tahun }}.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection