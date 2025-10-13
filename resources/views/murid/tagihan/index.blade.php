<!-- resources/views/murid/tagihan/index.blade.php -->
@extends('layouts.app')

@section('title', 'Daftar Tagihan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Daftar Semua Tagihan</h4>
    <a href="{{ route('murid.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Bulan/Tahun</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal Tagihan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagihan as $item)
                    <tr>
                        <td>{{ $item->periode }}</td>
                        <td>
                            <span class="badge {{ $item->jenis == 'spp' ? 'bg-primary' : 'bg-info' }}">
                                {{ ucfirst($item->jenis) }}
                            </span>
                        </td>
                        <td>{{ $item->keterangan }}</td>
                        <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td>
                            @if($item->status == 'unpaid')
                                <span class="badge bg-danger">Unpaid</span>
                            @elseif($item->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($item->status == 'success')
                                <span class="badge bg-success">Success</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($item->status == 'unpaid')
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                        data-bs-target="#uploadModal{{ $item->id }}">
                                    <i class="fas fa-upload"></i> Upload Bukti
                                </button>
                            @elseif($item->status == 'pending')
                                <span class="text-warning">
                                    <i class="fas fa-clock"></i> Menunggu
                                </span>
                            @elseif($item->status == 'success')
                                <span class="text-success">
                                    <i class="fas fa-check-circle"></i> Lunas
                                </span>
                            @else
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                        data-bs-target="#uploadModal{{ $item->id }}">
                                    <i class="fas fa-redo"></i> Upload Ulang
                                </button>
                            @endif
                        </td>
                    </tr>

                    <!-- Modal Upload Bukti -->
                    <div class="modal fade" id="uploadModal{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('murid.upload.bukti', $item->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Upload Bukti Pembayaran - {{ $item->keterangan }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Jumlah Tagihan</label>
                                            <input type="text" class="form-control" value="Rp {{ number_format($item->jumlah, 0, ',', '.') }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label>Metode Pembayaran *</label>
                                            <select name="metode" class="form-control" required>
                                                <option value="">Pilih Metode</option>
                                                <option value="Transfer Bank">Transfer Bank</option>
                                                <option value="Tunai">Tunai</option>
                                                <option value="E-Wallet">E-Wallet</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Bukti Pembayaran *</label>
                                            <input type="file" name="bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                                            <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                                        </div>
                                        <div class="alert alert-info">
                                            <small>
                                                <i class="fas fa-info-circle"></i>
                                                Pastikan bukti pembayaran jelas dan valid. Admin akan memverifikasi dalam 1x24 jam.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Upload Bukti</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <p>Tidak ada tagihan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tagihan->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $tagihan->links() }}
        </div>
        @endif
    </div>
</div>
@endsection