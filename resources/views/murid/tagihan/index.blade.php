<!-- resources/views/murid/tagihan/index.blade.php -->
@extends('layouts.app')

@section('title', 'Tagihan Saya')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="bg-primary rounded p-2 me-3">
                <i class="bi bi-receipt text-white fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">Tagihan Saya</h4>
                <p class="text-muted mb-0">Bayar tagihan secara cicilan seperti SPP</p>
            </div>
        </div>
    </div>

    <!-- Alert Section -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Info Sistem Cicilan -->
    <div class="alert alert-info">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle-fill me-3 fs-4"></i>
            <div>
                <h6 class="alert-heading mb-2">Sistem Pembayaran Cicilan</h6>
                <p class="mb-1">✅ Bayar berapapun (minimal Rp 1.000)</p>
                <p class="mb-1">✅ Bisa bayar berkali-kali sampai lunas</p>
                <p class="mb-0">✅ Progress pembayaran akan tercatat</p>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <!-- Filter Jenis -->
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-filter me-2"></i>
                @if(request('jenis'))
                    {{ request('jenis') == 'spp' ? 'SPP' : 'Non-SPP' }}
                @else
                    Semua Jenis
                @endif
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['jenis' => '']) }}">Semua Jenis</a></li>
                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['jenis' => 'spp']) }}">SPP</a></li>
                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['jenis' => 'non-spp']) }}">Non-SPP</a></li>
            </ul>
        </div>
    </div>

    <!-- Card Content -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="bi bi-list-ul text-primary me-2"></i>
                Daftar Tagihan
            </h5>
        </div>
        <div class="card-body p-0">
            @if($tagihan->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">#</th>
                            <th scope="col">Jenis</th>
                            <th scope="col">Keterangan</th>
                            <th scope="col">Total Tagihan</th>
                            <th scope="col">Status</th>
                            <th scope="col">Progress</th>
                            <th scope="col" class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tagihan as $index => $item)
                            <tr class="align-middle">
                                <td class="ps-4 fw-semibold">{{ $index + 1 }}</td>
                                
                                <!-- Jenis -->
                                <td>
                                    @if($item->jenis == 'spp')
                                        <span class="badge bg-info">SPP</span>
                                    @else
                                        <span class="badge bg-secondary">Tagihan</span>
                                    @endif
                                </td>
                            
                                <!-- Keterangan -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium text-dark">{{ $item->keterangan }}</span>
                                        <small class="text-muted mt-1">
                                            Dibuat: {{ $item->created_at->format('d/m/Y') }}
                                        </small>
                                        @if($item->is_cicilan)
                                        <div class="mt-2">
                                            <small class="text-success">
                                                <i class="bi bi-currency-dollar me-1"></i>
                                                Sudah dibayar: {{ $item->total_dibayar_formatted }}
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Jumlah Tagihan -->
                                <td>
                                    <div class="fw-bold text-primary">{{ $item->jumlah_formatted }}</div>
                                    @if($item->is_cicilan)
                                    <small class="text-warning">
                                        Sisa: {{ $item->sisa_tagihan_formatted }}
                                    </small>
                                    @endif
                                </td>
                                
                                <!-- Status -->
                                <td>
                                    @if($item->is_lunas)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Lunas
                                        </span>
                                    @elseif($item->is_pending)
                                        <span class="badge bg-warning">
                                            <i class="bi bi-clock me-1"></i>Menunggu
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>Belum Lunas
                                        </span>
                                        @if($item->is_cicilan)
                                        <div class="mt-1">
                                            <small class="text-info">(Sedang dicicil)</small>
                                        </div>
                                        @endif
                                    @endif
                                </td>
                                
                                <!-- Progress -->
                                <td>
                                    @if($item->is_cicilan || !$item->is_lunas)
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 me-3">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success" 
                                                     style="width: {{ number_format($item->persentase_dibayar, 1) }}%"
                                                     title="{{ number_format($item->persentase_dibayar, 1) }}%">
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted" style="min-width: 45px;">
                                            {{ number_format($item->persentase_dibayar, 1) }}%
                                        </small>
                                    </div>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <!-- Aksi -->
                                <td class="text-center pe-4">
                                    @if($item->bisaBayar())
                                    <div class="btn-group" role="group">
                                        <!-- Tombol Bayar/Cicil -->
                                        <button type="button" 
                                                class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#uploadModal{{ $item->id }}"
                                                title="{{ $item->is_cicilan ? 'Lanjutkan Cicilan' : 'Bayar Tagihan' }}">
                                            <i class="bi bi-credit-card me-1"></i>
                                            {{ $item->is_cicilan ? 'Cicil' : 'Bayar' }}
                                        </button>
                                        
                                        <!-- Tombol Riwayat -->
                                        @if($item->pembayaran->count() > 0)
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#riwayatModal{{ $item->id }}"
                                                title="Lihat Riwayat">
                                            <i class="bi bi-clock-history"></i>
                                        </button>
                                        @endif
                                    </div>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Modal Bayar/Cicil -->
                            <div class="modal fade" id="uploadModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title">
                                                <i class="bi bi-credit-card me-2"></i>
                                                @if($item->is_cicilan)
                                                    Lanjutkan Cicilan
                                                @else
                                                    Bayar Tagihan
                                                @endif
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <form action="{{ route('murid.tagihan.upload-bukti', $item->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-body">
                                                <!-- Info Tagihan -->
                                                <div class="card border-primary mb-3">
                                                    <div class="card-body">
                                                        <h6 class="card-title">{{ $item->keterangan }}</h6>
                                                        <div class="row small">
                                                            <div class="col-6">
                                                                <span class="text-muted">Total Tagihan:</span>
                                                                <div class="fw-bold text-primary">{{ $item->jumlah_formatted }}</div>
                                                            </div>
                                                            @if($item->is_cicilan)
                                                            <div class="col-6">
                                                                <span class="text-muted">Sudah Dibayar:</span>
                                                                <div class="fw-bold text-success">{{ $item->total_dibayar_formatted }}</div>
                                                            </div>
                                                            <div class="col-6 mt-2">
                                                                <span class="text-muted">Sisa Tagihan:</span>
                                                                <div class="fw-bold text-warning">{{ $item->sisa_tagihan_formatted }}</div>
                                                            </div>
                                                            <div class="col-6 mt-2">
                                                                <span class="text-muted">Minimal Bayar:</span>
                                                                <div class="fw-bold text-info">
                                                                    Rp {{ number_format($item->getMinimalPembayaranBerikutnya(), 0, ',', '.') }}
                                                                </div>
                                                            </div>
                                                            @else
                                                            <div class="col-6">
                                                                <span class="text-muted">Status:</span>
                                                                <div class="fw-bold text-danger">Belum Bayar</div>
                                                            </div>
                                                            <div class="col-12 mt-2">
                                                                <span class="text-muted">Minimal Bayar:</span>
                                                                <div class="fw-bold text-info">Rp 1.000</div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <!-- Progress Bar -->
                                                        @if($item->is_cicilan)
                                                        <div class="mt-3">
                                                            <div class="progress" style="height: 10px;">
                                                                <div class="progress-bar bg-success" style="width: {{ $item->persentase_dibayar }}%"></div>
                                                            </div>
                                                            <small class="text-muted">{{ number_format($item->persentase_dibayar, 1) }}% terbayar</small>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Input Jumlah -->
                                                <div class="mb-3">
                                                    <label class="form-label">Jumlah Bayar *</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" 
                                                            class="form-control" 
                                                            name="jumlah" 
                                                            id="jumlahInput{{ $item->id }}"
                                                            value="{{ $item->is_cicilan ? $item->getMinimalPembayaranBerikutnya() : 1000 }}" 
                                                            min="{{ $item->is_cicilan ? $item->getMinimalPembayaranBerikutnya() : 1000 }}" 
                                                            max="{{ $item->sisa_tagihan }}"
                                                            required
                                                            onchange="updateSisaInfo{{ $item->id }}(this.value)">
                                                    </div>
                                                    <div class="form-text">
                                                        @if($item->is_cicilan)
                                                            Minimal: Rp {{ number_format($item->getMinimalPembayaranBerikutnya(), 0, ',', '.') }}, 
                                                            Maksimal: {{ $item->sisa_tagihan_formatted }}
                                                        @else
                                                            Minimal: Rp 1.000, Maksimal: {{ $item->jumlah_formatted }}
                                                        @endif
                                                    </div>
                                                    
                                                    <!-- Info Sisa Setelah Bayar -->
                                                    <div id="sisaInfo{{ $item->id }}" class="mt-2" style="display: none;">
                                                        <small class="text-info">
                                                            <i class="bi bi-info-circle me-1"></i>
                                                            Sisa setelah bayar: <span id="sisaAmount{{ $item->id }}" class="fw-bold"></span>
                                                        </small>
                                                    </div>
                                                    
                                                    <!-- Info Akan Lunas -->
                                                    <div id="lunasInfo{{ $item->id }}" class="mt-2 alert alert-success py-1" style="display: none;">
                                                        <small><i class="bi bi-check-circle me-1"></i>Pembayaran ini akan melunasi tagihan!</small>
                                                    </div>
                                                </div>

                                                <!-- Metode Pembayaran -->
                                                <div class="mb-3">
                                                    <label class="form-label">Metode Pembayaran *</label>
                                                    <select class="form-select" name="metode" required>
                                                        <option value="">Pilih Metode</option>
                                                        <option value="Transfer">Transfer Bank</option>
                                                        <option value="Tunai">Tunai</option>
                                                        <option value="QRIS">QRIS</option>
                                                        <option value="E-Wallet">E-Wallet</option>
                                                    </select>
                                                </div>

                                                <!-- Bukti Pembayaran -->
                                                <div class="mb-3">
                                                    <label class="form-label">Bukti Pembayaran *</label>
                                                    <input type="file" class="form-control" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required>
                                                    <div class="form-text">Format: JPG, PNG, PDF (Maks. 2MB)</div>
                                                </div>

                                                <!-- Keterangan -->
                                                <div class="mb-3">
                                                    <label class="form-label">Keterangan *</label>
                                                    <input type="text" class="form-control" name="keterangan" 
                                                        value="{{ $item->is_cicilan ? 'Cicilan ' . $item->keterangan : 'Bayar ' . $item->keterangan }}" 
                                                        required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-upload me-1"></i>
                                                    Upload Bukti
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Riwayat -->
                            <div class="modal fade" id="riwayatModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title">
                                                <i class="bi bi-clock-history me-2"></i>
                                                Riwayat Pembayaran
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h6>{{ $item->keterangan }}</h6>
                                            <p class="text-muted">Total Tagihan: {{ $item->jumlah_formatted }}</p>
                                            
                                            @if($item->pembayaran->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Jumlah</th>
                                                            <th>Metode</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($item->pembayaran->sortByDesc('created_at') as $pembayaran)
                                                        <tr>
                                                            <td>{{ $pembayaran->tanggal_upload->format('d/m/Y H:i') }}</td>
                                                            <td class="fw-bold text-success">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                                                            <td>{{ $pembayaran->metode }}</td>
                                                            <td>
                                                                @if($pembayaran->status == 'accepted')
                                                                    <span class="badge bg-success">Diterima</span>
                                                                @elseif($pembayaran->status == 'pending')
                                                                    <span class="badge bg-warning">Menunggu</span>
                                                                @else
                                                                    <span class="badge bg-danger">Ditolak</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @else
                                            <div class="text-center py-4">
                                                <i class="bi bi-receipt display-1 text-muted"></i>
                                                <p class="text-muted mt-3">Belum ada riwayat pembayaran</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Script untuk setiap item -->
                            <script>
                            function updateSisaInfo{{ $item->id }}(jumlah) {
                                const sisaSekarang = {{ $item->sisa_tagihan }};
                                const jumlahNum = parseInt(jumlah) || 0;
                                const sisaSetelahBayar = sisaSekarang - jumlahNum;
                                
                                // Update info sisa
                                if (jumlahNum > 0) {
                                    document.getElementById('sisaInfo{{ $item->id }}').style.display = 'block';
                                    document.getElementById('sisaAmount{{ $item->id }}').textContent = 'Rp ' + (sisaSetelahBayar > 0 ? sisaSetelahBayar.toLocaleString('id-ID') : '0');
                                } else {
                                    document.getElementById('sisaInfo{{ $item->id }}').style.display = 'none';
                                }
                                
                                // Update info lunas
                                if (sisaSetelahBayar <= 0) {
                                    document.getElementById('lunasInfo{{ $item->id }}').style.display = 'block';
                                } else {
                                    document.getElementById('lunasInfo{{ $item->id }}').style.display = 'none';
                                }
                            }

                            // Initialize on page load untuk item ini
                            document.addEventListener('DOMContentLoaded', function() {
                                @if($item->is_cicilan)
                                updateSisaInfo{{ $item->id }}({{ $item->getMinimalPembayaranBerikutnya() }});
                                @else
                                updateSisaInfo{{ $item->id }}(1000);
                                @endif
                            });
                            </script>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($tagihan->hasPages())
            <div class="card-footer bg-white border-top">
                <div class="d-flex justify-content-center">
                    {{ $tagihan->links() }}
                </div>
            </div>
            @endif

            @else
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada tagihan</h5>
                <p class="text-muted mb-0">Semua tagihan sudah lunas atau belum ada tagihan.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Global Script (jika diperlukan) -->
<script>
// Script global bisa ditaruh di sini jika diperlukan
</script>
@endsection