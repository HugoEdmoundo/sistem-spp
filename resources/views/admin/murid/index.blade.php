<!-- resources/views/admin/murid/index.blade.php -->
@extends('layouts.app')

@section('title', 'Kelola Murid')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Data Murid</h4>
    <a href="{{ route('admin.murid.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Murid
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>NIP</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($murid as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->username }}</td>
                        <td>{{ $item->nip ?? '-' }}</td>
                        <td>
                            @if($item->aktif)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <!-- Edit -->
                                <a href="{{ route('admin.murid.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- Aktif / Nonaktif -->
                                <form action="{{ route('admin.murid.toggle', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $item->aktif ? 'btn-secondary' : 'btn-success' }}">
                                        <i class="bi {{ $item->aktif ? 'bi-x-lg' : 'bi-check-lg' }}"></i>
                                    </button>
                                </form>

                                <!-- Reset Password -->
                                <form action="{{ route('admin.murid.reset-password', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-info" onclick="return confirm('Reset password ke 123456789?')">
                                        <i class="bi bi-key"></i>
                                    </button>
                                </form>
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