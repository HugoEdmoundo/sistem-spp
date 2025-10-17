<!-- resources/views/admin/pembayaran/history.blade.php -->
@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Riwayat Pembayaran</h4>
    <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-primary">
        <i class="bi bi-clock me-2"></i> Pembayaran Pending
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Murid</th>
                        <th>Keterangan</th>
                        <th>Metode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Bukti</th>
                        <th>Admin</th>
                        <th>Tanggal Proses</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembayaran as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->user->nama }}</td>
                        <td>
                            @if($item->tagihan)
                                {{ $item->tagihan->keterangan }}
                                <br>
                                <small class="text-muted">(Tagihan)</small>
                            @else
                                {{ $item->keterangan ?? 'Pembayaran SPP Fleksibel' }}
                                <br>
                                <small class="text-muted">(Fleksibel)</small>
                            @endif
                        </td>
                        <td>{{ $item->metode }}</td>
                        <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td>
                            @if($item->status == 'accepted')
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
                        <td>{{ $item->admin ? $item->admin->nama : '-' }}</td>
                        <td>
                            @if($item->tanggal_proses)
                                {{ $item->tanggal_proses->format('d/m/Y H:i') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
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