@extends('layouts.app')

@section('title', 'Backup Database')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="bi bi-database me-2"></i>Backup Database</h4>
            <form action="{{ route('admin.backup.create') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Backup Sekarang
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0">Backup Terakhir</h5>
            </div>
            <div class="card-body">
                @if($backups->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama File</th>
                                <th>Ukuran</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                            <tr>
                                <td>{{ $backup['name'] }}</td>
                                <td>{{ $backup['size'] }}</td>
                                <td>{{ $backup['date'] }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.backup.download', ['file' => $backup['name']]) }}" 
                                           class="btn btn-sm btn-success">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <form action="{{ route('admin.backup.delete', ['file' => $backup['name']]) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Hapus backup ini?')">
                                                <i class="bi bi-trash"></i>
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
                    <i class="bi bi-database-x fs-1 text-muted mb-3"></i>
                    <p class="text-muted">Belum ada backup database.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0">Informasi Backup</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Jadwal Otomatis:</strong>
                    <ul class="mt-2">
                        <li>Backup: Setiap Minggu jam 02:00</li>
                        <li>Pembersihan: Setiap Hari jam 01:00</li>
                    </ul>
                </div>
                <div class="mb-3">
                    <strong>Retensi:</strong>
                    <p class="mt-1">Backup disimpan selama 30 hari</p>
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Backup otomatis menggunakan spatie/laravel-backup package.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection