<!-- resources/views/murid/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Murid')

@section('content')
<!-- resources/views/murid/dashboard.blade.php -->
<!-- Di bagian statistik -->

<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-hover">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-1">Total Tagihan</span>
                        <h4 class="mb-0">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h4>
                        <small class="text-muted">
                            {{ $tagihanUnpaidCount }} belum bayar + 
                            {{ $tagihanPartialCount }} cicilan
                        </small>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary rounded-circle">
                                <i class="bi bi-receipt fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card card-hover">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-1">Total Dibayar</span>
                        <h4 class="mb-0">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="bi bi-cash-coin fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card card-hover">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-1">Menunggu Verifikasi</span>
                        <h4 class="mb-0">{{ $pembayaranPendingCount }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning rounded-circle">
                                <i class="bi bi-clock fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card card-hover">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-1">Perlu Tindakan</span>
                        <h4 class="mb-0">{{ $totalNotifikasi }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-danger rounded-circle">
                                <i class="bi bi-exclamation-triangle fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section Notifikasi -->
@if($totalTagihanNotif > 0 || $pembayaranPendingCount > 0 || $pembayaranRejectedCount > 0 || $pembayaranPartialCount > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bell-fill me-2"></i>
                    Notifikasi & Peringatan
                </h5>
            </div>
            <div class="card-body">
                <!-- Notifikasi Tagihan -->
                @if($tagihanUnpaidCount > 0)
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Tagihan Belum Dibayar</h6>
                        <p class="mb-0">Anda memiliki <strong>{{ $tagihanUnpaidCount }} tagihan</strong> yang belum dibayar.</p>
                    </div>
                    <a href="{{ route('murid.tagihan.index') }}" class="btn btn-sm btn-outline-danger">
                        Bayar Sekarang
                    </a>
                </div>
                @endif

                @if($tagihanPartialCount > 0)
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="bi bi-arrow-repeat me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Tagihan Masih Cicilan</h6>
                        <p class="mb-0">Anda memiliki <strong>{{ $tagihanPartialCount }} tagihan</strong> yang masih dalam proses cicilan.</p>
                    </div>
                    <a href="{{ route('murid.tagihan.index') }}" class="btn btn-sm btn-outline-warning">
                        Lanjutkan Cicilan
                    </a>
                </div>
                @endif

                <!-- Notifikasi Pembayaran -->
                @if($pembayaranPendingCount > 0)
                <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-clock-history me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Pembayaran Menunggu Verifikasi</h6>
                        <p class="mb-0">Anda memiliki <strong>{{ $pembayaranPendingCount }} pembayaran</strong> yang sedang menunggu verifikasi admin.</p>
                    </div>
                    <a href="{{ route('murid.pembayaran.history') }}" class="btn btn-sm btn-outline-info">
                        Lihat Detail
                    </a>
                </div>
                @endif

                @if($pembayaranRejectedCount > 0)
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="bi bi-x-circle me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Pembayaran Ditolak</h6>
                        <p class="mb-0">Anda memiliki <strong>{{ $pembayaranRejectedCount }} pembayaran</strong> yang ditolak. Silakan upload ulang bukti pembayaran.</p>
                    </div>
                    <a href="{{ route('murid.pembayaran.history') }}" class="btn btn-sm btn-outline-danger">
                        Upload Ulang
                    </a>
                </div>
                @endif

                @if($pembayaranPartialCount > 0)
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="bi bi-arrow-repeat me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Pembayaran Cicilan</h6>
                        <p class="mb-0">Anda memiliki <strong>{{ $pembayaranPartialCount }} pembayaran cicilan</strong> yang masih belum lunas.</p>
                    </div>
                    <a href="{{ route('murid.pembayaran.history') }}" class="btn btn-sm btn-outline-warning">
                        Lanjutkan Bayar
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Section Tagihan Cicilan -->
@if($tagihanPartial->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-arrow-repeat me-2"></i>
                    Tagihan Masih Cicilan
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($tagihanPartial as $tagihan)
                    <div class="col-md-6 mb-3">
                        <div class="card border-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">{{ $tagihan->keterangan }}</h6>
                                    <span class="badge bg-info">Cicilan</span>
                                </div>
                                
                                <div class="mb-2">
                                    <small class="text-muted">Total Tagihan:</small>
                                    <div class="fw-bold">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</div>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">Sudah Dibayar:</small>
                                    <div class="fw-bold text-success">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</div>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">Sisa:</small>
                                    <div class="fw-bold text-warning">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</div>
                                </div>

                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $tagihan->persentase_dibayar }}%"></div>
                                </div>
                                <small class="text-muted">{{ number_format($tagihan->persentase_dibayar, 1) }}% terbayar</small>

                                <div class="mt-3">
                                    <a href="{{ route('murid.tagihan.index') }}" class="btn btn-sm btn-warning w-100">
                                        <i class="bi bi-credit-card me-1"></i>
                                        Lanjutkan Cicilan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Pembayaran Pending & Ditolak -->
<div class="row mb-4">
    @if($pembayaranPending->count() > 0)
    <div class="col-md-6 mb-4">
        <div class="material-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history text-warning me-2"></i>Menunggu Verifikasi</h5>
                <span class="badge bg-warning">{{ $pembayaranPending->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($pembayaranPending as $pembayaran)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $pembayaran->keterangan }}</h6>
                                <p class="mb-1 text-muted small">
                                    <i class="bi bi-currency-dollar me-1"></i>Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}
                                </p>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>{{ $pembayaran->tanggal_upload->format('d M Y') }}
                                </small>
                            </div>
                            <span class="badge bg-warning">Pending</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($pembayaranRejected->count() > 0)
    <div class="col-md-6 mb-4">
        <div class="material-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-x-circle text-danger me-2"></i>Pembayaran Ditolak</h5>
                <span class="badge bg-danger">{{ $pembayaranRejected->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($pembayaranRejected as $pembayaran)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $pembayaran->keterangan }}</h6>
                                <p class="mb-1 text-muted small">
                                    <i class="bi bi-currency-dollar me-1"></i>Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}
                                </p>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>{{ $pembayaran->tanggal_upload->format('d M Y') }}
                                    @if($pembayaran->alasan_penolakan)
                                        <br><strong>Alasan:</strong> {{ $pembayaran->alasan_penolakan }}
                                    @endif
                                </small>
                            </div>
                            <span class="badge bg-danger">Ditolak</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Tagihan Terbaru -->
@if(isset($tagihan) && $tagihan->count() > 0)
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Tagihan Terbaru</h5>
        <a href="{{ route('murid.tagihan.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
    </div>
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
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tagihan as $item)
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
                        <td>
                            @if($item->status == 'unpaid')
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                        data-bs-target="#uploadModal{{ $item->id }}">
                                    <i class="bi bi-upload"></i> Upload
                                </button>
                            @elseif($item->status == 'pending')
                                <span class="text-warning">Menunggu verifikasi</span>
                            @elseif($item->status == 'success')
                                <span class="text-success"><i class="bi bi-check"></i> Lunas</span>
                            @else
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                        data-bs-target="#uploadModal{{ $item->id }}">
                                    <i class="bi bi-redo"></i> Upload Ulang
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
                                        <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Metode Pembayaran</label>
                                            <select name="metode" class="form-control" required>
                                                <option value="Transfer Bank">Transfer Bank</option>
                                                <option value="Tunai">Tunai</option>
                                                <option value="E-Wallet">E-Wallet</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Bukti Pembayaran (JPG/PNG/PDF, max 2MB)</label>
                                            <input type="file" name="bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                                        </div>
                                        <div class="alert alert-info">
                                            <small>
                                                <i class="fas fa-info-circle"></i>
                                                Upload bukti pembayaran yang valid. Admin akan memverifikasi dalam 1x24 jam.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Upload</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Tagihan Terbaru</h5>
    </div>
    <div class="card-body text-center py-4">
        <i class="bi bi-file-earmark-text fs-1 text-muted mb-3"></i>
        <p class="text-muted">Belum ada tagihan.</p>
        <a href="{{ route('murid.tagihan.index') }}" class="btn btn-primary">Lihat Semua Tagihan</a>
    </div>
</div>
@endif
@endsection