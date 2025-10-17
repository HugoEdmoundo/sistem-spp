<!-- resources/views/murid/pembayaran/history.blade.php -->
@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Riwayat Pembayaran Saya</h4>
    <div>
        <a href="{{ route('murid.dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Dashboard
        </a>
        <a href="{{ route('murid.tagihan.index') }}" class="btn btn-primary">
            <i class="bi bi-file-invoice me-2"></i> Lihat Tagihan
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Keterangan</th>
                        <th>Metode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Bukti</th>
                        <th>Tanggal Upload</th>
                        <th>Tanggal Proses</th>
                        <th>Admin</th>
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
                            @endif
                        </td>
                        <td>{{ $item->metode }}</td>
                        <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
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
                            @if($item->bukti)
                            <a href="{{ asset('storage/' . $item->bukti) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Lihat
                            </a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $item->tanggal_upload->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($item->tanggal_proses)
                                {{ $item->tanggal_proses->format('d/m/Y H:i') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $item->admin ? $item->admin->nama : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="bi bi-history fa-3x text-muted mb-3"></i>
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
@endsection