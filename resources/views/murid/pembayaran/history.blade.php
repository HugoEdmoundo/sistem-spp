<!-- resources/views/murid/pembayaran/history.blade.php -->
@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="container-fluid px-0">
    <!-- Mobile Header -->
    <div class="bg-primary text-white p-3 sticky-top">
        <div class="d-flex align-items-center">
            {{-- <a href="{{ route('murid.dashboard') }}" class="text-white me-3">
                <i class="bi bi-arrow-left fs-5"></i>
            </a> --}}
            <div class="flex-grow-1">
                <h6 class="mb-0 fw-bold">Riwayat Pembayaran</h6>
                <small class="opacity-75">Semua pembayaran yang telah dilakukan</small>
            </div>
            <div class="bg-white bg-opacity-20 rounded p-1">
                <i class="bi bi-clock-history text-white"></i>
            </div>
        </div>
    </div>

    <!-- Alert Section Mobile -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div class="flex-grow-1">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    <!-- Content -->
    <div class="p-3">
        @if($pembayaran->count() > 0)
            <!-- Mobile Card List -->
            <div class="mb-4">
                @foreach($pembayaran as $index => $item)
                <!-- Mobile Payment Card -->
                <div class="card payment-card-mobile mb-3 border-0 shadow-sm 
                    @if($item->status == 'accepted') card-diterima
                    @elseif($item->status == 'pending') card-menunggu
                    @else card-ditolak @endif">
                    
                    <div class="card-body p-3">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center flex-grow-1">
                                <div class="bg-light rounded p-2 me-3">
                                    @if($item->tagihan_id)
                                        @if($item->tagihan->jenis == 'spp')
                                            <i class="bi bi-wallet2 text-info"></i>
                                        @else
                                            <i class="bi bi-receipt text-primary"></i>
                                        @endif
                                    @else
                                        <i class="bi bi-credit-card text-secondary"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1 fw-bold text-dark small">
                                        @if($item->tagihan)
                                            {{ Str::limit($item->tagihan->keterangan, 35) }}
                                        @else
                                            {{ Str::limit($item->keterangan ?? 'Pembayaran', 35) }}
                                        @endif
                                    </h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Jenis Badge -->
                                        @if($item->tagihan_id)
                                            @if($item->tagihan->jenis == 'spp')
                                                <span class="badge bg-info" style="font-size: 0.65rem;">SPP</span>
                                            @else
                                                <span class="badge bg-primary" style="font-size: 0.65rem;">TAGIHAN</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary" style="font-size: 0.65rem;">LAINNYA</span>
                                        @endif
                                        
                                        <!-- Jenis Bayar -->
                                        @if($item->isLunas())
                                            <span class="badge bg-success" style="font-size: 0.6rem;">LUNAS</span>
                                        @elseif($item->isCicilan())
                                            <span class="badge bg-warning" style="font-size: 0.6rem;">CICILAN</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Amount & Status Row -->
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted d-block" style="font-size: 0.7rem;">Jumlah</small>
                                <div class="fw-bold text-primary" style="font-size: 0.9rem;">
                                    Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block" style="font-size: 0.7rem;">Status</small>
                                <div>
                                    @if($item->status == 'pending')
                                        <span class="badge bg-warning" style="font-size: 0.7rem;">
                                            <i class="bi bi-clock me-1"></i>MENUNGGU
                                        </span>
                                    @elseif($item->status == 'accepted')
                                        <span class="badge bg-success" style="font-size: 0.7rem;">
                                            <i class="bi bi-check-circle me-1"></i>DITERIMA
                                        </span>
                                    @else
                                        <span class="badge bg-danger" style="font-size: 0.7rem;">
                                            <i class="bi bi-x-circle me-1"></i>DITOLAK
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block" style="font-size: 0.7rem;">Metode</small>
                                <div class="fw-medium" style="font-size: 0.8rem;">{{ $item->metode }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block" style="font-size: 0.7rem;">Tanggal</small>
                                <div class="fw-medium" style="font-size: 0.8rem;">
                                    {{ $item->tanggal_upload->format('d/m/y') }}
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar for Cicilan -->
                        @if($item->tagihan && $item->tagihan->is_cicilan)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted" style="font-size: 0.7rem;">Progress</small>
                                <small class="fw-bold text-primary" style="font-size: 0.7rem;">
                                    {{ number_format($item->tagihan->persentase_dibayar, 1) }}%
                                </small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" 
                                    style="width: {{ $item->tagihan->persentase_dibayar }}%">
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Alasan Ditolak -->
                        @if($item->status == 'rejected' && $item->alasan_reject)
                        <div class="alert alert-danger py-2 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-exclamation-triangle me-2 mt-1" style="font-size: 0.8rem;"></i>
                                <div>
                                    <small class="fw-bold d-block" style="font-size: 0.7rem;">Alasan Ditolak:</small>
                                    <small style="font-size: 0.7rem;">{{ $item->alasan_reject }}</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 pt-2 border-top">
                            <!-- Detail Button -->
                            <button type="button" 
                                    class="btn btn-outline-primary btn-sm flex-fill" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detailModal{{ $item->id }}">
                                <i class="bi bi-eye me-1"></i>
                                Detail
                            </button>

                            <!-- Kuitansi Button (Hanya untuk yang diterima) -->
                            @if($item->status == 'accepted')
                            <a href="{{ route('murid.kuitansi.pdf', $item->id) }}" 
                               class="btn btn-success btn-sm"
                               target="_blank">
                                <i class="bi bi-download"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Modal Detail Pembayaran Mobile -->
                <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $item->id }}">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-light py-3">
                                <h6 class="modal-title fw-bold">
                                    <i class="bi bi-info-circle text-primary me-2"></i>
                                    Detail Pembayaran
                                </h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-3">
                                <!-- Info Utama -->
                                <div class="card border-0 bg-light mb-3">
                                    <div class="card-body p-3">
                                        <h6 class="card-title small fw-bold border-bottom pb-2 mb-3">
                                            <i class="bi bi-receipt me-2"></i>Informasi Pembayaran
                                        </h6>
                                        <div class="row g-3 small">
                                            <div class="col-6">
                                                <span class="text-muted d-block">ID Pembayaran</span>
                                                <span class="fw-medium">#{{ $item->id }}</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted d-block">Jenis</span>
                                                @if($item->tagihan_id)
                                                    @if($item->tagihan->jenis == 'spp')
                                                        <span class="badge bg-info">SPP</span>
                                                    @else
                                                        <span class="badge bg-primary">Tagihan</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Lainnya</span>
                                                @endif
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted d-block">Jenis Bayar</span>
                                                @if($item->isLunas())
                                                    <span class="badge bg-success">Lunas</span>
                                                @else
                                                    <span class="badge bg-warning">Cicilan</span>
                                                @endif
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted d-block">Metode</span>
                                                <span class="fw-medium">{{ $item->metode }}</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted d-block">Tanggal Upload</span>
                                                <span class="fw-medium">{{ $item->tanggal_upload->format('d/m/y H:i') }}</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted d-block">Tanggal Proses</span>
                                                <span class="fw-medium">
                                                    @if($item->tanggal_proses)
                                                        {{ $item->tanggal_proses->format('d/m/y H:i') }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="col-12">
                                                <span class="text-muted d-block">Jumlah</span>
                                                <span class="fw-bold text-primary fs-6">
                                                    Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Info Tagihan -->
                                @if($item->tagihan)
                                <div class="card border-0 bg-light mb-3">
                                    <div class="card-body p-3">
                                        <h6 class="card-title small fw-bold border-bottom pb-2 mb-3">
                                            <i class="bi bi-activity me-2"></i>Informasi Tagihan
                                        </h6>
                                        <div class="row g-3 small text-center">
                                            <div class="col-4">
                                                <span class="text-muted d-block">Total</span>
                                                <div class="fw-bold">Rp {{ number_format($item->tagihan->jumlah, 0, ',', '.') }}</div>
                                            </div>
                                            <div class="col-4">
                                                <span class="text-muted d-block">Dibayar</span>
                                                <div class="fw-bold text-success">Rp {{ number_format($item->tagihan->total_dibayar, 0, ',', '.') }}</div>
                                            </div>
                                            <div class="col-4">
                                                <span class="text-muted d-block">Sisa</span>
                                                <div class="fw-bold text-warning">Rp {{ number_format($item->tagihan->sisa_tagihan, 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                        @if($item->tagihan->is_cicilan)
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted">Progress</small>
                                                <small class="fw-bold text-primary">{{ number_format($item->tagihan->persentase_dibayar, 1) }}%</small>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success" style="width: {{ $item->tagihan->persentase_dibayar }}%"></div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                <!-- Bukti Pembayaran -->
                                @if($item->bukti)
                                <div class="card border-0 bg-light mb-3">
                                    <div class="card-body p-3">
                                        <h6 class="card-title small fw-bold border-bottom pb-2 mb-3">
                                            <i class="bi bi-image me-2"></i>Bukti Pembayaran
                                        </h6>
                                        <a href="{{ asset('storage/' . $item->bukti) }}" 
                                           target="_blank" 
                                           class="btn btn-outline-primary btn-sm w-100">
                                            <i class="bi bi-eye me-1"></i>Lihat Bukti Pembayaran
                                        </a>
                                    </div>
                                </div>
                                @endif

                                <!-- Alasan Ditolak -->
                                @if($item->status == 'rejected' && $item->alasan_reject)
                                <div class="alert alert-danger">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-exclamation-triangle me-2 mt-1"></i>
                                        <div>
                                            <h6 class="alert-heading mb-2 small fw-bold">Alasan Penolakan</h6>
                                            <p class="mb-0 small">{{ $item->alasan_reject }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Status Diterima -->
                                @if($item->status == 'accepted')
                                <div class="alert alert-success">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        <div>
                                            <strong class="small d-block">Pembayaran telah diterima</strong>
                                            <small>Kuitansi tersedia untuk diunduh</small>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="modal-footer p-3">
                                @if($item->status == 'accepted')
                                <a href="{{ route('murid.kuitansi.pdf', $item->id) }}" 
                                   class="btn btn-success btn-sm flex-fill"
                                   target="_blank">
                                    <i class="bi bi-download me-1"></i>Download Kuitansi
                                </a>
                                @endif
                                <button type="button" class="btn btn-secondary btn-sm flex-fill" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination Mobile -->
            @if($pembayaran->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <div class="d-flex gap-2">
                    {{ $pembayaran->links('pagination::simple-bootstrap-4') }}
                </div>
            </div>
            @endif

        @else
        <!-- Empty State Mobile -->
        <div class="text-center py-5">
            <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                <i class="bi bi-inbox text-muted fs-1"></i>
            </div>
            <h6 class="text-muted mb-2">Belum ada riwayat pembayaran</h6>
            <p class="text-muted small mb-0">Semua pembayaran yang Anda lakukan akan muncul di sini</p>
        </div>
        @endif
    </div>
</div>

<style>
/* Mobile Optimizations */
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 1020;
}

/* Mobile Card Styles */
.payment-card-mobile {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border-left: 4px solid #007bff !important;
    border-radius: 12px;
}

.payment-card-mobile:active {
    transform: scale(0.98);
}

/* Card Colors Based on Status */
.card-diterima {
    border-left-color: #28a745 !important;
    background: linear-gradient(135deg, #f8fff9, #ffffff);
}

.card-menunggu {
    border-left-color: #ffc107 !important;
    background: linear-gradient(135deg, #fffbf0, #ffffff);
}

.card-ditolak {
    border-left-color: #dc3545 !important;
    background: linear-gradient(135deg, #fff5f5, #ffffff);
}

/* Progress Bar */
.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

/* Modal Mobile Optimization */
.modal-dialog {
    margin: 0.5rem;
}

.modal-content {
    border-radius: 16px;
    border: none;
    max-height: 90vh;
    overflow-y: auto;
}

/* Button Styles */
.btn {
    border-radius: 8px;
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
}

.btn-sm {
    font-size: 0.75rem;
    padding: 0.4rem 0.6rem;
}

/* Badge Styles */
.badge {
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
}

/* Alert Styles */
.alert {
    border-radius: 10px;
    border: none;
    font-size: 0.8rem;
}

/* Form Controls */
.form-control, .form-select {
    border-radius: 8px;
    font-size: 0.85rem;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 0;
        padding-right: 0;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .modal-dialog {
        margin: 0.5rem;
    }
}

/* Animation for modal */
@keyframes slideUp {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal.fade .modal-dialog {
    animation: slideUp 0.3s ease-out;
}

/* Custom scrollbar for modal */
.modal-content::-webkit-scrollbar {
    width: 6px;
}

.modal-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.modal-content::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.modal-content::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script>
// Mobile-specific enhancements
document.addEventListener('DOMContentLoaded', function() {
    // Add touch feedback to cards
    const cards = document.querySelectorAll('.payment-card-mobile');
    cards.forEach(card => {
        card.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        card.addEventListener('touchend', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Handle modal backdrop tap to close
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                const modalInstance = bootstrap.Modal.getInstance(this);
                modalInstance.hide();
            }
        });
    });

    // Improve form input experience on mobile
    const formInputs = document.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.fontSize = '16px'; // Prevent zoom on iOS
        });
    });

    // Close modals on back button press (mobile)
    document.addEventListener('backbutton', function() {
        const openModal = document.querySelector('.modal.show');
        if (openModal) {
            const modalInstance = bootstrap.Modal.getInstance(openModal);
            modalInstance.hide();
        }
    }, false);

    // Smooth scrolling for modal content
    const modalContents = document.querySelectorAll('.modal-content');
    modalContents.forEach(content => {
        content.addEventListener('touchstart', function() {
            this.style.overflowY = 'auto';
        });
    });
});
</script>
@endsection