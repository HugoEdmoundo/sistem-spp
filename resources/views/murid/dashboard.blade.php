<!-- resources/views/murid/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Murid')

@section('content')
<!-- Statistik Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card card-hover border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-1">Total Tagihan</span>
                        <h4 class="mb-0">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h4>
                        <small class="text-muted">
                            {{ $tagihanUnpaidCount }} belum bayar + {{ $tagihanPartialCount }} cicilan
                        </small>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary rounded-circle">
                                <i class="bi bi-receipt fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card card-hover border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-1">Total Dibayar</span>
                        <h4 class="mb-0">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="bi bi-cash-coin fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card card-hover border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-1">Menunggu Verifikasi</span>
                        <h4 class="mb-0">{{ $pembayaranPendingCount }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning rounded-circle">
                                <i class="bi bi-clock fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card card-hover border-danger">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-1">Perlu Tindakan</span>
                        <h4 class="mb-0">{{ $totalNotifikasi }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-danger rounded-circle">
                                <i class="bi bi-exclamation-triangle fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notifikasi & Peringatan -->
@php
    $perluTindakan = $tagihanUnpaidCount + $tagihanPartialCount + $pembayaranPendingCount + $pembayaranRejectedCount;
@endphp

@if($perluTindakan > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Perlu Tindakan ({{ $perluTindakan }})
                </h5>
            </div>
            <div class="card-body">
                @if($tagihanUnpaidCount > 0)
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="bi bi-x-circle me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Tagihan Belum Dibayar</h6>
                        <p class="mb-0">Anda memiliki <strong>{{ $tagihanUnpaidCount }} tagihan</strong> yang belum dibayar.</p>
                    </div>
                    <a href="{{ route('murid.tagihan.index') }}" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-credit-card me-1"></i>Bayar
                    </a>
                </div>
                @endif

                @if($tagihanPartialCount > 0)
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="bi bi-arrow-repeat me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Tagihan Masih Cicilan</h6>
                        <p class="mb-0">Anda memiliki <strong>{{ $tagihanPartialCount }} tagihan</strong> yang masih dalam proses cicilan.</p>
                    </div>
                    <a href="{{ route('murid.tagihan.index') }}" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-arrow-repeat me-1"></i>Lanjutkan
                    </a>
                </div>
                @endif

                @if($pembayaranPendingCount > 0)
                <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-clock me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Menunggu Verifikasi</h6>
                        <p class="mb-0">Anda memiliki <strong>{{ $pembayaranPendingCount }} pembayaran</strong> yang sedang diverifikasi admin.</p>
                    </div>
                    <a href="{{ route('murid.pembayaran.history') }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-eye me-1"></i>Lihat
                    </a>
                </div>
                @endif

                @if($pembayaranRejectedCount > 0)
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="bi bi-x-octagon me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Pembayaran Ditolak</h6>
                        <p class="mb-0">Anda memiliki <strong>{{ $pembayaranRejectedCount }} pembayaran</strong> yang ditolak.</p>
                    </div>
                    <a href="{{ route('murid.pembayaran.history') }}" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-upload me-1"></i>Upload Ulang
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Tagihan Cicilan Aktif -->
@if($tagihanPartial->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-arrow-repeat me-2"></i>
                    Tagihan Cicilan Aktif ({{ $tagihanPartial->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($tagihanPartial as $tagihan)
                    @php
                        // ⭐⭐ HELPER FUNCTION UNTUK AMBIL VALUE DARI ARRAY/OBJECT ⭐⭐
                        $getValue = function($key) use ($tagihan) {
                            if (is_array($tagihan)) {
                                return $tagihan[$key] ?? null;
                            } else {
                                return $tagihan->$key ?? null;
                            }
                        };
                        
                        $jenis = $getValue('jenis');
                        $keterangan = $getValue('keterangan');
                        $jumlah = $getValue('jumlah');
                        $total_dibayar = $getValue('total_dibayar');
                        $sisa_tagihan = $getValue('sisa_tagihan');
                        $persentase_dibayar = $getValue('persentase_dibayar');
                    @endphp
                    
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100 border-{{ $jenis == 'spp' ? 'info' : 'warning' }} shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0 text-truncate" title="{{ $keterangan ?? 'Tagihan' }}">
                                        {{ Str::limit($keterangan ?? 'Tagihan', 40) }}
                                    </h6>
                                    <span class="badge bg-{{ $jenis == 'spp' ? 'info' : 'warning' }}">
                                        {{ $jenis == 'spp' ? 'SPP' : 'Tagihan' }}
                                    </span>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">Progress</small>
                                        <small class="fw-bold text-primary">{{ number_format($persentase_dibayar ?? 0, 1) }}%</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" 
                                             style="width: {{ number_format($persentase_dibayar ?? 0, 1) }}%">
                                        </div>
                                    </div>
                                </div>

                                <div class="row small text-center">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Total</small>
                                        <div class="fw-bold text-dark">Rp {{ number_format($jumlah ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Dibayar</small>
                                        <div class="fw-bold text-success">Rp {{ number_format($total_dibayar ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Sisa</small>
                                        <div class="fw-bold text-warning">Rp {{ number_format($sisa_tagihan ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <a href="{{ route('murid.tagihan.index') }}" 
                                       class="btn btn-sm btn-{{ $jenis == 'spp' ? 'info' : 'warning' }} w-100">
                                        <i class="bi bi-credit-card me-1"></i>
                                        Lanjutkan Cicilan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @if($tagihanPartial->count() > 3)
                <div class="text-center mt-3">
                    <a href="{{ route('murid.tagihan.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-list-ul me-1"></i>
                        Lihat Semua Cicilan
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Sisa view tetap sama seperti sebelumnya -->
<!-- ... (Pembayaran Pending & Ditolak, Riwayat Pembayaran, Tagihan Terbaru) ... -->

<style>
.card-hover {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.avatar-sm {
    width: 50px;
    height: 50px;
}
.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}
.progress {
    border-radius: 10px;
}
.progress-bar {
    border-radius: 10px;
}
</style>
@endsection