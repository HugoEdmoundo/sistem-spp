<!-- resources/views/admin/pembayaran/history.blade.php -->
@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-clock-history me-2"></i>Riwayat Pembayaran</h4>
        <div>
            <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-outline-warning">
                <i class="bi bi-clock me-2"></i>Menunggu Verifikasi
                @if($pembayaranPending > 0)
                <span class="badge bg-danger ms-1">{{ $pembayaranPending }}</span>
                @endif
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('admin.pembayaran.history') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Cari nama murid..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Murid</th>
                            <th>Keterangan</th>
                            <th>Jenis</th>
                            <th>Metode</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Bukti</th>
                            <th>Admin</th>
                            <th>Tanggal Proses</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayaran as $item)
                        <tr>
                            <td>{{ $loop->iteration + (($pembayaran->currentPage() - 1) * $pembayaran->perPage()) }}</td>
                            <td>
                                <strong>{{ $item->user->nama }}</strong>
                                <br>
                                <small class="text-muted">{{ $item->user->email }}</small>
                            </td>
                            <td>
                                @if($item->tagihan)
                                    <span class="badge bg-primary">Tagihan</span>
                                    {{ $item->tagihan->keterangan }}
                                @else
                                    <span class="badge bg-info">Manual</span>
                                    {{ $item->keterangan }}
                                @endif
                                @if($item->catatan_admin)
                                <br>
                                <small class="text-muted"><em>{{ $item->catatan_admin }}</em></small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $item->jenis_bayar }}</span>
                            </td>
                            <td>
                                <span class="badge bg-dark">{{ $item->metode }}</span>
                            </td>
                            <td>
                                <strong>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</strong>
                            </td>
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
                            <td>
                                @if($item->bukti)
                                <a href="{{ Storage::url($item->bukti) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->admin)
                                    {{ $item->admin->nama }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->tanggal_proses)
                                    {{ $item->tanggal_proses->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.pembayaran.show', $item->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Detail Pembayaran">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                                <p>Tidak ada riwayat pembayaran.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pembayaran->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Menampilkan {{ $pembayaran->firstItem() }} - {{ $pembayaran->lastItem() }} dari {{ $pembayaran->total() }} pembayaran
                </div>
                <div>
                    {{ $pembayaran->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Inisialisasi tooltip
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection