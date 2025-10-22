@extends('layouts.app')

@section('title', 'Pembayaran Pending')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Pembayaran Menunggu Verifikasi</h4>
    <a href="{{ route('admin.pembayaran.history') }}" class="btn btn-primary">
        <i class="bi bi-history me-2"></i>Riwayat Pembayaran
    </a>
</div>

<div class="card">
    <div class="card-header">
        <div class="row">
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
                            
                            <!-- Modal untuk preview bukti -->
                            <div class="modal fade" id="buktiModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Bukti Pembayaran - {{ $item->user->nama }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="{{ asset('storage/' . $item->bukti) }}" 
                                                 alt="Bukti Pembayaran" 
                                                 class="img-fluid rounded" 
                                                 style="max-height: 500px;">
                                            <div class="mt-3">
                                                <a href="{{ asset('storage/' . $item->bukti) }}" 
                                                   download="bukti-{{ $item->user->nama }}-{{ $item->id }}.jpg"
                                                   class="btn btn-sm btn-primary">
                                                    <i class="bi bi-download me-1"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                <form action="{{ route('admin.pembayaran.approve', $item->id) }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-success"
                                            onclick="return confirm('Setujui pembayaran ini?')">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.pembayaran.reject', $item->id) }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-danger"
                                            onclick="return confirm('Tolak pembayaran ini?')">
                                        <i class="bi bi-x-lg"></i> Reject
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
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
@endsection