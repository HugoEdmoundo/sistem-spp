<!-- resources/views/admin/pembayaran/index.blade.php -->
@extends('layouts.app')

@section('title', 'Pembayaran Pending')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Pembayaran Menunggu Verifikasi</h4>
    <a href="{{ route('admin.pembayaran.history') }}" class="btn btn-secondary">
        <i class="fas fa-history"></i> Riwayat Pembayaran
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($pembayaran->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Murid</th>
                        <th>Tagihan</th>
                        <th>Metode</th>
                        <th>Jumlah</th>
                        <th>Bukti</th>
                        <th>Tanggal Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembayaran as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->user->nama }}</td>
                        <td>{{ $item->tagihan->keterangan }}</td>
                        <td>{{ $item->metode }}</td>
                        <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
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
                            <div class="btn-group">
                                <form action="{{ route('admin.pembayaran.approve', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setujui pembayaran?')">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.pembayaran.reject', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tolak pembayaran?')">
                                        <i class="fas fa-times"></i> Reject
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
        <div class="text-center py-4">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <p>Tidak ada pembayaran yang menunggu verifikasi.</p>
        </div>
        @endif
    </div>
</div>
@endsection