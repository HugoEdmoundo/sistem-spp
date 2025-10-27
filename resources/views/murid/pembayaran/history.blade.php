<!-- resources/views/murid/pembayaran/history.blade.php -->
@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-clock-history me-2"></i>Riwayat Pembayaran Saya</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Keterangan</th>
                            <th>Metode</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Alasan Reject</th>
                            <th>Bukti</th>
                            <th>Tanggal Upload</th>
                            <th>Tanggal Proses</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayaran as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($item->tagihan)
                                    {{ $item->tagihan->keterangan }}
                                    <br>
                                    <span class="badge bg-primary">Tagihan</span>
                                @else
                                    {{ $item->keterangan ?? 'Pembayaran SPP Fleksibel' }}
                                    <br>
                                    <span class="badge bg-info">Fleksibel</span>
                                    @if($item->range_bulan)
                                    <br>
                                    <small class="text-muted">{{ $item->range_bulan }}</small>
                                    @endif
                                @endif
                                
                                <!-- Tampilkan alasan reject jika status rejected -->
                                @if($item->status == 'rejected' && $item->alasan_reject)
                                    <div class="mt-2">
                                        <small class="text-danger">
                                            <strong><i class="bi bi-exclamation-triangle"></i> Alasan ditolak:</strong><br>
                                            {{ $item->alasan_reject }}
                                        </small>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $item->metode }}</span>
                            </td>
                            <td class="fw-bold">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($item->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($item->status == 'accepted')
                                    <span class="badge bg-success">Accepted</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                @if($item->status == 'rejected' && $item->alasan_reject)
                                    <span class="text-danger">
                                        <i class="bi bi-exclamation-circle"></i> Ada alasan
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->bukti)
                                <a href="{{ asset('storage/' . $item->bukti) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small>
                                    {{ $item->tanggal_upload->format('d/m/Y') }}<br>
                                    <span class="text-muted">{{ $item->tanggal_upload->format('H:i') }}</span>
                                </small>
                            </td>
                            <td>
                                @if($item->tanggal_proses)
                                    <small>
                                        {{ $item->tanggal_proses->format('d/m/Y') }}<br>
                                        <span class="text-muted">{{ $item->tanggal_proses->format('H:i') }}</span>
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->status == 'rejected')
                                    @if($item->tagihan_id)
                                        <!-- Upload ulang untuk tagihan -->
                                        <button type="button" 
                                                class="btn btn-sm btn-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#uploadUlangModal{{ $item->id }}">
                                            <i class="bi bi-upload"></i> Upload Ulang
                                        </button>
                                    @else
                                        <!-- Upload ulang untuk SPP -->
                                        <button type="button" 
                                                class="btn btn-sm btn-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#uploadUlangSppModal{{ $item->id }}">
                                            <i class="bi bi-upload"></i> Upload Ulang
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>

                        <!-- Modal Upload Ulang Tagihan -->
                        @if($item->status == 'rejected' && $item->tagihan_id)
                        <div class="modal fade" id="uploadUlangModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Upload Ulang Bukti Pembayaran Tagihan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('murid.pembayaran.upload-ulang', $item->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <h6><i class="bi bi-info-circle"></i> Alasan Penolakan Sebelumnya:</h6>
                                                <p class="mb-0">{{ $item->alasan_reject }}</p>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="metode{{ $item->id }}" class="form-label">Metode Pembayaran *</label>
                                                <select class="form-control" id="metode{{ $item->id }}" name="metode" required>
                                                    <option value="">Pilih Metode</option>
                                                    <option value="Transfer">Transfer</option>
                                                    <option value="Tunai">Tunai</option>
                                                    <option value="QRIS">QRIS</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="bukti{{ $item->id }}" class="form-label">Bukti Pembayaran Baru *</label>
                                                <input type="file" class="form-control" id="bukti{{ $item->id }}" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required>
                                                <div class="form-text">Format: JPG, PNG, PDF (Maks. 2MB)</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Upload Ulang</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Modal Upload Ulang SPP -->
                        @if($item->status == 'rejected' && !$item->tagihan_id)
                        <div class="modal fade" id="uploadUlangSppModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Upload Ulang Bukti Pembayaran SPP</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('murid.spp.upload-ulang', $item->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <h6><i class="bi bi-info-circle"></i> Alasan Penolakan Sebelumnya:</h6>
                                                <p class="mb-0">{{ $item->alasan_reject }}</p>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="metode_spp{{ $item->id }}" class="form-label">Metode Pembayaran *</label>
                                                <select class="form-control" id="metode_spp{{ $item->id }}" name="metode" required>
                                                    <option value="">Pilih Metode</option>
                                                    <option value="Transfer">Transfer</option>
                                                    <option value="Tunai">Tunai</option>
                                                    <option value="QRIS">QRIS</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="bukti_spp{{ $item->id }}" class="form-label">Bukti Pembayaran Baru *</label>
                                                <input type="file" class="form-control" id="bukti_spp{{ $item->id }}" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required>
                                                <div class="form-text">Format: JPG, PNG, PDF (Maks. 2MB)</div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="keterangan{{ $item->id }}" class="form-label">Keterangan *</label>
                                                <input type="text" class="form-control" id="keterangan{{ $item->id }}" name="keterangan" value="Upload ulang pembayaran SPP" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Upload Ulang</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                                <p>Belum ada riwayat pembayaran.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pembayaran->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $pembayaran->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection