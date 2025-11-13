<!-- resources/views/murid/rekap-spp.blade.php -->
@extends('layouts.app')

@section('title', 'Rekap SPP')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Rekap Pembayaran SPP</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('murid.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Rekap SPP</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0">Filter Tahun</h5>
            </div>
            <div class="card-body">
                <form method="GET">
                    <select name="tahun" class="form-select" onchange="this.form.submit()">
                        @foreach($tahunTersedia as $tahunItem)
                            <option value="{{ $tahunItem }}" {{ $tahun == $tahunItem ? 'selected' : '' }}>
                                {{ $tahunItem }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="material-card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Ringkasan {{ $tahun }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Lunas:</span>
                    <span class="badge bg-success">{{ count($statusSpp['sudah_lunas']) }} Bulan</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Cicilan:</span>
                    <span class="badge bg-warning">{{ count($statusSpp['masih_cicilan']) }} Bulan</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Belum Bayar:</span>
                    <span class="badge bg-danger">{{ count($statusSpp['belum_bayar']) }} Bulan</span>
                </div>
                <hr>
                <div class="text-center">
                    <small class="text-muted">Total SPP Setahun</small>
                    <h5 class="text-primary mb-0">Rp {{ number_format(count($statusSpp['semua_bulan']) * $nominalSpp, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0">Detail Bulanan {{ $tahun }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($statusSpp['semua_bulan'] as $bulan)
                    <div class="col-md-6 mb-3">
                        <div class="card border-{{ $bulan['status'] == 'paid' ? 'success' : ($bulan['status'] == 'cicilan' ? 'warning' : 'danger') }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">{{ $bulan['nama_bulan'] }}</h6>
                                    <span class="badge bg-{{ $bulan['status'] == 'paid' ? 'success' : ($bulan['status'] == 'cicilan' ? 'warning' : 'danger') }}">
                                        {{ $bulan['status'] == 'paid' ? 'LUNAS' : ($bulan['status'] == 'cicilan' ? 'CICILAN' : 'BELUM BAYAR') }}
                                    </span>
                                </div>
                                
                                <div class="mb-2">
                                    <small class="text-muted">Tagihan:</small>
                                    <div class="fw-bold">Rp {{ number_format($bulan['total_tagihan'], 0, ',', '.') }}</div>
                                </div>

                                @if($bulan['status'] != 'unpaid')
                                <div class="mb-2">
                                    <small class="text-muted">Dibayar:</small>
                                    <div class="fw-bold text-success">Rp {{ number_format($bulan['total_dibayar'], 0, ',', '.') }}</div>
                                </div>

                                @if($bulan['status'] == 'cicilan')
                                <div class="mb-2">
                                    <small class="text-muted">Sisa:</small>
                                    <div class="fw-bold text-warning">Rp {{ number_format($bulan['sisa'], 0, ',', '.') }}</div>
                                </div>

                                <div class="progress mb-2" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: {{ $bulan['persentase'] }}%"></div>
                                </div>
                                <small class="text-muted">{{ number_format($bulan['persentase'], 1) }}% terbayar</small>
                                @endif

                                @if(!empty($bulan['pembayaran']))
                                <div class="mt-2">
                                    <small class="text-muted">Riwayat:</small>
                                    @foreach($bulan['pembayaran'] as $pembayaran)
                                    <div class="d-flex justify-content-between small">
                                        <span>Rp {{ number_format($pembayaran['jumlah'], 0, ',', '.') }}</span>
                                        <span>{{ $pembayaran['tanggal']->format('d/m') }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection