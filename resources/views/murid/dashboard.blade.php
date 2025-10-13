<!-- resources/views/murid/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Murid')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5><i class="fas fa-file-invoice"></i> Total Tagihan</h5>
                <h3>Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5><i class="fas fa-check-circle"></i> Total Dibayar</h5>
                <h3>Rp {{ number_format($totalDibayar, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5><i class="fas fa-clock"></i> Menunggu Verifikasi</h5>
                <h3>{{ $tagihanPending }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Menu Cepat untuk Murid -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Menu Cepat</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3 text-center">
                        <a href="{{ route('murid.tagihan.index') }}" class="btn btn-outline-primary w-100 h-100 py-3">
                            <i class="fas fa-file-invoice fa-2x mb-2"></i><br>
                            <span>Lihat Semua Tagihan</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3 text-center">
                        <a href="{{ route('murid.pembayaran.history') }}" class="btn btn-outline-info w-100 h-100 py-3">
                            <i class="fas fa-history fa-2x mb-2"></i><br>
                            <span>Riwayat Pembayaran</span>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3 text-center">
                        <a href="{{ route('murid.profile') }}" class="btn btn-outline-success w-100 h-100 py-3">
                            <i class="fas fa-user fa-2x mb-2"></i><br>
                            <span>Profile Saya</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Tagihan Terbaru</h5>
        <div>
            <a href="{{ route('murid.pembayaran.history') }}" class="btn btn-sm btn-info me-2">
                <i class="fas fa-history"></i> Riwayat Pembayaran
            </a>
            <a href="{{ route('murid.tagihan.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
        </div>
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
                                    <i class="fas fa-upload"></i> Upload
                                </button>
                            @elseif($item->status == 'pending')
                                <span class="text-warning">Menunggu verifikasi</span>
                            @elseif($item->status == 'success')
                                <span class="text-success"><i class="fas fa-check"></i> Lunas</span>
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
@endsection