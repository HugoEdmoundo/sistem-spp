<!-- resources/views/admin/tagihan/index.blade.php -->
@extends('layouts.app')

@section('title', 'Kelola Tagihan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Data Tagihan</h4>
    <a href="{{ route('admin.tagihan.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tambah Tagihan
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
                        <td>
                            <span class="fw-bold text-success">{{ $item->jumlah_formatted }}</span>
                        </td>

                        <td>
                            @if($item->status == 'unpaid')
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i>Belum Bayar
                                </span>
                            @elseif($item->status == 'pending')
                                <span class="badge bg-warning">
                                    <i class="bi bi-clock me-1"></i>Pending
                                </span>
                            @elseif($item->status == 'success')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Lunas
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-slash-circle me-1"></i>Ditolak
                                </span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex gap-1">
                                <!-- Edit Button -->
                                @if($item->can_edit)
                                <a href="{{ route('admin.tagihan.edit', $item->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Tagihan">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @else
                                <button class="btn btn-sm btn-warning" disabled data-bs-toggle="tooltip" title="Tidak dapat edit tagihan yang sudah dibayar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @endif
                                
                                <!-- Delete Button -->
                                @if($item->can_delete)
                                <form action="{{ route('admin.tagihan.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Hapus Tagihan" onclick="return confirm('Apakah Anda yakin ingin menghapus tagihan ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @else
                                <button class="btn btn-sm btn-danger" disabled data-bs-toggle="tooltip" title="Tidak dapat menghapus tagihan yang sudah dibayar">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection