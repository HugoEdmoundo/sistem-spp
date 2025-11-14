<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">Dashboard Admin</h4>
            <p class="text-muted mb-0">Ringkasan statistik sistem pembayaran SPP</p>
        </div>
        <div class="text-end">
            <small class="text-muted">{{ now()->translatedFormat('l, d F Y') }}</small>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <!-- Total Murid -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-hover border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-1">Total Murid Aktif</span>
                            <h3 class="mb-0 fw-bold text-primary">{{ $totalMurid }}</h3>
                            <small class="text-muted">Murid terdaftar</small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-primary bg-opacity-10 rounded-circle">
                                    <i class="bi bi-people-fill text-primary fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Tagihan Belum Dibayar -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-hover border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-1">Total Tagihan</span>
                            <h3 class="mb-0 fw-bold text-danger">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h3>
                            <small class="text-muted">Belum dibayar</small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-danger bg-opacity-10 rounded-circle">
                                    <i class="bi bi-receipt text-danger fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pembayaran Diterima -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-hover border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-1">Pembayaran Diterima</span>
                            <h3 class="mb-0 fw-bold text-success">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</h3>
                            <small class="text-muted">Total diterima</small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-success bg-opacity-10 rounded-circle">
                                    <i class="bi bi-cash-coin text-success fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pembayaran Pending -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-hover border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-1">Menunggu Verifikasi</span>
                            <h3 class="mb-0 fw-bold text-warning">{{ $pembayaranPending }}</h3>
                            <small class="text-muted">Pembayaran pending</small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-warning bg-opacity-10 rounded-circle">
                                    <i class="bi bi-clock text-warning fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Stats -->
    <div class="row">
        <!-- Tagihan Bulan Ini -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-hover border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-1">Tagihan Bulan Ini</span>
                            <h4 class="mb-0 fw-bold text-info">Rp {{ number_format($totalTagihanBulanIni, 0, ',', '.') }}</h4>
                            <small class="text-muted">{{ now()->translatedFormat('F Y') }}</small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-info bg-opacity-10 rounded-circle">
                                    <i class="bi bi-calendar-month text-info fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pengeluaran -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-hover border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-1">Total Pengeluaran</span>
                            <h4 class="mb-0 fw-bold text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
                            <small class="text-muted">Biaya operasional</small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-danger bg-opacity-10 rounded-circle">
                                    <i class="bi bi-arrow-up-right text-danger fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saldo Akhir -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-hover border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-1">Saldo Akhir</span>
                            <h4 class="mb-0 fw-bold {{ $totalAkhir >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($totalAkhir, 0, ',', '.') }}
                            </h4>
                            <small class="text-muted">Pendapatan bersih</small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title {{ $totalAkhir >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 rounded-circle">
                                    <i class="bi bi-wallet2 {{ $totalAkhir >= 0 ? 'text-success' : 'text-danger' }} fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Recent Activity -->
    <div class="row">
        <!-- Recent Pembayaran -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        Pembayaran Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $recentPembayaran = \App\Models\Pembayaran::with(['user', 'tagihan'])
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @if($recentPembayaran->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentPembayaran as $pembayaran)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $pembayaran->user->nama }}</h6>
                                    <small class="text-muted">
                                        {{ $pembayaran->tagihan ? $pembayaran->tagihan->keterangan : $pembayaran->keterangan }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">{{ $pembayaran->jumlah_formatted }}</div>
                                    <span class="badge bg-{{ $pembayaran->status == 'accepted' ? 'success' : ($pembayaran->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ $pembayaran->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                        <p class="text-muted mb-0">Belum ada pembayaran</p>
                    </div>
                    @endif
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.pembayaran.history') }}" class="btn btn-outline-primary btn-sm">
                            Lihat Semua Pembayaran
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Tagihan -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="bi bi-receipt text-primary me-2"></i>
                        Tagihan Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $recentTagihan = \App\Models\Tagihan::with(['user'])
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @if($recentTagihan->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentTagihan as $tagihan)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $tagihan->user->nama }}</h6>
                                    <small class="text-muted">{{ $tagihan->keterangan }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">{{ $tagihan->jumlah_formatted }}</div>
                                    <span class="badge bg-{{ $tagihan->status == 'success' ? 'success' : ($tagihan->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ $tagihan->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                        <p class="text-muted mb-0">Belum ada tagihan</p>
                    </div>
                    @endif
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.tagihan.index') }}" class="btn btn-outline-primary btn-sm">
                            Lihat Semua Tagihan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection