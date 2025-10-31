<!-- resources/views/murid/pembayaran/history.blade.php -->
@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="bg-primary rounded p-2 me-3">
                <i class="bi bi-clock-history text-white fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">Riwayat Pembayaran Saya</h4>
                <p class="text-muted mb-0">Daftar semua pembayaran yang telah Anda lakukan</p>
            </div>
        </div>
    </div>

    <!-- Alert Section -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Card Content -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="bi bi-list-ul text-primary me-2"></i>
                Daftar Riwayat Pembayaran
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">#</th>
                            <th scope="col">Keterangan</th>
                            <th scope="col">Metode</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Status</th>
                            <th scope="col">Bukti</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col" class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayaran as $index => $item)
                        <tr class="align-middle">
                            <td class="ps-4 fw-semibold">{{ $index + 1 }}</td>
                            
                            <!-- Keterangan Column -->
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">
                                        @if($item->tagihan)
                                            {{ $item->tagihan->keterangan }}
                                        @else
                                            {{ $item->keterangan ?? 'Pembayaran SPP Fleksibel' }}
                                        @endif
                                    </span>
                                    
                                    <!-- Badge Jenis Pembayaran -->
                                    <div class="mt-1">
                                        @if($item->tagihan)
                                            <span class="badge bg-primary">Tagihan</span>
                                        @else
                                            <span class="badge bg-info">Fleksibel</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Periode untuk SPP Fleksibel -->
                                    @if(!$item->tagihan && $item->bulan_mulai && $item->bulan_akhir)
                                        <small class="text-muted mt-1">
                                            Periode: {{ \App\Models\User::getNamaBulanStatic($item->bulan_mulai) }} - 
                                            {{ \App\Models\User::getNamaBulanStatic($item->bulan_akhir) }} {{ $item->tahun }}
                                        </small>
                                    @endif
                                    
                                    <!-- Alasan Reject -->
                                    @if($item->status == 'rejected' && $item->alasan_reject)
                                        <div class="mt-2">
                                            <small class="text-danger">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                <strong>Alasan ditolak:</strong> {{ $item->alasan_reject }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Metode Column -->
                            <td>
                                <span class="badge bg-secondary">{{ $item->metode }}</span>
                            </td>
                            
                            <!-- Jumlah Column -->
                            <td class="fw-bold text-primary">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                            
                            <!-- Status Column -->
                            <td>
                                @if($item->status == 'pending')
                                    <span class="badge bg-warning">
                                        <i class="bi bi-clock me-1"></i>Pending
                                    </span>
                                @elseif($item->status == 'accepted')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Accepted
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Rejected
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Bukti Column -->
                            <td>
                                @if($item->bukti)
                                <a href="{{ asset('storage/' . $item->bukti) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>Lihat
                                </a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            
                            <!-- Tanggal Column -->
                            <td>
                                <div class="d-flex flex-column">
                                    <small class="fw-medium">
                                        {{ $item->tanggal_upload->format('d/m/Y') }}
                                    </small>
                                    <small class="text-muted">
                                        {{ $item->tanggal_upload->format('H:i') }}
                                    </small>
                                    @if($item->tanggal_proses)
                                    <small class="text-success mt-1">
                                        <i class="bi bi-check me-1"></i>
                                        Diproses: {{ $item->tanggal_proses->format('d/m/Y') }}
                                    </small>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Aksi Column -->
                            <td class="text-center pe-4">
                                <div class="btn-group" role="group">
                                    <!-- Tombol Detail -->
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailModal{{ $item->id }}"
                                            title="Lihat Detail">
                                        <i class="bi bi-info-circle"></i>
                                    </button>

                                    <!-- Tombol Kuitansi untuk yang accepted -->
                                    @if($item->status == 'accepted')
                                        <a href="{{ route('murid.kuitansi.pdf', $item->id) }}" 
                                        class="btn btn-sm btn-outline-success" 
                                        title="Download Kuitansi"
                                        target="_blank">
                                            <i class="bi bi-receipt"></i>
                                        </a>
                                    @endif
                                    
                                    <!-- Tombol Upload Ulang untuk yang rejected -->
                                    @if($item->status == 'rejected')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#uploadUlangModal{{ $item->id }}"
                                                title="Upload Ulang Bukti">
                                            <i class="bi bi-upload"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Detail Pembayaran -->
                        <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title d-flex align-items-center">
                                            <i class="bi bi-info-circle text-primary me-2"></i>
                                            Detail Pembayaran #{{ $item->id }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card border-0 bg-light">
                                                    <div class="card-body">
                                                        <h6 class="card-title border-bottom pb-2">
                                                            <i class="bi bi-receipt me-2"></i>Informasi Pembayaran
                                                        </h6>
                                                        <div class="row g-2">
                                                            <div class="col-5"><small class="text-muted">ID Pembayaran</small></div>
                                                            <div class="col-7"><small class="fw-medium">#{{ $item->id }}</small></div>
                                                            
                                                            <div class="col-5"><small class="text-muted">Jenis</small></div>
                                                            <div class="col-7">
                                                                @if($item->tagihan_id)
                                                                    <span class="badge bg-primary">Tagihan</span>
                                                                @else
                                                                    <span class="badge bg-info">SPP Fleksibel</span>
                                                                @endif
                                                            </div>
                                                            
                                                            <div class="col-5"><small class="text-muted">Tanggal Upload</small></div>
                                                            <div class="col-7"><small>{{ $item->tanggal_upload->format('d/m/Y H:i') }}</small></div>
                                                            
                                                            <div class="col-5"><small class="text-muted">Tanggal Proses</small></div>
                                                            <div class="col-7">
                                                                @if($item->tanggal_proses)
                                                                    <small>{{ $item->tanggal_proses->format('d/m/Y H:i') }}</small>
                                                                @else
                                                                    <small class="text-muted">-</small>
                                                                @endif
                                                            </div>
                                                            
                                                            <div class="col-5"><small class="text-muted">Metode</small></div>
                                                            <div class="col-7"><small>{{ $item->metode }}</small></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card border-0 bg-light">
                                                    <div class="card-body">
                                                        <h6 class="card-title border-bottom pb-2">
                                                            <i class="bi bi-activity me-2"></i>Status & Keterangan
                                                        </h6>
                                                        <div class="row g-2">
                                                            <div class="col-5"><small class="text-muted">Status</small></div>
                                                            <div class="col-7">
                                                                @if($item->status == 'pending')
                                                                    <span class="badge bg-warning">Pending</span>
                                                                @elseif($item->status == 'accepted')
                                                                    <span class="badge bg-success">Accepted</span>
                                                                @else
                                                                    <span class="badge bg-danger">Rejected</span>
                                                                @endif
                                                            </div>
                                                            
                                                            <div class="col-5"><small class="text-muted">Jumlah</small></div>
                                                            <div class="col-7">
                                                                <small class="fw-bold text-primary">
                                                                    Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                                                </small>
                                                            </div>
                                                            
                                                            @if($item->status == 'rejected' && $item->alasan_reject)
                                                            <div class="col-12 mt-2">
                                                                <small class="text-muted">Alasan Ditolak</small>
                                                                <div class="text-danger small">
                                                                    <i class="bi bi-exclamation-triangle me-1"></i> 
                                                                    {{ $item->alasan_reject }}
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if($item->bukti)
                                        <div class="mt-3">
                                            <h6 class="border-bottom pb-2">
                                                <i class="bi bi-image me-2"></i>Bukti Pembayaran
                                            </h6>
                                            <a href="{{ asset('storage/' . $item->bukti) }}" 
                                               target="_blank" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye me-1"></i>Lihat Bukti Pembayaran
                                            </a>
                                        </div>
                                        @endif

                                        @if($item->status == 'accepted')
                                        <div class="mt-3 alert alert-success d-flex align-items-center">
                                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                            <div>
                                                <strong>Pembayaran telah diverifikasi dan diterima.</strong>
                                                <br>
                                                <small>Kuitansi tersedia untuk diunduh.</small>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        @if($item->status == 'accepted')
                                        <a href="{{ route('murid.kuitansi.pdf', $item->id) }}" 
                                        class="btn btn-success" 
                                        target="_blank">
                                            <i class="bi bi-download me-1"></i>Download Kuitansi
                                        </a>
                                        @endif
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Upload Ulang (Unified) -->
                        @if($item->status == 'rejected')
                        <div class="modal fade" id="uploadUlangModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title">
                                            <i class="bi bi-upload me-2"></i>
                                            Upload Ulang Bukti Pembayaran
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ $item->tagihan_id ? route('murid.pembayaran.upload-ulang', $item->id) : route('murid.spp.upload-ulang', $item->id) }}" 
                                          method="POST" 
                                          enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="alert alert-warning">
                                                <h6 class="alert-heading">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>Alasan Penolakan Sebelumnya
                                                </h6>
                                                <p class="mb-0">{{ $item->alasan_reject }}</p>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="metode{{ $item->id }}" class="form-label">Metode Pembayaran *</label>
                                                <select class="form-select" id="metode{{ $item->id }}" name="metode" required>
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

                                            @if(!$item->tagihan_id)
                                            <div class="mb-3">
                                                <label for="keterangan{{ $item->id }}" class="form-label">Keterangan *</label>
                                                <input type="text" class="form-control" id="keterangan{{ $item->id }}" name="keterangan" value="Upload ulang pembayaran SPP" required>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-upload me-1"></i>Upload Ulang
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada riwayat pembayaran</h5>
                                    <p class="text-muted">Semua pembayaran yang Anda lakukan akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($pembayaran->hasPages())
            <div class="card-footer bg-white border-top">
                <div class="d-flex justify-content-center">
                    {{ $pembayaran->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection