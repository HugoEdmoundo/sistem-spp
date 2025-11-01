<!-- resources/views/admin/murid/pembayaran.blade.php -->
@extends('layouts.app')

@section('title', 'Detail Pembayaran - ' . $murid->nama)

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800 font-weight-bold">Detail Pembayaran</h1>
            <p class="mb-0 text-muted">
                <i class="fas fa-user mr-1"></i>{{ $murid->nama }} - 
                <i class="fas fa-envelope mr-1 ml-2"></i>{{ $murid->email }}
            </p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- Tahun Filter - MODERN BADGE STYLE -->
            <div class="dropdown">
                <button class="btn btn-light border dropdown-toggle shadow-sm d-flex align-items-center py-2 px-3" 
                        type="button" id="tahunDropdown" data-bs-toggle="dropdown" 
                        aria-expanded="false" style="border-radius: 25px;">
                    <div class="d-flex align-items-center">
                        <span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2" 
                            style="width: 30px; height: 30px;">
                            <i class="fas fa-calendar" style="font-size: 0.8rem;"></i>
                        </span>
                        <div class="text-left">
                            <div class="small text-muted">Tahun Aktif</div>
                            <div class="font-weight-bold text-dark">{{ $tahun }}</div>
                        </div>
                        <i class="fas fa-chevron-down text-muted ml-3"></i>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" 
                    style="border-radius: 15px; min-width: 200px;" 
                    aria-labelledby="tahunDropdown">
                    <li class="dropdown-header small font-weight-bold text-uppercase text-muted mb-2">
                        <i class="fas fa-history mr-1"></i>Riwayat Tahun
                    </li>
                    @foreach($tahunTersedia as $tahunItem)
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2 {{ $tahunItem == $tahun ? 'bg-light' : '' }}" 
                            href="?tahun={{ $tahunItem }}">
                                <div class="d-flex align-items-center">
                                    @if($tahunItem == $tahun)
                                        <i class="fas fa-dot-circle text-primary mr-2"></i>
                                    @else
                                        <i class="far fa-circle text-muted mr-2"></i>
                                    @endif
                                    <span>Tahun {{ $tahunItem }}</span>
                                </div>
                                @if($tahunItem == date('Y'))
                                    <span class="badge badge-primary badge-pill small">Now</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <a href="{{ route('admin.murid.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Dibayar ({{ $tahun }})</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Bulan Lunas ({{ $tahun }})</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ count($statusSpp['sudah_bayar']) }} Bulan
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Belum Bayar ({{ $tahun }})</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ count($statusSpp['belum_bayar']) }} Bulan
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Transaksi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pembayaran->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status SPP Card -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Status Pembayaran SPP - Tahun {{ $tahun }}
            </h6>
            <span class="badge badge-primary">
                <i class="fas fa-filter mr-1"></i>
                Tahun {{ $tahun }}
            </span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="font-weight-bold text-success mb-3">
                        <i class="fas fa-check-circle mr-2"></i>Bulan Sudah Bayar
                        <span class="badge badge-success ml-2">{{ count($statusSpp['sudah_bayar']) }}</span>
                    </h6>
                    @if(count($statusSpp['sudah_bayar']) > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($statusSpp['sudah_bayar'] as $bulan)
                                <span class="badge badge-success badge-pill py-2 px-3">
                                    <i class="fas fa-check mr-1"></i>
                                    {{ $bulan['nama_bulan'] }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle mr-2"></i>
                            Belum ada pembayaran SPP untuk tahun {{ $tahun }}
                        </p>
                    @endif
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="font-weight-bold text-warning mb-3">
                        <i class="fas fa-clock mr-2"></i>Bulan Belum Bayar
                        <span class="badge badge-warning ml-2">{{ count($statusSpp['belum_bayar']) }}</span>
                    </h6>
                    @if(count($statusSpp['belum_bayar']) > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($statusSpp['belum_bayar'] as $bulan)
                                <span class="badge badge-warning badge-pill py-2 px-3">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $bulan['nama_bulan'] }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-success mb-0">
                            <i class="fas fa-trophy mr-2"></i>
                            Semua bulan sudah lunas di tahun {{ $tahun }}!
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Pembayaran Card -->
    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Riwayat Semua Pembayaran 
                <small class="text-muted ml-2">(Semua Tahun)</small>
            </h6>
            <div>
                <span class="badge badge-secondary">
                    Total: {{ $pembayaran->count() }} transaksi
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Tanggal</th>
                            <th>Tahun</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Metode</th>
                            <th>Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayaran as $index => $p)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $p->tanggal_upload->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($p->tahun)
                                    <span class="badge badge-info">{{ $p->tahun }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ $p->tanggal_upload->year }}</span>
                                @endif
                            </td>
                            <td>
                                @if($p->tagihan_id)
                                    <span class="badge badge-info">Tagihan</span>
                                @else
                                    <span class="badge badge-primary">SPP</span>
                                @endif
                            </td>
                            <td>{{ $p->keterangan }}</td>
                            <td class="font-weight-bold">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                            <td>
                                @if($p->status == 'accepted')
                                    <span class="badge badge-success">Diterima</span>
                                @elseif($p->status == 'rejected')
                                    <span class="badge badge-danger">Ditolak</span>
                                    @if($p->alasan_reject)
                                        <br><small class="text-muted">{{ Str::limit($p->alasan_reject, 30) }}</small>
                                    @endif
                                @else
                                    <span class="badge badge-warning">Menunggu</span>
                                @endif
                            </td>
                            <td>{{ $p->metode }}</td>
                            <td>{{ $p->admin->nama ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-receipt fa-2x mb-3"></i>
                                <p class="mb-0">Belum ada riwayat pembayaran</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.badge-pill {
    border-radius: 50rem;
}
.gap-2 {
    gap: 0.5rem;
}

/* Custom styles untuk dropdown */
.dropdown-menu {
    border: none;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.dropdown-item {
    border-radius: 8px;
    margin-bottom: 2px;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.dropdown-item.active {
    background-color: #007bff;
    border-left: 3px solid #0056b3;
}

/* Progress bar custom */
.progress {
    background-color: #e9ecef;
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
}

/* Custom button hover effects */
.btn-outline-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,123,255,0.3);
}
</style>

<script>
// DataTable initialization
$(document).ready(function() {
    $('#dataTable').DataTable({
        "pageLength": 25,
        "order": [[1, 'desc']],
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
            "infoEmpty": "Tidak ada data",
            "infoFiltered": "(disaring dari _MAX_ total data)"
        }
    });
});
</script>
@endsection