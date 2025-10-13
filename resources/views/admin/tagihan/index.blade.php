<!-- resources/views/admin/tagihan/index.blade.php -->
@extends('layouts.app')

@section('title', 'Kelola Tagihan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Data Tagihan</h4>
    <a href="{{ route('admin.tagihan.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Tagihan Custom
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
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th>Bulan/Tahun</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tagihan as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->user->nama }}</td>
                        <td>
                            <span class="badge {{ $item->jenis == 'spp' ? 'bg-primary' : 'bg-info' }}">
                                {{ strtoupper($item->jenis) }}
                            </span>
                        </td>
                        <td>{{ $item->keterangan }}</td>
                        <td>{{ $item->periode }}</td>
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
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if(!$item->pembayaran)
                            <form action="{{ route('admin.tagihan.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus tagihan?')">
                                    <i class="bi bi-trash"></i> 
                                </button>
                            </form>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection