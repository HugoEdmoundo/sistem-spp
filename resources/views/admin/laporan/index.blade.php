{{-- resources/views/admin/laporan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="main-content">
    <div class="page-header">
        <div class="header-title">
            <h1 class="fw-bold mb-2">Laporan Keuangan</h1>
            <p class="text-muted mb-0">Kelola dan ekspor laporan SPP serta pengeluaran</p>
        </div>
        <div class="header-actions">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Laporan</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Filter Tahun -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="tahun" class="col-form-label fw-medium">Tahun Laporan:</label>
                </div>
                <div class="col-auto">
                    <select name="tahun" id="tahun" class="form-select form-select-sm border-secondary-subtle" style="min-width: 120px;">
                        @for($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tambahkan di view setelah filter --}}
    <div class="alert alert-info">
        <h6>🔍 Debug Detail - Filter Tahun {{ $tahun }}</h6>
        
        @php
            // Hitung statistik
            $totalBulanBayar = 0;
            $totalBulanBelum = 0;
            $muridDenganData = 0;
            
            if (is_array($dataMurid)) {
                foreach ($dataMurid as $data) {
                    $totalBulanBayar += $data['total_bulan_bayar'];
                    $totalBulanBelum += $data['total_bulan_belum_bayar'];
                    if ($data['total_bulan_bayar'] > 0) {
                        $muridDenganData++;
                    }
                }
            }
        @endphp
        
        <p><strong>📊 Statistik Tahun {{ $tahun }}:</strong></p>
        <ul class="mb-2">
            <li>Murid dengan data: <strong>{{ $muridDenganData }}/{{ count($dataMurid) }}</strong></li>
            <li>Total bulan bayar: <strong>{{ $totalBulanBayar }} bulan</strong></li>
            <li>Total bulan belum: <strong>{{ $totalBulanBelum }} bulan</strong></li>
        </ul>
        
        <p class="mb-1"><strong>🔢 Detail per Murid:</strong></p>
        @if(is_array($dataMurid) && count($dataMurid) > 0)
            @foreach($dataMurid as $index => $data)
            <div class="mb-1">
                <small>
                    {{ $index + 1 }}. <strong>{{ $data['murid']->nama }}</strong>: 
                    @if($data['total_bulan_bayar'] > 0)
                        <span class="text-success">✅ {{ $data['total_bulan_bayar'] }} bulan</span>
                    @else
                        <span class="text-danger">❌ 0 bulan</span>
                    @endif
                    - 
                    @if(count($data['sudah_bayar']) > 0)
                        @foreach(array_slice($data['sudah_bayar'], 0, 3) as $bulan)
                            <span class="badge bg-success">{{ $bulan['nama_bulan'] }}</span>
                        @endforeach
                        @if(count($data['sudah_bayar']) > 3)
                            <span class="text-muted">+{{ count($data['sudah_bayar']) - 3 }} lagi</span>
                        @endif
                    @else
                        <span class="text-muted">Tidak ada bulan bayar</span>
                    @endif
                </small>
            </div>
            @endforeach
        @else
            <div class="text-danger">
                <strong>❌ Tidak ada data murid untuk tahun {{ $tahun }}!</strong>
            </div>
        @endif
    </div>

    <div class="row g-3">
        <!-- Laporan SPP -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="m-0 fw-bold">
                            <i class="fas fa-file-invoice-dollar me-2"></i>Laporan SPP
                        </h6>
                        <small class="opacity-75">Tahun {{ $tahun }}</small>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('admin.laporan.export.spp.excel', $tahun) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-file-excel text-success me-1"></i> Excel
                        </a>
                        <a href="{{ route('admin.laporan.export.spp.pdf', $tahun) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-file-pdf text-danger me-1"></i> PDF
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(is_array($dataMurid) && count($dataMurid) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" id="dataTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="ps-3">No</th>
                                    <th width="20%">Nama Murid</th>
                                    <th width="20%">Email</th>
                                    <th width="20%">Bulan Sudah Bayar</th>
                                    <th width="20%">Bulan Belum Bayar</th>
                                    <th width="7%" class="text-center">Total Bayar</th>
                                    <th width="8%" class="text-center">Total Belum</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataMurid as $index => $data)
                                <tr>
                                    <td class="ps-3">{{ $index + 1 }}</td>
                                    <td class="fw-medium">{{ $data['murid']->nama ?? 'N/A' }}</td>
                                    <td class="text-muted">{{ $data['murid']->email ?? 'N/A' }}</td>
                                    <td>
                                        @if(isset($data['sudah_bayar']) && count($data['sudah_bayar']) > 0)
                                            @foreach($data['sudah_bayar'] as $bulan)
                                                <span class="badge bg-success me-1 mb-1">{{ $bulan['nama_bulan'] ?? 'Unknown' }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($data['belum_bayar']) && count($data['belum_bayar']) > 0)
                                            @foreach($data['belum_bayar'] as $bulan)
                                                <span class="badge bg-warning me-1 mb-1">{{ $bulan['nama_bulan'] ?? 'Unknown' }}</span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-success">Semua Lunas</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $data['total_bulan_bayar'] ?? 0 }} Bulan</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $data['total_bulan_belum_bayar'] ?? 0 }} Bulan</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-file-invoice fa-4x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted mb-2">Tidak ada data laporan SPP</h5>
                        <p class="text-muted mb-0">
                            @if(!is_array($dataMurid))
                                Data murid tidak tersedia.
                            @else
                                Tidak ada data murid yang aktif.
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Laporan Pengeluaran -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="m-0 fw-bold">
                            <i class="fas fa-receipt me-2"></i>Laporan Pengeluaran
                        </h6>
                        <small class="opacity-75">Tahun {{ $tahun }}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-3 bg-white bg-opacity-25 px-2 py-1 rounded">
                            <span class="fw-medium">Total: <strong>Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</strong></span>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('admin.laporan.export.pengeluaran.excel', $tahun) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-file-excel text-success me-1"></i> Excel
                            </a>
                            <a href="{{ route('admin.laporan.export.pengeluaran.pdf', $tahun) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-file-pdf text-danger me-1"></i> PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(isset($pengeluaran) && $pengeluaran->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" id="dataTable2" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="ps-3">No</th>
                                    <th width="15%">Tanggal</th>
                                    <th width="15%">Kategori</th>
                                    <th width="30%">Keterangan</th>
                                    <th width="15%" class="text-end pe-3">Jumlah</th>
                                    <th width="20%">Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengeluaran as $index => $p)
                                <tr>
                                    <td class="ps-3">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="fw-medium">{{ $p->tanggal->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $p->kategori }}</span>
                                    </td>
                                    <td class="text-muted">{{ $p->keterangan }}</td>
                                    <td class="text-end fw-bold text-success pe-3">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $p->admin->nama ?? '-' }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-group-divider">
                                <tr class="fw-bold bg-light">
                                    <td colspan="4" class="text-end ps-3">TOTAL PENGELUARAN:</td>
                                    <td class="text-end text-primary fs-6 pe-3">Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-receipt fa-4x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted mb-2">Tidak ada data pengeluaran</h5>
                        <p class="text-muted mb-0">Tidak ada data pengeluaran untuk tahun {{ $tahun }}.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Pastikan konten utama tidak overlap dengan sidebar */
.main-content {
    margin-left: 0;
    padding: 0;
    width: 100%;
}

.content-wrapper {
    padding: 1rem;
    margin-left: 0;
}

/* Responsive adjustments */
@media (min-width: 768px) {
    .content-wrapper {
        margin-left: 280px; /* Sesuaikan dengan lebar sidebar */
        padding: 1.5rem;
    }
}

/* Untuk mobile */
@media (max-width: 767.98px) {
    .content-wrapper {
        margin-left: 0;
        padding: 1rem;
    }
}

/* Table responsive fixes */
.table-responsive {
    border-radius: 0.375rem;
}

.table {
    margin-bottom: 0;
    font-size: 0.875rem;
}

.table th {
    border-top: none;
    padding: 0.75rem;
    background-color: #f8f9fa;
    font-weight: 600;
}

.table td {
    padding: 0.75rem;
    vertical-align: middle;
}

/* Card adjustments */
.card {
    margin-bottom: 1rem;
}

.card-header {
    padding: 1rem 1.25rem;
}

.card-body {
    padding: 0;
}

/* Badge styling */
.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

/* Button adjustments */
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

/* Form controls */
.form-select-sm {
    padding: 0.375rem 2.25rem 0.375rem 0.75rem;
    font-size: 0.875rem;
}
</style>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        @if(is_array($dataMurid) && count($dataMurid) > 0)
        $('#dataTable').DataTable({
            "pageLength": 25,
            "responsive": true,
            "searching": true,
            "info": true,
            "paging": true,
            "autoWidth": false,
            "language": {
                "search": "<i class='fas fa-search me-1'></i>Cari:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Tidak ada data yang ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "paginate": {
                    "previous": "<i class='fas fa-chevron-left'></i>",
                    "next": "<i class='fas fa-chevron-right'></i>"
                }
            },
            "dom": '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>'
        });
        @endif
        
        @if(isset($pengeluaran) && $pengeluaran->count() > 0)
        $('#dataTable2').DataTable({
            "pageLength": 25,
            "responsive": true,
            "order": [[1, 'desc']],
            "searching": true,
            "info": true,
            "paging": true,
            "autoWidth": false,
            "language": {
                "search": "<i class='fas fa-search me-1'></i>Cari:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Tidak ada data yang ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "paginate": {
                    "previous": "<i class='fas fa-chevron-left'></i>",
                    "next": "<i class='fas fa-chevron-right'></i>"
                }
            },
            "dom": '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>'
        });
        @endif

        // Handle responsive behavior
        function handleResponsiveLayout() {
            if (window.innerWidth <= 768) {
                $('.content-wrapper').css('margin-left', '0');
            } else {
                $('.content-wrapper').css('margin-left', '280px');
            }
        }

        // Initial call
        handleResponsiveLayout();

        // Call on resize
        $(window).resize(handleResponsiveLayout);
    });
</script>
@endsection