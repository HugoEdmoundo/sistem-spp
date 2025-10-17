@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
            <div>
                <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-primary me-2">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i>Laporan
                </a>
                <a href="{{ route('admin.backup.index') }}" class="btn btn-outline-info">
                    <i class="bi bi-database me-2"></i>Backup
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<!-- Ganti bagian stats cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-value">{{ $totalMurid }}</div>
            <div class="stat-label">Total Murid</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card success">
            <div class="stat-icon">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="stat-value">Rp {{ number_format($totalTagihanBulanIni, 0, ',', '.') }}</div>
            <div class="stat-label">Tagihan Bulan Ini</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card info">
            <div class="stat-icon">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="stat-value">Rp {{ number_format($totalAkhir, 0, ',', '.') }}</div>
            <div class="stat-label">Total Akhir</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="bi bi-clock"></i>
            </div>
            <div class="stat-value">{{ $pembayaranPending }}</div>
            <div class="stat-label">Menunggu Verifikasi</div>
        </div>
    </div>
</div>

<!-- Tambah card untuk detail keuangan -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Ringkasan Keuangan</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="text-success">
                            <div class="h4">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</div>
                            <small>Total Pemasukan</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-danger">
                            <div class="h4">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                            <small>Total Pengeluaran</small>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <div class="h3 {{ $totalAkhir >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($totalAkhir, 0, ',', '.') }}
                    </div>
                    <small>Saldo Akhir</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Menu Cepat</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-xl-2 col-md-4 col-6">
                        <a href="{{ route('admin.murid.index') }}" class="btn btn-outline-primary w-100 h-100 py-3">
                            <i class="bi bi-people fs-2 mb-2 d-block"></i>
                            <span>Kelola Murid</span>
                        </a>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <a href="{{ route('admin.tagihan.index') }}" class="btn btn-outline-success w-100 h-100 py-3">
                            <i class="bi bi-receipt fs-2 mb-2 d-block"></i>
                            <span>Kelola Tagihan</span>
                        </a>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-outline-warning w-100 h-100 py-3 position-relative">
                            <i class="bi bi-credit-card fs-2 mb-2 d-block"></i>
                            <span>Verifikasi</span>
                            @if($pembayaranPending > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $pembayaranPending }}
                            </span>
                            @endif
                        </a>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <a href="{{ route('admin.spp-setting') }}" class="btn btn-outline-info w-100 h-100 py-3">
                            <i class="bi bi-gear fs-2 mb-2 d-block"></i>
                            <span>Setting SPP</span>
                        </a>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-dark w-100 h-100 py-3">
                            <i class="bi bi-file-earmark-spreadsheet fs-2 mb-2 d-block"></i>
                            <span>Laporan</span>
                        </a>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <a href="{{ route('admin.profile') }}" class="btn btn-outline-secondary w-100 h-100 py-3">
                            <i class="bi bi-person fs-2 mb-2 d-block"></i>
                            <span>Profile</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="material-card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Tagihan Terbaru</h5>
            </div>
            <div class="card-body">
                @php
                    $recentTagihan = \App\Models\Tagihan::with('user')
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
                                <p class="mb-1 text-muted small">{{ $tagihan->keterangan }}</p>
                                <small class="text-muted">{{ $tagihan->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</div>
                                <span class="badge bg-{{ $tagihan->status == 'success' ? 'success' : ($tagihan->status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ $tagihan->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-receipt fs-1 text-muted mb-3"></i>
                    <p class="text-muted">Belum ada tagihan.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="material-card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Statistik Bulan Ini</h5>
            </div>
            <div class="card-body">
                @php
                    $currentMonth = now()->month;
                    $currentYear = now()->year;
                    
                    $tagihanBulanIni = \App\Models\Tagihan::whereMonth('created_at', $currentMonth)
                        ->whereYear('created_at', $currentYear)
                        ->count();
                    
                    $pembayaranBulanIni = \App\Models\Pembayaran::whereMonth('tanggal_upload', $currentMonth)
                        ->whereYear('tanggal_upload', $currentYear)
                        ->count();
                    
                    $muridBaru = \App\Models\User::where('role', 'murid')
                        ->whereMonth('created_at', $currentMonth)
                        ->whereYear('created_at', $currentYear)
                        ->count();
                @endphp
                
                <div class="row text-center">
                    <div class="col-4">
                        <div class="border-end pe-3">
                            <div class="h4 text-primary mb-1">{{ $tagihanBulanIni }}</div>
                            <small class="text-muted">Tagihan</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-end pe-3">
                            <div class="h4 text-success mb-1">{{ $pembayaranBulanIni }}</div>
                            <small class="text-muted">Pembayaran</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="h4 text-info mb-1">{{ $muridBaru }}</div>
                        <small class="text-muted">Murid Baru</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="mt-3">
                    <small class="text-muted d-block mb-2">Progress Pembayaran Bulan Ini:</small>
                    @php
                        $totalTagihan = \App\Models\Tagihan::whereMonth('created_at', $currentMonth)
                            ->whereYear('created_at', $currentYear)
                            ->count();
                        $lunasTagihan = \App\Models\Tagihan::whereMonth('created_at', $currentMonth)
                            ->whereYear('created_at', $currentYear)
                            ->where('status', 'success')
                            ->count();
                        $progress = $totalTagihan > 0 ? ($lunasTagihan / $totalTagihan) * 100 : 0;
                    @endphp
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
                    </div>
                    <small class="text-muted mt-1 d-block">{{ number_format($progress, 1) }}% lunas</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection