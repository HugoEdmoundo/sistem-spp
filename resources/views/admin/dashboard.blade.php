<!-- resources/views/murid/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Murid')

@section('content')
<div class="row">
    <!-- Statistik -->
    <div class="col-md-3 mb-4">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h4>
                        <p class="mb-0">Total Tagihan</p>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card stat-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</h4>
                        <p class="mb-0">Total Dibayar</p>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-credit-card"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card stat-card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $tagihanPending }}</h4>
                        <p class="mb-0">Tagihan Pending</p>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card stat-card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $tagihanRejected }}</h4>
                        <p class="mb-0">Pembayaran Ditolak</p>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notifikasi Penting -->
@if($pembayaranPending->count() > 0 || $pembayaranRejected->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-bell-fill me-2"></i>Notifikasi Penting
                </h5>
            </div>
            <div class="card-body">
                <!-- Pembayaran Pending -->
                @if($pembayaranPending->count() > 0)
                <div class="alert alert-info mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-clock-history me-2"></i>
                            <strong>Anda memiliki {{ $pembayaranPending->count() }} pembayaran menunggu verifikasi</strong>
                        </div>
                        <a href="{{ route('murid.pembayaran.history') }}" class="btn btn-sm btn-outline-info">
                            Lihat Detail
                        </a>
                    </div>
                </div>
                @endif

                <!-- Pembayaran Ditolak -->
                @if($pembayaranRejected->count() > 0)
                <div class="alert alert-danger">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Anda memiliki {{ $pembayaranRejected->count() }} pembayaran ditolak</strong>
                            <p class="mb-0 mt-1">Silakan upload ulang bukti pembayaran</p>
                        </div>
                        <a href="{{ route('murid.pembayaran.history') }}" class="btn btn-sm btn-outline-danger">
                            Upload Ulang
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Konten lainnya tetap sama -->
<div class="row">
    <!-- Tagihan Terbaru -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-receipt me-2"></i>Tagihan Terbaru
                </h5>
            </div>
            <div class="card-body">
                @forelse($tagihan as $item)
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <div>
                        <h6 class="mb-1">{{ $item->keterangan }}</h6>
                        <small class="text-muted">{{ $item->created_at->format('d M Y') }}</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-primary">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</div>
                        <span class="badge bg-{{ $item->status == 'success' ? 'success' : ($item->status == 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">Tidak ada tagihan</p>
                @endforelse
                
                @if($tagihan->count() > 0)
                <div class="text-center mt-3">
                    <a href="{{ route('murid.tagihan.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua Tagihan
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Riwayat Pembayaran Terbaru -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Riwayat Pembayaran Terbaru
                </h5>
            </div>
            <div class="card-body">
                @forelse($riwayatPembayaran as $item)
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <div>
                        <h6 class="mb-1">{{ $item->keterangan }}</h6>
                        <small class="text-muted">{{ $item->tanggal_upload->format('d M Y') }}</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-success">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</div>
                        <span class="badge bg-{{ $item->status == 'accepted' ? 'success' : ($item->status == 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">Tidak ada riwayat pembayaran</p>
                @endforelse
                
                @if($riwayatPembayaran->count() > 0)
                <div class="text-center mt-3">
                    <a href="{{ route('murid.pembayaran.history') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua Riwayat
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection