<!-- resources/views/admin/murid/index.blade.php -->
@extends('layouts.app')

@section('title', 'Kelola Murid')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800 font-weight-bold">Kelola Murid</h1>
            <p class="mb-0 text-muted">Kelola data dan status keaktifan murid di sistem</p>
        </div>
        <a href="{{ route('admin.murid.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus-circle mr-2"></i>Tambah Murid Baru
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Murid</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($murid) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Murid Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $murid->where('aktif', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Murid Nonaktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $murid->where('aktif', false)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Dengan NIS</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $murid->whereNotNull('nip')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Murid</h6>
            <div class="d-flex">
                <div class="input-group input-group-sm mr-2" style="width: 200px;">
                    <input type="text" class="form-control" placeholder="Cari murid..." id="searchInput">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="dataTable">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th>Nama Murid</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>NIS</th>
                            <th width="120" class="text-center">Status</th>
                            <th width="200" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($murid as $index => $item)
                        <tr>
                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm mr-3">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center font-weight-bold" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($item->nama, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-weight-semibold">{{ $item->nama }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-envelope text-muted mr-2"></i>
                                    <span>{{ $item->email }}</span>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user text-muted mr-2"></i>
                                    <span>{{ $item->username }}</span>
                                </div>
                            </td>
                            <td class="align-middle">
                                @if($item->nip)
                                <span class="badge badge-primary border">{{ $item->nip }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if($item->aktif)
                                    <span class="badge badge-success badge-pill py-1 px-3">
                                        <i class="fas fa-check-circle mr-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="badge badge-danger badge-pill py-1 px-3">
                                        <i class="fas fa-times-circle mr-1"></i>Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('admin.murid.edit', $item->id) }}" class="btn btn-sm btn-outline-primary mr-1" title="Edit Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.murid.toggle', $item->id) }}" method="POST" class="d-inline mr-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $item->aktif ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                                title="{{ $item->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="fas {{ $item->aktif ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.murid.reset-password', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-info" 
                                                onclick="return confirm('Reset password murid {{ $item->nama }} ke 123456789?')"
                                                title="Reset Password">
                                            <i class="fas fa-key"></i>
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
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Menampilkan <span class="font-weight-bold">{{ count($murid) }}</span> data murid
                </div>
                <div class="small">
                    <a href="{{ route('admin.murid.create') }}" class="text-primary font-weight-bold">
                        <i class="fas fa-plus mr-1"></i>Tambah Murid Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.card {
    border-radius: 0.5rem;
}
.btn-sm {
    border-radius: 0.35rem;
}
.badge-pill {
    border-radius: 50rem;
}
</style>

<script>
// Simple search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#dataTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});
</script>
@endsection