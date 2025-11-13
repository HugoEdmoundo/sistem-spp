<!-- resources/views/admin/pembayaran/show.blade.php -->
@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Detail Pembayaran</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.pembayaran.history') }}">Riwayat Pembayaran</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0">Informasi Pembayaran</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Murid</th>
                                <td>{{ $pembayaran->user->nama }} ({{ $pembayaran->user->username }})</td>
                            </tr>
                            <tr>
                                <th>Jenis</th>
                                <td>
                                    @if($pembayaran->tagihan_id)
                                        <span class="badge bg-info">Tagihan</span>
                                        @if($pembayaran->isCicilan())
                                            <span class="badge bg-warning">Cicilan</span>
                                        @else
                                            <span class="badge bg-success">Lunas</span>
                                        @endif
                                    @else
                                        <span class="badge bg-primary">SPP</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Jumlah Bayar</th>
                                <td class="fw-bold text-primary">{{ $pembayaran->jumlah_formatted }}</td>
                            </tr>
                            @if($pembayaran->tagihan && $pembayaran->isCicilan())
                            <tr>
                                <th>Sisa Tagihan</th>
                                <td class="fw-bold text-warning">{{ $pembayaran->sisa_tagihan_formatted }}</td>
                            </tr>
                            <tr>
                                <th>Progress</th>
                                <td>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $pembayaran->tagihan->persentase_dibayar }}%">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ number_format($pembayaran->tagihan->persentase_dibayar, 1) }}% terbayar</small>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Status</th>
                                <td>
                                    @if($pembayaran->status == 'accepted')
                                        <span class="badge bg-success">{{ $pembayaran->status_label }}</span>
                                    @elseif($pembayaran->status == 'rejected')
                                        <span class="badge bg-danger">{{ $pembayaran->status_label }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ $pembayaran->status_label }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Metode</th>
                                <td>{{ $pembayaran->metode }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Upload</th>
                                <td>{{ $pembayaran->tanggal_upload->format('d F Y H:i') }}</td>
                            </tr>
                            @if($pembayaran->tanggal_proses)
                            <tr>
                                <th>Tanggal Proses</th>
                                <td>{{ $pembayaran->tanggal_proses->format('d F Y H:i') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Admin</th>
                                <td>{{ $pembayaran->admin ? $pembayaran->admin->nama : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($pembayaran->tagihan)
                <div class="mt-4">
                    <h6>Informasi Tagihan</h6>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th width="30%">Keterangan Tagihan</th>
                            <td>{{ $pembayaran->tagihan->keterangan }}</td>
                        </tr>
                        <tr>
                            <th>Total Tagihan</th>
                            <td class="fw-bold">{{ $pembayaran->tagihan->jumlah_formatted }}</td>
                        </tr>
                        <tr>
                            <th>Total Dibayar</th>
                            <td class="fw-bold text-success">{{ $pembayaran->tagihan->total_dibayar_formatted ?? 'Rp 0' }}</td>
                        </tr>
                        <tr>
                            <th>Sisa Tagihan</th>
                            <td class="fw-bold text-warning">{{ $pembayaran->tagihan->sisa_tagihan_formatted ?? $pembayaran->tagihan->jumlah_formatted }}</td>
                        </tr>
                    </table>
                </div>
                @endif

                @if($pembayaran->alasan_reject)
                <div class="alert alert-danger mt-3">
                    <h6 class="alert-heading">Alasan Penolakan</h6>
                    <p class="mb-0">{{ $pembayaran->alasan_reject }}</p>
                </div>
                @endif

                @if($pembayaran->bukti)
                <div class="mt-4">
                    <h6>Bukti Pembayaran</h6>
                    @if(in_array(pathinfo($pembayaran->bukti, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="{{ Storage::url($pembayaran->bukti) }}" alt="Bukti Pembayaran" class="img-fluid rounded" style="max-height: 400px;">
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-file-pdf me-2"></i>
                            <a href="{{ Storage::url($pembayaran->bukti) }}" target="_blank" class="text-decoration-none">
                                Lihat Dokumen PDF
                            </a>
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @if($pembayaran->status == 'accepted')
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0">Aksi</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.pembayaran.kuitansi', $pembayaran->id) }}" class="btn btn-success w-100 mb-2">
                    <i class="bi bi-receipt me-2"></i>Download Kuitansi
                </a>
            </div>
        </div>
        @endif

        @if($pembayaran->tagihan && $pembayaran->tagihan->sisa_tagihan > 0)
        <div class="material-card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Riwayat Cicilan</h5>
            </div>
            <div class="card-body">
                @php
                    $cicilan = $pembayaran->tagihan->pembayaran()
                        ->where('status', 'accepted')
                        ->orderBy('tanggal_proses', 'asc')
                        ->get();
                @endphp
                
                @foreach($cicilan as $cicil)
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <div>
                        <div class="fw-semibold">{{ $cicil->jumlah_formatted }}</div>
                        <small class="text-muted">{{ $cicil->tanggal_proses->format('d/m/Y') }}</small>
                    </div>
                    <span class="badge bg-{{ $cicil->isCicilan() ? 'warning' : 'success' }}">
                        {{ $cicil->jenis_bayar_label }}
                    </span>
                </div>
                @endforeach
                
                <div class="mt-3 p-2 bg-light rounded">
                    <div class="d-flex justify-content-between">
                        <strong>Total Dibayar:</strong>
                        <strong class="text-success">{{ $pembayaran->tagihan->total_dibayar_formatted }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong>Sisa:</strong>
                        <strong class="text-warning">{{ $pembayaran->tagihan->sisa_tagihan_formatted }}</strong>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection