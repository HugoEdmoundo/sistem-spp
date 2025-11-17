<!-- resources/views/admin/laporan/index.blade.php -->
@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="bg-primary rounded p-2 me-3">
                <i class="bi bi-file-earmark-text text-white fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">Laporan Keuangan</h4>
                <p class="text-muted mb-0">Laporan SPP, Tagihan, dan Pengeluaran</p>
            </div>
        </div>
    </div>

    <!-- Filter Tahun -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                    <select name="tahun" class="form-select" id="tahun" required>
                        <option value="">Pilih Tahun</option>
                        @foreach($tahunUntukSelect as $tahunItem)
                            <option value="{{ $tahunItem }}" {{ $tahunItem == $tahun ? 'selected' : '' }}>
                                {{ $tahunItem }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Laporan SPP -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="bi bi-file-earmark-text me-2"></i>Laporan Pembayaran SPP
                            </h5>
                            <small class="opacity-75">Tahun {{ $tahun }} - Status Per Bulan</small>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('admin.laporan.export.spp.excel', $tahun) }}" 
                               class="btn btn-light btn-sm" 
                               title="Export Excel">
                                <i class="bi bi-file-earmark-excel text-success me-1"></i> Excel
                            </a>
                            <a href="{{ route('admin.laporan.export.spp.pdf', $tahun) }}" 
                               class="btn btn-light btn-sm" 
                               title="Export PDF">
                                <i class="bi bi-file-earmark-pdf text-danger me-1"></i> PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(is_array($dataSpp) && count($dataSpp) > 0)
                        <!-- Tampilkan data SPP yang sudah dipisah -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Siswa</th>
                                        @for($i = 1; $i <= 12; $i++)
                                        <th class="text-center">{{ \App\Models\User::getNamaBulanStatic($i) }}</th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataSpp as $dataMurid)
                                    <tr>
                                        <td class="fw-bold">{{ $dataMurid['murid']->nama }}</td>
                                        @for($i = 1; $i <= 12; $i++)
                                        <td class="text-center">
                                            @php $bulanData = $dataMurid['bulan'][$i]; @endphp
                                            @if($bulanData['status'] === 'LUNAS')
                                                <span class="badge bg-success">LUNAS</span>
                                            @elseif($bulanData['status'] === 'CICILAN')
                                                <span class="badge bg-warning">CICILAN</span>
                                            @else
                                                <span class="badge bg-secondary">BELUM</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                Rp {{ number_format($bulanData['total_dibayar'], 0, ',', '.') }}
                                            </small>
                                        </td>
                                        @endfor
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-file-earmark-text display-1 text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted mb-2">Tidak ada data SPP</h5>
                        <p class="text-muted mb-0">Tidak ada data pembayaran SPP untuk tahun {{ $tahun }}.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Laporan Tagihan (Non-SPP) -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="bi bi-receipt me-2"></i>Laporan Tagihan (Non-SPP)
                            </h5>
                            <small class="opacity-75">Tahun {{ $tahun }}</small>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('admin.laporan.export.tagihan.excel', $tahun) }}" 
                               class="btn btn-light btn-sm" 
                               title="Export Excel">
                                <i class="bi bi-file-earmark-excel text-success me-1"></i> Excel
                            </a>
                            <a href="{{ route('admin.laporan.export.tagihan.pdf', $tahun) }}" 
                               class="btn btn-light btn-sm" 
                               title="Export PDF">
                                <i class="bi bi-file-earmark-pdf text-danger me-1"></i> PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($dataTagihan) && $dataTagihan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Jenis Tagihan</th>
                                    <th>Keterangan</th>
                                    <th class="text-end">Total Tagihan</th>
                                    <th class="text-end">Dibayar</th>
                                    <th class="text-end">Sisa</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataTagihan as $index => $tagihan)
                                <tr>
                                    <td class="fw-semibold">{{ $index + 1 }}</td>
                                    <td>{{ $tagihan->user->nama ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $tagihan->jenis }}</span>
                                    </td>
                                    <td class="text-muted">{{ $tagihan->keterangan }}</td>
                                    <td class="text-end fw-bold text-dark">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold text-success">Rp {{ number_format($tagihan->total_dibayar, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold text-danger">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge 
                                            @if($tagihan->status_detail === 'LUNAS') bg-success
                                            @elseif($tagihan->status_detail === 'CICILAN') bg-warning
                                            @else bg-secondary @endif">
                                            {{ $tagihan->status_detail }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-receipt display-1 text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted mb-2">Tidak ada data tagihan</h5>
                        <p class="text-muted mb-0">Tidak ada data tagihan non-SPP untuk tahun {{ $tahun }}.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Laporan Pengeluaran -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="bi bi-receipt me-2"></i>Laporan Pengeluaran
                            </h5>
                            <small class="opacity-75">Tahun {{ $tahun }}</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-25 px-3 py-1 rounded">
                                <span class="fw-medium">Total: <strong>Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</strong></span>
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('admin.laporan.export.pengeluaran.excel', $tahun) }}" 
                                   class="btn btn-light btn-sm" 
                                   title="Export Excel">
                                    <i class="bi bi-file-earmark-excel text-success me-1"></i> Excel
                                </a>
                                <a href="{{ route('admin.laporan.export.pengeluaran.pdf', $tahun) }}" 
                                   class="btn btn-light btn-sm" 
                                   title="Export PDF">
                                    <i class="bi bi-file-earmark-pdf text-danger me-1"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($pengeluaran) && $pengeluaran->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="12%">Tanggal</th>
                                    <th width="15%">Kategori</th>
                                    <th width="35%">Keterangan</th>
                                    <th width="15%" class="text-end">Jumlah</th>
                                    <th width="18%">Dibuat Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengeluaran as $index => $p)
                                <tr>
                                    <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="fw-medium">{{ $p->tanggal->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $p->kategori }}</span>
                                    </td>
                                    <td class="text-muted">{{ $p->keterangan }}</td>
                                    <td class="text-end fw-bold text-danger">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($p->admin->foto)
                                                <img src="{{ Storage::url($p->admin->foto) }}" 
                                                     alt="{{ $p->admin->nama }}" 
                                                     class="rounded-circle me-2" 
                                                     width="24" 
                                                     height="24">
                                            @else
                                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                     style="width: 24px; height: 24px;">
                                                    <span class="text-white fw-bold small">{{ substr($p->admin->nama ?? 'A', 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <span class="small">{{ $p->admin->nama ?? '-' }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-group-divider">
                                <tr class="fw-bold bg-light">
                                    <td colspan="4" class="text-end">TOTAL PENGELUARAN:</td>
                                    <td class="text-end text-danger fs-6">Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-receipt display-1 text-muted opacity-50"></i>
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
@endsection