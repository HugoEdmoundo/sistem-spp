@extends('layouts.app')

@section('title', 'Pengeluaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="bi bi-cash-coin me-2"></i>Data Pengeluaran</h4>
    <a href="{{ route('admin.pengeluaran.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tambah Pengeluaran
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th>Jumlah</th>
                        <th>Admin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengeluaran as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-info">{{ $item->kategori }}</span>
                        </td>
                        <td>{{ $item->keterangan }}</td>
                        <td class="text-danger">- Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td>{{ $item->admin->nama }}</td>
                        <td>
                            <!-- Tombol Hapus -->
                            <form action="{{ route('admin.pengeluaran.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pengeluaran?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>

                            <!-- Tombol Edit -->
                            <a href="{{ route('admin.pengeluaran.edit', $item->id) }}" 
                            class="btn btn-sm btn-warning" 
                            onclick="return confirm('Edit pengeluaran?')">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-warning">
                        <td colspan="4" class="text-end"><strong>Total Pengeluaran:</strong></td>
                        <td colspan="3" class="text-danger">
                            <strong>- Rp {{ number_format($pengeluaran->sum('jumlah'), 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection