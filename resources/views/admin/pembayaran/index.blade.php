<!-- resources/views/admin/pembayaran/index.blade.php -->
@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-clock-history me-2"></i>Verifikasi Pembayaran
            </h1>
            <p class="text-muted">Kelola dan verifikasi pembayaran yang menunggu persetujuan</p>
        </div>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>
                    <strong>Berhasil!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>
                    <strong>Error!</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->

    <!-- Main Card -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-list-ul me-2"></i>Daftar Pembayaran Menunggu Verifikasi
                </h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($pembayaran->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Siswa</th>
                                <th>Jenis</th>
                                <th>Keterangan</th>
                                <th>Metode</th>
                                <th>Jumlah</th>
                                <th>Tanggal Upload</th>
                                <th>Bukti</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pembayaran as $index => $item)
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <span class="text-white fw-bold">
                                                {{ substr($item->user->nama, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong class="d-block">{{ $item->user->nama }}</strong>
                                            <small class="text-muted">NIS: {{ $item->user->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($item->tagihan_id)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">
                                            <i class="bi bi-receipt me-1"></i>Tagihan
                                        </span>
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                            <i class="bi bi-wallet me-1"></i>SPP
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="max-width-200">
                                        @if($item->tagihan_id)
                                            <strong>{{ $item->tagihan->keterangan ?? 'Tagihan' }}</strong>
                                        @else
                                            <strong>{{ $item->keterangan }}</strong>
                                            @if($item->bulan_mulai && $item->bulan_akhir)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    {{ \App\Models\User::getNamaBulanStatic($item->bulan_mulai) }} - 
                                                    {{ \App\Models\User::getNamaBulanStatic($item->bulan_akhir) }} 
                                                    {{ $item->tahun }}
                                                </small>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                        {{ $item->metode }}
                                    </span>
                                </td>
                                <td class="fw-bold text-success">
                                    Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>{{ $item->tanggal_upload->format('d/m/Y') }}<br>
                                        <i class="bi bi-clock me-1"></i>{{ $item->tanggal_upload->format('H:i') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    @if($item->bukti)
                                    <a href="{{ asset('storage/' . $item->bukti) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip" 
                                       title="Lihat Bukti Pembayaran">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>
                                    @else
                                    <span class="badge bg-warning">Tidak Ada</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- Tombol Terima -->
                                        <form action="{{ route('admin.pembayaran.approve', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-success"
                                                    data-bs-toggle="tooltip"
                                                    title="Terima Pembayaran"
                                                    onclick="return confirm('Apakah Anda yakin ingin menerima pembayaran ini?')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>

                                        <!-- Tombol Detail -->
                                        <button type="button" 
                                                class="btn btn-info text-white"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailModal{{ $item->id }}"
                                                data-bs-toggle="tooltip"
                                                title="Lihat Detail">
                                            <i class="bi bi-info-circle"></i>
                                        </button>

                                        <!-- Tombol Tolak -->
                                        <button type="button" 
                                                class="btn btn-danger"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $item->id }}"
                                                data-bs-toggle="tooltip"
                                                title="Tolak Pembayaran">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Detail -->
                            <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">
                                                <i class="bi bi-info-circle me-2"></i>Detail Pembayaran
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="text-primary mb-3">
                                                        <i class="bi bi-person me-2"></i>Informasi Siswa
                                                    </h6>
                                                    <table class="table table-sm table-borderless">
                                                        <tr>
                                                            <td width="120"><strong>Nama</strong></td>
                                                            <td>: {{ $item->user->nama }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>NIS</strong></td>
                                                            <td>: {{ $item->user->username }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Email</strong></td>
                                                            <td>: {{ $item->user->email }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-primary mb-3">
                                                        <i class="bi bi-credit-card me-2"></i>Informasi Pembayaran
                                                    </h6>
                                                    <table class="table table-sm table-borderless">
                                                        <tr>
                                                            <td width="120"><strong>Jenis</strong></td>
                                                            <td>
                                                                @if($item->tagihan_id)
                                                                    <span class="badge bg-primary">Tagihan</span>
                                                                @else
                                                                    <span class="badge bg-info">SPP</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Metode</strong></td>
                                                            <td>{{ $item->metode }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Jumlah</strong></td>
                                                            <td class="fw-bold text-success">
                                                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>

                                            @if($item->bukti)
                                            <div class="mt-4">
                                                <h6 class="text-primary mb-3">
                                                    <i class="bi bi-image me-2"></i>Bukti Pembayaran
                                                </h6>
                                                <a href="{{ asset('storage/' . $item->bukti) }}" 
                                                   target="_blank" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye me-1"></i>Lihat Bukti Pembayaran
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Tolak -->
                            <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">
                                                <i class="bi bi-x-circle me-2"></i>Tolak Pembayaran
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.pembayaran.reject', $item->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="alert alert-warning">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                    Anda akan menolak pembayaran dari <strong>{{ $item->user->nama }}</strong>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="alasan_reject{{ $item->id }}" class="form-label">
                                                        <strong>Alasan Penolakan</strong>
                                                    </label>
                                                    <textarea class="form-control" id="alasan_reject{{ $item->id }}" 
                                                              name="alasan_reject" rows="4" required 
                                                              placeholder="Berikan alasan penolakan yang jelas..."></textarea>
                                                    <div class="form-text">
                                                        Alasan ini akan dikirimkan ke siswa/orang tua.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Tolak Pembayaran</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="bi bi-check-circle display-1 text-success mb-3"></i>
                        <h4>Tidak ada pembayaran yang menunggu verifikasi</h4>
                        <p class="text-muted">Semua pembayaran sudah diverifikasi. Cek riwayat untuk melihat data sebelumnya.</p>
                        <a href="{{ route('admin.pembayaran.history') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-house"></i>Lihat Riwayat
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
}
.max-width-200 {
    max-width: 200px;
}
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-danger { border-left: 4px solid #e74a3b !important; }
.empty-state {
    max-width: 400px;
    margin: 0 auto;
}
</style>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection