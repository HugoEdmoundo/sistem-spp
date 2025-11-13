<!-- resources/views/admin/pembayaran/index.blade.php -->
@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">
                <i class="bi bi-patch-check me-2"></i>Verifikasi Pembayaran
            </h1>
            <p class="text-muted mb-0">Kelola pembayaran yang menunggu persetujuan</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-warning text-dark fs-6">{{ $pembayaran->count() }} Menunggu</span>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="m-0 fw-bold text-dark">
                <i class="bi bi-list-ul me-2"></i>Daftar Pembayaran
            </h6>
        </div>
        <div class="card-body p-0">
            @if($pembayaran->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="ps-4">#</th>
                                <th>Siswa</th>
                                <th>Jenis</th>
                                <th>Keterangan</th>
                                <th>Jumlah</th>
                                <th class="text-center">Bukti</th>
                                <th width="200" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pembayaran as $index => $item)
                            <tr>
                                <td class="ps-4">
                                    <span class="text-muted">{{ $index + 1 }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <span class="text-white fw-bold small">
                                                {{ substr($item->user->nama, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $item->user->nama }}</div>
                                            <small class="text-muted">NIS: {{ $item->user->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($item->tagihan_id)
                                        <span class="badge bg-primary">Tagihan</span>
                                    @else
                                        <span class="badge bg-info">SPP</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="max-width-200">
                                        @if($item->tagihan_id)
                                            <div class="fw-bold">{{ $item->tagihan->keterangan ?? 'Tagihan' }}</div>
                                        @else
                                            <div class="fw-bold">{{ $item->keterangan }}</div>
                                            @if($item->bulan_mulai && $item->bulan_akhir)
                                                <small class="text-muted">
                                                    {{ \App\Models\User::getNamaBulanStatic($item->bulan_mulai) }} - 
                                                    {{ \App\Models\User::getNamaBulanStatic($item->bulan_akhir) }} 
                                                    {{ $item->tahun }}
                                                </small>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="fw-bold text-success">
                                    Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    @if($item->bukti)
                                    <a href="{{ asset('storage/' . $item->bukti) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="Lihat Bukti">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Tombol Terima -->
                                        <form action="{{ route('admin.pembayaran.approve', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-success btn-sm"
                                                    onclick="return confirm('Terima pembayaran ini?')">
                                                <i class="bi bi-check-lg"></i> Terima
                                            </button>
                                        </form>

                                        <!-- Tombol Tolak -->
                                        <button type="button" 
                                                class="btn btn-danger btn-sm"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $item->id }}">
                                            <i class="bi bi-x-lg"></i> Tolak
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Tolak -->
                            <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tolak Pembayaran</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <!-- Di dalam modal tolak -->
                                        <form action="{{ route('admin.pembayaran.reject', $item->id) }}" method="POST">
                                            @csrf
                                            @method('POST') <!-- Tambahkan ini -->
                                            <div class="modal-body">
                                                <p class="mb-3">Tolak pembayaran dari <strong>{{ $item->user->nama }}</strong>?</p>
                                                <div class="mb-3">
                                                    <label for="alasan_reject{{ $item->id }}" class="form-label">
                                                        <strong>Alasan Penolakan <span class="text-danger">*</span></strong>
                                                    </label>
                                                    <textarea class="form-control" id="alasan_reject{{ $item->id }}" 
                                                            name="alasan_reject" rows="3" required 
                                                            minlength="5" maxlength="500"
                                                            placeholder="Berikan alasan penolakan yang jelas...">{{ old('alasan_reject') }}</textarea>
                                                    @error('alasan_reject')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
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
                        <i class="bi bi-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-gray-800">Tidak ada pembayaran yang menunggu verifikasi</h5>
                        <p class="text-muted">Semua pembayaran sudah diverifikasi.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 36px;
    height: 36px;
    font-size: 14px;
}
.max-width-200 {
    max-width: 200px;
}
.empty-state {
    max-width: 300px;
    margin: 0 auto;
}
.table > :not(caption) > * > * {
    padding: 0.75rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple confirmation for approve
    const approveForms = document.querySelectorAll('form[action*="approve"]');
    approveForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Apakah Anda yakin ingin menerima pembayaran ini?')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection