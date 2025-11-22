<!-- resources/views/murid/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Murid')

@section('content')
<!-- Statistik Cards - Mobile First -->
<div class="row mb-4">
    <!-- Total Tagihan -->
    <div class="col-6 col-md-3 mb-3">
        <div class="card card-hover border-primary h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted small d-block">Total Tagihan</span>
                        <h6 class="mb-0 fw-bold">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h6>
                        <small class="text-muted small">
                            {{ $tagihanUnpaidCount }} belum + {{ $tagihanPartialCount }} cicilan
                        </small>
                    </div>
                    <div class="flex-shrink-0 ms-2">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary rounded-circle p-2">
                                <i class="bi bi-receipt fs-6"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total Dibayar -->
    <div class="col-6 col-md-3 mb-3">
        <div class="card card-hover border-success h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted small d-block">Total Dibayar</span>
                        <h6 class="mb-0 fw-bold">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</h6>
                        <small class="text-muted small">
                            {{ $totalSppDibayarFormatted ?? 'Rp 0' }} SPP
                        </small>
                    </div>
                    <div class="flex-shrink-0 ms-2">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success rounded-circle p-2">
                                <i class="bi bi-cash-coin fs-6"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Menunggu Verifikasi -->
    <div class="col-6 col-md-3 mb-3">
        <div class="card card-hover border-warning h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted small d-block">Menunggu Verifikasi</span>
                        <h6 class="mb-0 fw-bold">{{ $pembayaranPendingCount }}</h6>
                        <small class="text-muted small">pembayaran</small>
                    </div>
                    <div class="flex-shrink-0 ms-2">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning rounded-circle p-2">
                                <i class="bi bi-clock fs-6"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Perlu Tindakan -->
    <div class="col-6 col-md-3 mb-3">
        <div class="card card-hover border-danger h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted small d-block">Perlu Tindakan</span>
                        <h6 class="mb-0 fw-bold">{{ $totalNotifikasi }}</h6>
                        <small class="text-muted small">item</small>
                    </div>
                    <div class="flex-shrink-0 ms-2">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-danger rounded-circle p-2">
                                <i class="bi bi-exclamation-triangle fs-6"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status SPP Tahun Ini -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-calendar-check me-2"></i>
                        Status SPP {{ date('Y') }}
                    </h6>
                    <span class="badge bg-light text-info">
                        Total Dibayar: {{ $totalSppDibayarFormatted ?? 'Rp 0' }}
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                @if(isset($statusSppTahunIni) && !empty($statusSppTahunIni['semua_bulan']))
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                @foreach($statusSppTahunIni['semua_bulan'] as $bulan)
                                <th class="text-center small p-2" style="width: 8.33%">
                                    {{ substr($bulan['nama_bulan'], 0, 3) }}
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach($statusSppTahunIni['semua_bulan'] as $bulan)
                                <td class="text-center p-2">
                                    @if($bulan['status'] === 'paid')
                                    <i class="bi bi-check-circle-fill text-success fs-5" title="LUNAS"></i>
                                    @elseif($bulan['status'] === 'cicilan')
                                    <i class="bi bi-arrow-repeat text-warning fs-5" title="CICILAN"></i>
                                    @else
                                    <i class="bi bi-x-circle text-secondary fs-5" title="BELUM"></i>
                                    @endif
                                    <br>
                                    <small class="text-muted d-block">
                                        Rp {{ number_format($bulan['total_dibayar'], 0, ',', '.') }}
                                    </small>
                                </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Legend -->
                <div class="p-3 border-top">
                    <div class="row text-center">
                        <div class="col-4">
                            <i class="bi bi-check-circle-fill text-success me-1"></i>
                            <small>Lunas</small>
                        </div>
                        <div class="col-4">
                            <i class="bi bi-arrow-repeat text-warning me-1"></i>
                            <small>Cicilan</small>
                        </div>
                        <div class="col-4">
                            <i class="bi bi-x-circle text-secondary me-1"></i>
                            <small>Belum</small>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-calendar-x text-muted fs-1"></i>
                    <p class="text-muted mb-0 mt-2">Tidak ada data SPP untuk tahun ini</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Notifikasi & Peringatan - PERBAIKAN LOGIKA PERLU TINDAKAN -->
@php
    // PERBAIKAN: Hitung yang benar-benar perlu tindakan
    $perluTindakanItems = [];
    
    // Tagihan unpaid perlu tindakan
    if($tagihanUnpaidCount > 0) {
        $perluTindakanItems[] = [
            'type' => 'tagihan_unpaid',
            'count' => $tagihanUnpaidCount,
            'title' => 'Tagihan Belum Dibayar',
            'message' => "Anda memiliki {$tagihanUnpaidCount} tagihan yang belum dibayar",
            'icon' => 'bi-x-circle',
            'color' => 'danger',
            'action' => route('murid.tagihan.index'),
            'action_text' => 'Bayar'
        ];
    }
    
    // Tagihan partial perlu tindakan
    if($tagihanPartialCount > 0) {
        $perluTindakanItems[] = [
            'type' => 'tagihan_partial',
            'count' => $tagihanPartialCount,
            'title' => 'Tagihan Masih Cicilan',
            'message' => "Anda memiliki {$tagihanPartialCount} tagihan yang masih dalam proses cicilan",
            'icon' => 'bi-arrow-repeat',
            'color' => 'warning',
            'action' => route('murid.tagihan.index'),
            'action_text' => 'Lanjutkan'
        ];
    }
    
    // Pembayaran pending perlu monitoring
    if($pembayaranPendingCount > 0) {
        $perluTindakanItems[] = [
            'type' => 'pembayaran_pending',
            'count' => $pembayaranPendingCount,
            'title' => 'Menunggu Verifikasi',
            'message' => "Anda memiliki {$pembayaranPendingCount} pembayaran yang sedang diverifikasi",
            'icon' => 'bi-clock',
            'color' => 'info',
            'action' => route('murid.pembayaran.history'),
            'action_text' => 'Lihat'
        ];
    }
    
    // Pembayaran rejected perlu tindakan
    if($pembayaranRejectedCount > 0) {
        $perluTindakanItems[] = [
            'type' => 'pembayaran_rejected',
            'count' => $pembayaranRejectedCount,
            'title' => 'Pembayaran Ditolak',
            'message' => "Anda memiliki {$pembayaranRejectedCount} pembayaran yang ditolak",
            'icon' => 'bi-x-octagon',
            'color' => 'danger',
            'action' => route('murid.pembayaran.history'),
            'action_text' => 'Upload Ulang'
        ];
    }
@endphp

@if(count($perluTindakanItems) > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-white py-2">
                <h6 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Perlu Tindakan ({{ count($perluTindakanItems) }})
                </h6>
            </div>
            <div class="card-body p-0">
                @foreach($perluTindakanItems as $item)
                <div class="border-bottom p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="{{ $item['icon'] }} text-{{ $item['color'] }} fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">{{ $item['title'] }}</h6>
                            <p class="mb-0 small text-muted">{{ $item['message'] }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ $item['action'] }}" class="btn btn-sm btn-outline-{{ $item['color'] }}">
                                <i class="bi {{ $item['icon'] }} me-1"></i>
                                {{ $item['action_text'] }}
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
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
            <div class="card-header bg-primary text-white py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-arrow-repeat me-2"></i>
                        Tagihan Cicilan Aktif ({{ $tagihanPartial->count() }})
                    </h6>
                    <a href="{{ route('murid.tagihan.index') }}" class="btn btn-sm btn-light">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($tagihanPartial as $tagihan)
                    @php
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
                        $id = $getValue('id');
                    @endphp
                    
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 border-{{ $jenis == 'spp' ? 'info' : 'warning' }} shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0 text-truncate" title="{{ $keterangan ?? 'Tagihan' }}">
                                        {{ Str::limit($keterangan ?? 'Tagihan', 30) }}
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

                                <div class="row small text-center g-1 mb-3">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Total</small>
                                        <div class="fw-bold text-dark small">Rp {{ number_format($jumlah ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Dibayar</small>
                                        <div class="fw-bold text-success small">Rp {{ number_format($total_dibayar ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Sisa</small>
                                        <div class="fw-bold text-warning small">Rp {{ number_format($sisa_tagihan ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <a href="{{ route('murid.tagihan.index') }}" 
                                       class="btn btn-sm btn-{{ $jenis == 'spp' ? 'info' : 'warning' }}">
                                        <i class="bi bi-credit-card me-1"></i>
                                        Lanjutkan Cicilan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<style>
.card-hover {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.avatar-sm {
    width: 40px;
    height: 40px;
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
/* Mobile optimizations */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    .btn {
        font-size: 0.875rem;
    }
    .table th, .table td {
        padding: 0.5rem;
        font-size: 0.875rem;
    }
}
</style>
@endsection