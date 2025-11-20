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
                {{-- @if($tagihan->count() > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $tagihan->count() }}
                </span>
                @endif --}}
            </div>
            <div>
                <h4 class="mb-0 fw-bold">Tagihan Saya</h4>
                <p class="text-muted mb-0">
                    @if($tagihan->count() > 0)
                        Ada {{ $tagihan->count() }} tagihan belum lunas
                    @else
                        Semua tagihan sudah lunas
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Alert Section -->
    @if(session('success'))
    <div class="custom-alert success d-flex align-items-center">
        <i class="bi bi-check-circle-fill icon"></i>
        <span class="text">{{ session('success') }}</span>
        <button class="close-btn" data-bs-dismiss="alert">&times;</button>
    </div>
    @endif


    <!-- Info Sistem Cicilan -->
    {{-- <div class="alert alert-info">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle-fill me-3 fs-4"></i>
            <div>
                <h6 class="alert-heading mb-2">Sistem Pembayaran Cicilan</h6>
                <p class="mb-1">✅ Bayar berapapun (minimal Rp 1.000)</p>
                <p class="mb-1">✅ Bisa bayar berkali-kali sampai lunas</p>
                <p class="mb-0">✅ Progress pembayaran akan tercatat</p>
            </div>
        </div>
    </div> --}}

    <div class="d-flex gap-2 mb-4">
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

    <!-- Card List Content -->
    <div class="card shadow-sm border-0">
        <!-- Di bagian header card - line sekitar 80 -->
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="bi bi-list-ul text-primary me-2"></i>
                @php
                    $totalCicilan = 0;
                    foreach($tagihan as $item) {
                        // ⭐⭐ SAFE CHECK: Gunakan null coalescing ⭐⭐
                        if (($item->is_cicilan ?? false) || (isset($item->is_virtual) && $item->is_virtual && ($item->is_cicilan ?? false))) {
                            $totalCicilan++;
                        }
                    }
                @endphp
                Daftar Tagihan & Cicilan
                @if($tagihan->count() > 0)
                <span class="badge bg-primary ms-2">{{ $tagihan->count() }}</span>
                @endif
                @if($totalCicilan > 0)
                <span class="badge bg-warning ms-1">{{ $totalCicilan }} Cicilan</span>
                @endif
            </h5>
            @if($tagihan->count() > 0)
            <small class="text-muted">
                Termasuk {{ $totalCicilan }} tagihan yang sedang dicicil
            </small>
            @endif
        </div>
        <div class="card-body p-0">
            @if($tagihan->count() > 0)
                <div class="p-3">
                    @foreach($tagihan as $index => $item)
                        <!-- Skip yang sudah lunas -->
                        @if($item->is_lunas)
                            @continue
                        @endif
                        <!-- Card Tagihan -->
                        <div class="card tagihan-card mb-3 border-0 shadow-sm 
                            @if($item->is_lunas) card-lunas 
                            @elseif($item->is_pending) card-pending 
                            @elseif(isset($item->is_cicilan) && $item->is_cicilan) card-cicilan 
                            @elseif(isset($item->pembayaran) && $item->pembayaran->where('status', 'rejected')->count() > 0) card-ditolak
                            @else card-belum-bayar @endif">
                            <div class="card-body">
                                <!-- Header Card -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded p-2 me-3">
                                            @if($item->jenis == 'spp')
                                                <i class="bi bi-wallet2 text-info fs-5"></i>
                                            @else
                                                <i class="bi bi-receipt text-secondary fs-5"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-1 fw-bold text-dark">{{ $item->keterangan }}</h6>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($item->jenis == 'spp')
                                                    <span class="badge bg-info">SPP</span>
                                                @else
                                                    <span class="badge bg-secondary">Tagihan</span>
                                                @endif
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    {{ $item->created_at->format('d/m/Y') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <!-- Status Badge -->
                                        @if($item->is_lunas)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Lunas
                                            </span>
                                        @elseif($item->is_pending)
                                            <span class="badge bg-warning">
                                                <i class="bi bi-clock me-1"></i>Menunggu
                                            </span>
                                        @elseif(isset($item->pembayaran) && $item->pembayaran->where('status', 'rejected')->count() > 0)
                                            <span class="badge badge-ditolak">
                                                <i class="bi bi-x-circle me-1"></i>Ditolak
                                            </span>
                                        @elseif(isset($item->is_cicilan) && $item->is_cicilan)
                                            <span class="badge badge-cicilan">
                                                <i class="bi bi-arrow-repeat me-1"></i>Sedang Dicicil
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>Belum Bayar
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Info Tagihan -->
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Total Tagihan</small>
                                        <div class="fw-bold text-primary fs-6">{{ $item->jumlah_formatted }}</div>
                                    </div>
                                    <div class="col-6">
                                        @if($item->is_cicilan)
                                            <small class="text-muted d-block">Sudah Dibayar</small>
                                            <div class="fw-bold text-success fs-6">{{ $item->total_dibayar_formatted }}</div>
                                        @else
                                            <small class="text-muted d-block">Status</small>
                                            <div class="fw-bold text-danger fs-6">Belum Bayar</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <!-- Progress Bar - sekitar line 150 -->
                                @if(($item->is_cicilan ?? false) || !($item->is_lunas ?? false))
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">Progress Pembayaran</small>
                                        <small class="fw-bold text-primary">{{ number_format($item->persentase_dibayar ?? 0, 1) }}%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" 
                                            style="width: {{ number_format($item->persentase_dibayar ?? 0, 1) }}%"
                                            role="progressbar">
                                        </div>
                                    </div>
                                    @if($item->is_cicilan ?? false)
                                    <div class="mt-2">
                                        <small class="text-warning">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Sisa tagihan: {{ $item->sisa_tagihan_formatted ?? 'Rp 0' }}
                                        </small>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <div>
                                        @php
                                            // LOGIC BISA BAYAR YANG BENAR DENGAN SAFE CHECK
                                            $bisaBayar = false;
                                            $isDitolak = false;
                                            $isCicilan = false;
                                            
                                            // Untuk virtual SPP
                                            if (isset($item->is_virtual) && $item->is_virtual) {
                                                $bisaBayar = $item->bisa_bayar ?? false;
                                                $isDitolak = $item->is_rejected ?? false;
                                                $isCicilan = $item->is_cicilan ?? false;
                                            } 
                                            // Untuk tagihan biasa
                                            else {
                                                $isDitolak = ($item->pembayaran ?? collect())->where('status', 'rejected')->count() > 0;
                                                $isCicilan = $item->is_cicilan ?? false;
                                                
                                                // ⭐⭐ BISA BAYAR KALO: CICILAN ATAU DITOLAK ATAU BELUM LUNAS & BELUM PENDING ⭐⭐
                                                $bisaBayar = $isCicilan || $isDitolak || (!($item->is_lunas ?? false) && !($item->is_pending ?? false));
                                            }
                                            
                                            // Tentukan text button
                                            if ($isDitolak) {
                                                $buttonText = 'Bayar Ulang';
                                                $buttonTitle = 'Bayar Ulang Tagihan (Ditolak)';
                                            } elseif ($isCicilan) {
                                                $buttonText = 'Cicil';
                                                $buttonTitle = 'Lanjutkan Cicilan';
                                            } else {
                                                $buttonText = 'Bayar';
                                                $buttonTitle = 'Bayar Tagihan';
                                            }
                                        @endphp

                                        @if($bisaBayar)
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#uploadModal{{ $item->id }}"
                                                    title="{{ $buttonTitle }}">
                                                <i class="bi bi-credit-card me-1"></i>
                                                {{ $buttonText }}
                                            </button>
                                        @else
                                            <span class="text-muted small">
                                                <i class="bi bi-check-circle text-success me-1"></i>
                                                Tagihan sudah lunas
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if(isset($item->pembayaran) && $item->pembayaran->count() > 0)
                                    <button type="button" 
                                            class="btn btn-outline-info btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#riwayatModal{{ $item->id }}"
                                            title="Lihat Riwayat">
                                        <i class="bi bi-clock-history me-1"></i>
                                        Riwayat
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Modal Bayar/Cicil -->
                            <div class="modal fade" id="uploadModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title">
                                                <i class="bi bi-credit-card me-2"></i>
                                                @php
                                                    $isDitolak = false;
                                                    $jumlahDitolak = 0;
                                                    
                                                    if (isset($item->pembayaran)) {
                                                        $pembayaranDitolak = $item->pembayaran->where('status', 'rejected')->first();
                                                        $isDitolak = $pembayaranDitolak ? true : false;
                                                        $jumlahDitolak = $pembayaranDitolak ? $pembayaranDitolak->jumlah : 0;
                                                    }
                                                @endphp
                                                
                                                @if($isDitolak)
                                                    Bayar Tagihan (Ditolak)
                                                @elseif(isset($item->is_cicilan) && $item->is_cicilan)
                                                    Lanjutkan Cicilan
                                                @else
                                                    Bayar Tagihan
                                                @endif
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <!-- Form Action -->
                                        @if(strpos($item->id, 'spp_') === 0)
                                            <form action="{{ route('murid.spp.upload-bukti') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="spp_id" value="{{ $item->id }}">
                                        @else
                                            <form action="{{ route('murid.tagihan.upload-bukti', $item->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                        @endif

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
                                                        
                                                        @if($isDitolak)
                                                        <!-- TAMPILAN KHUSUS DITOLAK -->
                                                        <div class="col-6">
                                                            <span class="text-muted">Status:</span>
                                                            <div class="fw-bold text-danger">Ditolak</div>
                                                        </div>
                                                        <div class="col-12 mt-2">
                                                            <span class="text-muted">Jumlah Ditolak:</span>
                                                            <div class="fw-bold text-warning">Rp {{ number_format($jumlahDitolak, 0, ',', '.') }}</div>
                                                        </div>
                                                        <div class="col-12 mt-2">
                                                            <span class="text-muted">Minimal Bayar:</span>
                                                            <div class="fw-bold text-info">Rp 1.000</div>
                                                        </div>
                                                        
                                                        @elseif(isset($item->is_cicilan) && $item->is_cicilan)
                                                        <!-- TAMPILAN CICILAN -->
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
                                                                Rp {{ number_format($item->minimal_bayar, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                        
                                                        @else
                                                        <!-- TAMPILAN BELUM BAYAR -->
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
                                                    @if(isset($item->is_cicilan) && $item->is_cicilan && !$isDitolak)
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
                                                        @if($isDitolak)
                                                            value="{{ $jumlahDitolak }}" 
                                                            min="1000" 
                                                            max="{{ $item->jumlah }}"
                                                        @else
                                                            value="{{ isset($item->is_cicilan) && $item->is_cicilan ? $item->minimal_bayar : 1000 }}" 
                                                            min="{{ isset($item->is_cicilan) && $item->is_cicilan ? $item->minimal_bayar : 1000 }}" 
                                                            max="{{ $item->sisa_tagihan }}"
                                                        @endif
                                                        required
                                                        onchange="updateSisaInfo{{ $item->id }}(this.value)">
                                                </div>
                                                <div class="form-text">
                                                    @if($isDitolak)
                                                        Minimal: Rp 1.000, Maksimal: {{ $item->jumlah_formatted }}
                                                    @elseif(isset($item->is_cicilan) && $item->is_cicilan)
                                                        Minimal: Rp {{ number_format($item->minimal_bayar, 0, ',', '.') }}, 
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
                                                    @if($isDitolak)
                                                        value="Bayar Ulang {{ $item->keterangan }}"
                                                    @else
                                                        value="{{ isset($item->is_cicilan) && $item->is_cicilan ? 'Cicilan ' . $item->keterangan : 'Bayar ' . $item->keterangan }}"
                                                    @endif
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
                            @php
                                $isDitolak = isset($item->pembayaran) && $item->pembayaran->where('status', 'rejected')->count() > 0;
                            @endphp
                            
                            @if($isDitolak)
                                const sisaSekarang = {{ $item->jumlah }};
                            @else
                                const sisaSekarang = {{ $item->sisa_tagihan }};
                            @endif
                            
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

                        // Initialize on page load
                        document.addEventListener('DOMContentLoaded', function() {
                            @php
                                $isDitolak = isset($item->pembayaran) && $item->pembayaran->where('status', 'rejected')->count() > 0;
                            @endphp
                            
                            @if($isDitolak)
                                updateSisaInfo{{ $item->id }}({{ $item->pembayaran->where('status', 'rejected')->first()->jumlah ?? 1000 }});
                            @elseif(isset($item->is_cicilan) && $item->is_cicilan)
                                updateSisaInfo{{ $item->id }}({{ $item->minimal_bayar }});
                            @else
                                updateSisaInfo{{ $item->id }}(1000);
                            @endif
                        });
                        </script>
                    @endforeach
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
                <i class="bi bi-check-circle display-1 text-success mb-3"></i>
                <h5 class="text-success">Semua tagihan sudah lunas!</h5>
                <p class="text-muted mb-0">Tidak ada tagihan yang perlu dibayar saat ini.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
/* WARNA CARD BERDASARKAN STATUS */
.tagihan-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border-left: 4px solid #007bff !important;
}

/* CARD LUNAS - Hijau */
.card-lunas {
    border-left-color: #28a745 !important;
    background: linear-gradient(135deg, #f8fff9, #ffffff);
}

/* CARD CICILAN - Kuning/Oranye */
.card-cicilan {
    border-left-color: #ffc107 !important;
    background: linear-gradient(135deg, #fffbf0, #ffffff);
}

/* CARD DITOLAK - Merah */
.card-ditolak {
    border-left-color: #dc3545 !important;
    background: linear-gradient(135deg, #fff5f5, #ffffff);
}

/* CARD PENDING - Biru Muda */
.card-pending {
    border-left-color: #17a2b8 !important;
    background: linear-gradient(135deg, #f0f9ff, #ffffff);
}

/* CARD BELUM BAYAR - Abu-abu */
.card-belum-bayar {
    border-left-color: #6c757d !important;
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
}

.tagihan-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

/* BADGE KHUSUS UNTUK STATUS DITOLAK */
.badge-ditolak {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.badge-cicilan {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: black;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .tagihan-card .card-body {
        padding: 1rem;
    }
    
    .tagihan-card .btn-group {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .tagihan-card .btn {
        width: 100%;
    }
}

.custom-alert {
    position: relative;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    display: flex;
    gap: 10px;
    align-items: center;
    animation: slideDown 0.4s ease;
    font-size: 0.95rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.custom-alert.success {
    background: linear-gradient(135deg, #d4f7df, #b6f3ce);
    border-left: 5px solid #1c9c55;
}

.custom-alert .icon {
    font-size: 1.4rem;
    color: #1c9c55;
    flex-shrink: 0;
}

.custom-alert .text {
    flex-grow: 1;
    font-weight: 500;
    color: #185c39;
}

.custom-alert .close-btn {
    background: none;
    border: none;
    font-size: 1.2rem;
    line-height: 1;
    color: #185c39;
    cursor: pointer;
    opacity: 0.7;
    transition: 0.2s;
}

.custom-alert .close-btn:hover {
    opacity: 1;
}

/* Animation */
@keyframes slideDown {
    from { transform: translateY(-10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

/* BADGE KHUSUS UNTUK STATUS DITOLAK */
.badge-ditolak {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.badge-cicilan {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: black;
}
</style>

<style>
.nav-badge {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    line-height: 1;
}

.nav-link {
    position: relative;
    padding-right: 25px !important; /* Kasih space buat badge */
}
</style>

<!-- Global Script (jika diperlukan) -->
<script>
// Script global bisa ditaruh di sini jika diperlukan
</script>
@endsection