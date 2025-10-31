<!-- resources/views/admin/pembayaran/history.blade.php -->
@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>Riwayat Pembayaran
                </h4>
                <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-warning">
                    <i class="bi bi-clock me-2"></i>Menunggu Verifikasi
                    @if($pembayaranPending > 0)
                    <span class="badge bg-danger ms-1">{{ $pembayaranPending }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Notification -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form action="{{ route('admin.pembayaran.history') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Cari nama murid..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                </div>
                <div class="col-md-6 text-end text-muted small">
                    Total: <strong>{{ $pembayaran->total() }}</strong> pembayaran
                </div>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Murid</th>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayaran as $item)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration + (($pembayaran->currentPage() - 1) * $pembayaran->perPage()) }}</td>
                            
                            <!-- Murid Info -->
                            <td>
                                <div class="fw-semibold">{{ $item->user->nama }}</div>
                                <small class="text-muted">{{ $item->user->email }}</small>
                            </td>
                            
                            <!-- Keterangan -->
                            <td>
                                <div class="fw-medium">{{ $item->tagihan ? $item->tagihan->keterangan : $item->keterangan }}</div>
                                <div class="small text-muted">
                                    {{ $item->jenis_bayar }} • {{ $item->metode }}
                                    @if($item->range_bulan)
                                    • {{ $item->range_bulan }}
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Jumlah -->
                            <td class="fw-bold text-nowrap text-primary">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                            
                            <!-- Status -->
                            <td>
                                @if($item->status == 'accepted')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Diterima
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Ditolak
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Tanggal -->
                            <td class="text-muted small">
                                {{ $item->tanggal_proses ? $item->tanggal_proses->format('d/m/Y H:i') : '-' }}
                            </td>
                            
                            <!-- Actions -->
                            <td>
                                <div class="btn-group">
                                    <!-- Detail Button -->
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailModal{{ $item->id }}"
                                            title="Detail Pembayaran">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-inbox display-4 text-muted opacity-50 mb-3"></i>
                                    <h5 class="text-muted">Tidak ada riwayat pembayaran</h5>
                                    <p class="text-muted mb-0">Semua pembayaran yang telah diproses akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($pembayaran->hasPages())
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Menampilkan <strong>{{ $pembayaran->firstItem() ?? 0 }}</strong> - 
                    <strong>{{ $pembayaran->lastItem() ?? 0 }}</strong> dari 
                    <strong>{{ $pembayaran->total() }}</strong> pembayaran
                </div>
                <div>
                    {{ $pembayaran->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modals for Details -->
@foreach($pembayaran as $item)
<!-- Detail Modal - Medium Size -->
<div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-receipt me-2"></i>Detail Pembayaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Header Info -->
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h6 class="mb-1">{{ $item->user->nama }}</h6>
                        <small class="text-muted">{{ $item->user->email }}</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-primary fs-5">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</div>
                        @if($item->status == 'accepted')
                            <span class="badge bg-success">Diterima</span>
                        @else
                            <span class="badge bg-danger">Ditolak</span>
                        @endif
                    </div>
                </div>

                <!-- Informasi Utama -->
                <div class="row g-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Jenis Pembayaran</small>
                        <div class="fw-medium">{{ $item->jenis_bayar }}</div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Metode</small>
                        <div class="fw-medium">{{ $item->metode }}</div>
                    </div>
                    
                    <div class="col-12">
                        <small class="text-muted d-block">Keterangan</small>
                        <div class="fw-medium">{{ $item->tagihan ? $item->tagihan->keterangan : $item->keterangan }}</div>
                    </div>

                    @if($item->range_bulan)
                    <div class="col-12">
                        <small class="text-muted d-block">Periode</small>
                        <div class="fw-medium">{{ $item->range_bulan }}</div>
                    </div>
                    @endif

                    <div class="col-6">
                        <small class="text-muted d-block">Admin</small>
                        <div>{{ $item->admin ? $item->admin->nama : '-' }}</div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Tanggal Proses</small>
                        <div>{{ $item->tanggal_proses ? $item->tanggal_proses->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                </div>

                <!-- Alasan Penolakan -->
                @if($item->status == 'rejected' && $item->alasan_reject)
                <div class="mt-4 pt-3 border-top">
                    <h6 class="text-danger mb-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>Alasan Penolakan
                    </h6>
                    <div class="alert alert-light border border-danger border-opacity-25">
                        <p class="mb-0">{{ $item->alasan_reject }}</p>
                    </div>
                </div>
                @endif

                <!-- Bukti Pembayaran -->
                @if($item->bukti)
                <div class="mt-4 pt-3 border-top">
                    <small class="text-muted d-block mb-2">Bukti Pembayaran</small>
                    <a href="{{ Storage::url($item->bukti) }}" target="_blank" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-image me-1"></i>Lihat Bukti
                    </a>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                @if($item->status == 'accepted')
                <a href="{{ route('admin.kuitansi.pdf', $item->id) }}" 
                   class="btn btn-success btn-sm" target="_blank">
                    <i class="bi bi-download me-1"></i>Download Kuitansi
                </a>
                @endif
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>

<style>
.table > :not(caption) > * > * {
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
}
.badge {
    font-weight: 500;
    font-size: 0.75rem;
}
</style>
@endsection