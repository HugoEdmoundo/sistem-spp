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
        <div>
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
                                Total Dibayar</div>
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

    <!-- Debug Info -->
<div class="alert alert-info">
    <h6>Debug Information</h6>
    <p><strong>Total Pembayaran SPP Diterima:</strong> {{ $pembayaran->where('status', 'accepted')->whereNull('tagihan_id')->count() }}</p>
    <p><strong>Bulan Terdeteksi Sudah Bayar:</strong> {{ count($statusSpp['sudah_bayar']) }}</p>
    <p><strong>Bulan Terdeteksi Belum Bayar:</strong> {{ count($statusSpp['belum_bayar']) }}</p>
    
    @if(count($statusSpp['sudah_bayar']) > 0)
    <div class="mt-2">
        <strong>Detail Bulan Sudah Bayar:</strong>
        <ul>
            @foreach($statusSpp['sudah_bayar'] as $bulan)
            <li>
                {{ $bulan['nama_bulan'] }} - 
                Rp {{ number_format($bulan['jumlah'], 0, ',', '.') }} - 
                {{ $bulan['metode'] ?? 'unknown' }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>

    <!-- Status SPP Card -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Status Pembayaran SPP - Tahun {{ $tahun }}
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="font-weight-bold text-success mb-3">
                        <i class="fas fa-check-circle mr-2"></i>Bulan Sudah Bayar
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
                            Semua bulan sudah lunas!
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Pembayaran Card -->
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Semua Pembayaran</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Tanggal</th>
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
                            <td colspan="8" class="text-center text-muted py-4">
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
</style>

<script>
// DataTable initialization
$(document).ready(function() {
    $('#dataTable').DataTable({
        "pageLength": 25,
        "order": [[1, 'desc']]
    });
});
</script>
@endsection