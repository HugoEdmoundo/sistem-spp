<!-- resources/views/admin/pembayaran/index.blade.php -->
@extends('layouts.app')

@section('title', 'Pembayaran Pending')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-clock me-2"></i>Pembayaran Menunggu Verifikasi</h4>
        <a href="{{ route('admin.pembayaran.history') }}" class="btn btn-primary">
            <i class="bi bi-history me-2"></i>Riwayat Pembayaran
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success:</strong> {{ session('success') }}
            @if(session('debug_info'))
                <br><small>Debug: {{ json_encode(session('debug_info')) }}</small>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">Daftar Pembayaran Pending</h5>
                </div>
                <div class="col-md-6 text-end">
                    <span class="badge bg-warning">{{ $pembayaran->count() }} Menunggu</span>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            @if($pembayaran->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Murid</th>
                                <th>Keterangan</th>
                                <th>Metode</th>
                                <th>Jumlah</th>
                                <th>Bukti</th>
                                <th>Tanggal Upload</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pembayaran as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm bg-light rounded">
                                                    <div class="avatar-title bg-soft-primary text-primary rounded fs-16">
                                                        {{ substr($item->user->nama, 0, 1) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <strong>{{ $item->user->nama }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $item->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->tagihan)
                                            <span class="fw-semibold">{{ $item->tagihan->keterangan }}</span>
                                            <br>
                                            <small class="badge bg-info">Tagihan</small>
                                        @else
                                            <span class="fw-semibold">{{ $item->keterangan ?? 'Pembayaran SPP Fleksibel' }}</span>
                                            <br>
                                            <small class="badge bg-secondary">Fleksibel</small>
                                            @if($item->range_bulan)
                                                <br>
                                                <small class="text-muted">{{ $item->range_bulan }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ ucfirst($item->metode) }}</span>
                                    </td>
                                    <td class="fw-bold text-success">
                                        Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @if($item->bukti)
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#buktiModal{{ $item->id }}">
                                                <i class="bi bi-eye me-1"></i> Lihat
                                            </button>
                                        @else
                                            <span class="text-muted fst-italic">Tidak ada bukti</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>
                                            {{ $item->tanggal_upload->format('d/m/Y') }}<br>
                                            <span class="text-muted">{{ $item->tanggal_upload->format('H:i') }}</span>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <!-- Approve Button -->
                                            <form action="{{ route('admin.pembayaran.approve', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-success"
                                                        onclick="return confirm('Setujui pembayaran ini?')">
                                                    <i class="bi bi-check-lg"></i> Approve
                                                </button>
                                            </form>
                                            
                                            <!-- Reject Button with Modal -->
                                            <button type="button" 
                                                    class="btn btn-danger"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#rejectModal{{ $item->id }}">
                                                <i class="bi bi-x-lg"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="text-muted">Tidak ada pembayaran yang menunggu verifikasi</h5>
                    <p class="text-muted">Semua pembayaran telah diproses.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals Section -->
@foreach($pembayaran as $item)
    <!-- Bukti Modal -->
    @if($item->bukti)
        <div class="modal fade" id="buktiModal{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Bukti Pembayaran - {{ $item->user->nama }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        @if(pathinfo($item->bukti, PATHINFO_EXTENSION) === 'pdf')
                            <iframe src="{{ asset('storage/' . $item->bukti) }}" 
                                    width="100%" 
                                    height="500px"
                                    class="border rounded">
                            </iframe>
                        @else
                            <img src="{{ asset('storage/' . $item->bukti) }}" 
                                 alt="Bukti Pembayaran" 
                                 class="img-fluid rounded" 
                                 style="max-height: 500px;">
                        @endif
                        <div class="mt-3">
                            <a href="{{ asset('storage/' . $item->bukti) }}" 
                               download="bukti-{{ $item->user->nama }}-{{ $item->id }}.{{ pathinfo($item->bukti, PATHINFO_EXTENSION) }}"
                               class="btn btn-sm btn-primary">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.pembayaran.reject', $item->id) }}" method="POST" id="rejectForm{{ $item->id }}">
                    @csrf
                    <div class="modal-body">
                        @if(app()->environment('local'))
                            <div class="alert alert-info">
                                <strong>Debug Info:</strong><br>
                                Pembayaran ID: {{ $item->id }}<br>
                                Route: {{ route('admin.pembayaran.reject', $item->id) }}<br>
                                User: {{ auth()->user()->nama }} (ID: {{ auth()->user()->id }})
                            </div>
                        @endif
                        
                        <p>Anda akan menolak pembayaran dari <strong>{{ $item->user->nama }}</strong> sebesar <strong>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</strong></p>
                        
                        <div class="mb-3">
                            <label for="alasan_reject{{ $item->id }}" class="form-label">Alasan Penolakan *</label>
                            <textarea class="form-control" id="alasan_reject{{ $item->id }}" name="alasan_reject" rows="4" 
                                      placeholder="Berikan alasan jelas mengapa pembayaran ditolak..." required></textarea>
                            <div class="form-text">Alasan ini akan dilihat oleh murid.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger" id="submitBtn{{ $item->id }}">Tolak Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Debug form submissiochn (only in development)
        @if(app()->environment('local'))
            document.querySelectorAll('[id^="rejectForm"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    console.log('Form submitted:', {
                        action: this.action,
                        method: this.method,
                        data: new FormData(this)
                    });
                    
                    const submitBtn = this.querySelector('[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
                });
            });
        @endif
    });
</script>
@endsection