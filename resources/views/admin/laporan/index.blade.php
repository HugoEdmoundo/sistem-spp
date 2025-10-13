@extends('layouts.app')

@section('title', 'Laporan & Export')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="bi bi-file-earmark-spreadsheet me-2"></i>Laporan & Export Data</h4>
        </div>
    </div>
</div>

<div class="row">
    <!-- Export Murid -->
    <div class="col-md-4 mb-4">
        <div class="material-card h-100">
            <div class="card-body text-center">
                <div class="stat-icon bg-primary mb-3 mx-auto">
                    <i class="bi bi-people fs-2"></i>
                </div>
                <h5 class="card-title">Data Murid</h5>
                <p class="text-muted">Export semua data murid ke Excel</p>
                <form action="{{ route('admin.export.murid') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-download me-2"></i>Export Excel
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Export Tagihan -->
    <div class="col-md-8 mb-4">
        <div class="material-card h-100">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="bi bi-receipt me-2"></i>Laporan Tagihan
                </h5>
                <form action="{{ route('admin.export.tagihan') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-download me-2"></i>Export
                            </button>
                        </div>
                    </div>
                    <small class="text-muted">Kosongkan tanggal untuk export semua data</small>
                </form>
            </div>
        </div>
    </div>

    <!-- Export Pembayaran -->
    <div class="col-md-8 mb-4">
        <div class="material-card h-100">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="bi bi-credit-card me-2"></i>Laporan Pembayaran
                </h5>
                <form action="{{ route('admin.export.pembayaran') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bi bi-download me-2"></i>Export
                            </button>
                        </div>
                    </div>
                    <small class="text-muted">Kosongkan tanggal untuk export semua data</small>
                </form>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="col-md-4 mb-4">
        <div class="material-card h-100">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="bi bi-graph-up me-2"></i>Statistik Cepat
                </h5>
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total Murid:</span>
                            <strong>{{ \App\Models\User::where('role', 'murid')->count() }}</strong>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Tagihan Bulan Ini:</span>
                            <strong>Rp {{ number_format(\App\Models\Tagihan::whereMonth('created_at', now()->month)->sum('jumlah'), 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Pembayaran Pending:</span>
                            <strong>{{ \App\Models\Pembayaran::where('status', 'pending')->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection