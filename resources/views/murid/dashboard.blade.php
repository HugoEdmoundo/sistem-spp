@extends('layouts.app')

@section('title', 'Dashboard Murid')

@section('content')
<div class="row mb-4">
    <div class="col-md-3 mb-4">
        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="bi bi-file-invoice"></i>
            </div>
            <div class="stat-value">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
            <div class="stat-label">Total Tagihan</div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card success">
            <div class="stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-value">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</div>
            <div class="stat-label">Total Dibayar</div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card info">
            <div class="stat-icon">
                <i class="bi bi-clock"></i>
            </div>
            <div class="stat-value">{{ $tagihanPending }}</div>
            <div class="stat-label">Menunggu Verifikasi</div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card danger">
            <div class="stat-icon">
                <i class="bi bi-x-circle me-2 text-danger"></i>
            </div>
            <div class="stat-value">{{ $tagihanRejected }}</div>
            <div class="stat-label">Ditolak</div>
        </div>
    </div>
</div>

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