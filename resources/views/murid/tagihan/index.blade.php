<!-- resources/views/murid/tagihan/index.blade.php -->
@extends('layouts.app')

@section('title', 'Tagihan Saya')

@section('content')
<div class="container-fluid px-0">
    <!-- Mobile Header -->
    <div class="bg-primary text-white p-3 sticky-top">
        <div class="d-flex align-items-center">
            {{-- <a href="{{ route('murid.dashboard') }}" class="text-white me-3">
                <i class="bi bi-arrow-left fs-5"></i>
            </a> --}}
            <div class="flex-grow-1">
                <h6 class="mb-0 fw-bold">Tagihan Saya</h6>
                <small class="opacity-75">
                    @if($tagihan->count() > 0)
                        {{ $tagihan->count() }} tagihan belum lunas
                    @else
                        Semua tagihan lunas
                    @endif
                </small>
            </div>
            <div class="position-relative">
                <i class="bi bi-receipt text-white fs-5"></i>
                @if($tagihan->count() > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                    {{ $tagihan->count() }}
                </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Alert Section Mobile -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    <!-- Filter Mobile -->
    <div class="p-3 border-bottom bg-white">
        <div class="d-flex gap-2 overflow-auto pb-2">
            <!-- Filter Jenis -->
            <div class="dropdown">
    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" 
            id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-filter me-1"></i>
        @if(request('jenis'))
            {{ request('jenis') == 'spp' ? 'SPP' : 'Non-SPP' }}
        @else
            Semua
        @endif
    </button>
    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
        <li>
            <a class="dropdown-item {{ !request('jenis') ? 'active' : '' }}" 
               href="{{ request()->fullUrlWithQuery(['jenis' => '']) }}">
                <i class="bi bi-grid-3x3-gap me-2"></i>
                Semua Jenis
            </a>
        </li>
        <li>
            <a class="dropdown-item {{ request('jenis') == 'spp' ? 'active' : '' }}" 
               href="{{ request()->fullUrlWithQuery(['jenis' => 'spp']) }}">
                <i class="bi bi-wallet2 me-2"></i>
                SPP
            </a>
        </li>
        <li>
            <a class="dropdown-item {{ request('jenis') == 'non-spp' ? 'active' : '' }}" 
               href="{{ request()->fullUrlWithQuery(['jenis' => 'non-spp']) }}">
                <i class="bi bi-receipt me-2"></i>
                Non-SPP
            </a>
        </li>
    </ul>
</div>

            <!-- Quick Stats -->
            @php
                $totalCicilan = 0;
                foreach($tagihan as $item) {
                    if (($item->is_cicilan ?? false) || (isset($item->is_virtual) && $item->is_virtual && ($item->is_cicilan ?? false))) {
                        $totalCicilan++;
                    }
                }
            @endphp
            
            @if($totalCicilan > 0)
            <span class="badge bg-warning d-flex align-items-center">
                <i class="bi bi-arrow-repeat me-1"></i>
                {{ $totalCicilan }} Cicilan
            </span>
            @endif
        </div>
    </div>

    <!-- Content -->
    <div class="p-3">
        @if($tagihan->count() > 0)
            <!-- Tagihan List -->
            <div class="mb-4">
                @foreach($tagihan as $index => $item)
                    <!-- Skip yang sudah lunas -->
                    @if($item->is_lunas)
                        @continue
                    @endif

                    <!-- Mobile Card Tagihan -->
                    <div class="card tagihan-card-mobile mb-3 border-0 shadow-sm 
                        @if($item->is_lunas) card-lunas 
                        @elseif($item->is_pending) card-pending 
                        @elseif(isset($item->is_cicilan) && $item->is_cicilan) card-cicilan 
                        @elseif(isset($item->pembayaran) && $item->pembayaran->where('status', 'rejected')->count() > 0) card-ditolak
                        @else card-belum-bayar @endif">
                        
                        <div class="card-body p-3">
                            <!-- Header -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="bg-light rounded p-2 me-3">
                                        @if($item->jenis == 'spp')
                                            <i class="bi bi-wallet2 text-info"></i>
                                        @else
                                            <i class="bi bi-receipt text-secondary"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1 fw-bold text-dark small">
                                            {{ Str::limit($item->keterangan, 40) }}
                                        </h6>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($item->jenis == 'spp')
                                                <span class="badge bg-info" style="font-size: 0.65rem;">SPP</span>
                                            @else
                                                <span class="badge bg-secondary" style="font-size: 0.65rem;">TAGIHAN</span>
                                            @endif
                                            <small class="text-muted" style="font-size: 0.7rem;">
                                                {{ $item->created_at->format('d/m/y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="mb-3">
                                @if($item->is_lunas)
                                    <span class="badge bg-success" style="font-size: 0.7rem;">
                                        <i class="bi bi-check-circle me-1"></i>LUNAS
                                    </span>
                                @elseif($item->is_pending)
                                    <span class="badge bg-warning" style="font-size: 0.7rem;">
                                        <i class="bi bi-clock me-1"></i>MENUNGGU
                                    </span>
                                @elseif(isset($item->pembayaran) && $item->pembayaran->where('status', 'rejected')->count() > 0)
                                    <span class="badge badge-ditolak" style="font-size: 0.7rem;">
                                        <i class="bi bi-x-circle me-1"></i>DITOLAK
                                    </span>
                                @elseif(isset($item->is_cicilan) && $item->is_cicilan)
                                    <span class="badge badge-cicilan" style="font-size: 0.7rem;">
                                        <i class="bi bi-arrow-repeat me-1"></i>CICILAN
                                    </span>
                                @else
                                    <span class="badge bg-danger" style="font-size: 0.7rem;">
                                        <i class="bi bi-x-circle me-1"></i>BELUM BAYAR
                                    </span>
                                @endif
                            </div>

                            <!-- Amount Info -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Total Tagihan</small>
                                    <div class="fw-bold text-primary" style="font-size: 0.85rem;">{{ $item->jumlah_formatted }}</div>
                                </div>
                                <div class="col-6">
                                    @if($item->is_cicilan)
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">Sudah Dibayar</small>
                                        <div class="fw-bold text-success" style="font-size: 0.85rem;">{{ $item->total_dibayar_formatted }}</div>
                                    @else
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">Status</small>
                                        <div class="fw-bold text-danger" style="font-size: 0.85rem;">BELUM BAYAR</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Progress Bar Mobile -->
                            @if(($item->is_cicilan ?? false) || !($item->is_lunas ?? false))
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted" style="font-size: 0.7rem;">Progress</small>
                                    <small class="fw-bold text-primary" style="font-size: 0.7rem;">{{ number_format($item->persentase_dibayar ?? 0, 1) }}%</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" 
                                        style="width: {{ number_format($item->persentase_dibayar ?? 0, 1) }}%"
                                        role="progressbar">
                                    </div>
                                </div>
                                @if($item->is_cicilan ?? false)
                                <div class="mt-1">
                                    <small class="text-warning" style="font-size: 0.65rem;">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Sisa: {{ $item->sisa_tagihan_formatted ?? 'Rp 0' }}
                                    </small>
                                </div>
                                @endif
                            </div>
                            @endif

                            <!-- Action Buttons Mobile -->
                            <div class="d-flex gap-2 pt-2 border-top">
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
                                        $buttonIcon = 'bi-arrow-clockwise';
                                    } elseif ($isCicilan) {
                                        $buttonText = 'Cicil';
                                        $buttonIcon = 'bi-arrow-repeat';
                                    } else {
                                        $buttonText = 'Bayar';
                                        $buttonIcon = 'bi-credit-card';
                                    }
                                @endphp

                                @if($bisaBayar)
                                    <button type="button" 
                                            class="btn btn-primary btn-sm flex-fill" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#uploadModal{{ $item->id }}">
                                        <i class="{{ $buttonIcon }} me-1"></i>
                                        {{ $buttonText }}
                                    </button>
                                @else
                                    <span class="text-muted small flex-fill text-center">
                                        <i class="bi bi-check-circle text-success me-1"></i>
                                        Lunas
                                    </span>
                                @endif
                                
                                @if(isset($item->pembayaran) && $item->pembayaran->count() > 0)
                                <button type="button" 
                                        class="btn btn-outline-info btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#riwayatModal{{ $item->id }}"
                                        title="Lihat Riwayat">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Modal Bayar/Cicil Mobile -->
                    <div class="modal fade" id="uploadModal{{ $item->id }}" tabindex="-1" aria-labelledby="uploadModalLabel{{ $item->id }}">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-light py-3">
                                    <h6 class="modal-title fw-bold">
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
                                            Bayar Ulang
                                        @elseif(isset($item->is_cicilan) && $item->is_cicilan)
                                            Lanjutkan Cicilan
                                        @else
                                            Bayar Tagihan
                                        @endif
                                    </h6>
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

                                <div class="modal-body p-3">
                                    <!-- Info Tagihan Mobile -->
                                    <div class="card border-primary mb-3">
                                        <div class="card-body p-3">
                                            <h6 class="card-title small fw-bold">{{ $item->keterangan }}</h6>
                                            <div class="row small g-2">
                                                <div class="col-6">
                                                    <span class="text-muted">Total:</span>
                                                    <div class="fw-bold text-primary">{{ $item->jumlah_formatted }}</div>
                                                </div>
                                                
                                                @if($isDitolak)
                                                <!-- TAMPILAN KHUSUS DITOLAK -->
                                                <div class="col-6">
                                                    <span class="text-muted">Status:</span>
                                                    <div class="fw-bold text-danger">Ditolak</div>
                                                </div>
                                                <div class="col-12">
                                                    <span class="text-muted">Ditolak:</span>
                                                    <div class="fw-bold text-warning">Rp {{ number_format($jumlahDitolak, 0, ',', '.') }}</div>
                                                </div>
                                                <div class="col-12">
                                                    <span class="text-muted">Minimal:</span>
                                                    <div class="fw-bold text-info">Rp 1.000</div>
                                                </div>
                                                
                                                @elseif(isset($item->is_cicilan) && $item->is_cicilan)
                                                <!-- TAMPILAN CICILAN -->
                                                <div class="col-6">
                                                    <span class="text-muted">Dibayar:</span>
                                                    <div class="fw-bold text-success">{{ $item->total_dibayar_formatted }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-muted">Sisa:</span>
                                                    <div class="fw-bold text-warning">{{ $item->sisa_tagihan_formatted }}</div>
                                                </div>
                                                <div class="col-12">
                                                    <span class="text-muted">Minimal:</span>
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
                                                <div class="col-12">
                                                    <span class="text-muted">Minimal:</span>
                                                    <div class="fw-bold text-info">Rp 1.000</div>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Progress Bar -->
                                            @if(isset($item->is_cicilan) && $item->is_cicilan && !$isDitolak)
                                            <div class="mt-2">
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $item->persentase_dibayar }}%"></div>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.7rem;">{{ number_format($item->persentase_dibayar, 1) }}% terbayar</small>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Input Jumlah -->
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Jumlah Bayar *</label>
                                        <div class="input-group input-group-sm">
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
                                        <div class="form-text small">
                                            @if($isDitolak)
                                                Min: Rp 1.000, Max: {{ $item->jumlah_formatted }}
                                            @elseif(isset($item->is_cicilan) && $item->is_cicilan)
                                                Min: Rp {{ number_format($item->minimal_bayar, 0, ',', '.') }}, 
                                                Max: {{ $item->sisa_tagihan_formatted }}
                                            @else
                                                Min: Rp 1.000, Max: {{ $item->jumlah_formatted }}
                                            @endif
                                        </div>
                                        
                                        <!-- Info Sisa Setelah Bayar -->
                                        <div id="sisaInfo{{ $item->id }}" class="mt-2" style="display: none;">
                                            <small class="text-info" style="font-size: 0.7rem;">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Sisa: <span id="sisaAmount{{ $item->id }}" class="fw-bold"></span>
                                            </small>
                                        </div>
                                        
                                        <!-- Info Akan Lunas -->
                                        <div id="lunasInfo{{ $item->id }}" class="mt-2 alert alert-success py-1 small" style="display: none;">
                                            <small><i class="bi bi-check-circle me-1"></i>Pembayaran akan melunasi!</small>
                                        </div>
                                    </div>

                                    <!-- Metode Pembayaran -->
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Metode Pembayaran *</label>
                                        <select class="form-select form-select-sm" name="metode" required>
                                            <option value="">Pilih Metode</option>
                                            <option value="Transfer">Transfer Bank</option>
                                            <option value="Tunai">Tunai</option>
                                            <option value="QRIS">QRIS</option>
                                            <option value="E-Wallet">E-Wallet</option>
                                        </select>
                                    </div>

                                    <!-- Bukti Pembayaran -->
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Bukti Pembayaran *</label>
                                        <input type="file" class="form-control form-control-sm" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required>
                                        <div class="form-text small">Format: JPG, PNG, PDF (Maks. 2MB)</div>
                                    </div>

                                    <!-- Keterangan -->
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Keterangan *</label>
                                        <input type="text" class="form-control form-control-sm" name="keterangan" 
                                            @if($isDitolak)
                                                value="Bayar Ulang {{ $item->keterangan }}"
                                            @else
                                                value="{{ isset($item->is_cicilan) && $item->is_cicilan ? 'Cicilan ' . $item->keterangan : 'Bayar ' . $item->keterangan }}"
                                            @endif
                                            required>
                                    </div>
                                </div>
                                <div class="modal-footer p-3">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-upload me-1"></i>
                                        Upload Bukti
                                    </button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Riwayat Mobile -->
                    <div class="modal fade" id="riwayatModal{{ $item->id }}" tabindex="-1" aria-labelledby="riwayatModalLabel{{ $item->id }}">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-light py-3">
                                    <h6 class="modal-title fw-bold">
                                        <i class="bi bi-clock-history me-2"></i>
                                        Riwayat Pembayaran
                                    </h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-3">
                                    <h6 class="small fw-bold">{{ $item->keterangan }}</h6>
                                    <p class="text-muted small">Total: {{ $item->jumlah_formatted }}</p>
                                    
                                    @if($item->pembayaran->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm small">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Jumlah</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($item->pembayaran->sortByDesc('created_at') as $pembayaran)
                                                <tr>
                                                    <td style="font-size: 0.7rem;">{{ $pembayaran->tanggal_upload->format('d/m/y H:i') }}</td>
                                                    <td class="fw-bold text-success" style="font-size: 0.7rem;">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                                                    <td>
                                                        @if($pembayaran->status == 'accepted')
                                                            <span class="badge bg-success" style="font-size: 0.6rem;">Diterima</span>
                                                        @elseif($pembayaran->status == 'pending')
                                                            <span class="badge bg-warning" style="font-size: 0.6rem;">Menunggu</span>
                                                        @else
                                                            <span class="badge bg-danger" style="font-size: 0.6rem;">Ditolak</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="text-center py-3">
                                        <i class="bi bi-receipt text-muted fs-1"></i>
                                        <p class="text-muted mt-2 small">Belum ada riwayat pembayaran</p>
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

            <!-- Pagination Mobile -->
            @if($tagihan->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <div class="d-flex gap-2">
                    {{ $tagihan->links('pagination::simple-bootstrap-4') }}
                </div>
            </div>
            @endif

        @else
        <!-- Empty State Mobile -->
        <div class="text-center py-5">
            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                <i class="bi bi-check-circle text-success fs-1"></i>
            </div>
            <h6 class="text-success mb-2">Semua tagihan sudah lunas!</h6>
            <p class="text-muted small mb-0">Tidak ada tagihan yang perlu dibayar saat ini.</p>
        </div>
        @endif
    </div>
</div>

<style>
/* Mobile Optimizations */
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 1020;
}

/* Mobile Card Styles */
.tagihan-card-mobile {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border-left: 4px solid #007bff !important;
    border-radius: 12px;
}

.tagihan-card-mobile:active {
    transform: scale(0.98);
}

/* WARNA CARD BERDASARKAN STATUS */
.card-lunas {
    border-left-color: #28a745 !important;
    background: linear-gradient(135deg, #f8fff9, #ffffff);
}

.card-cicilan {
    border-left-color: #ffc107 !important;
    background: linear-gradient(135deg, #fffbf0, #ffffff);
}

.card-ditolak {
    border-left-color: #dc3545 !important;
    background: linear-gradient(135deg, #fff5f5, #ffffff);
}

.card-pending {
    border-left-color: #17a2b8 !important;
    background: linear-gradient(135deg, #f0f9ff, #ffffff);
}

.card-belum-bayar {
    border-left-color: #6c757d !important;
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
}

/* BADGE STYLES */
.badge-ditolak {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.badge-cicilan {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: black;
}

/* Progress Bar */
.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

/* Modal Mobile Optimization */
.modal-dialog {
    margin: 1rem;
}

.modal-content {
    border-radius: 16px;
    border: none;
}

/* Button Styles */
.btn {
    border-radius: 8px;
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
}

/* Form Controls */
.form-control, .form-select {
    border-radius: 8px;
    font-size: 0.85rem;
}

.form-control-sm, .form-select-sm {
    font-size: 0.8rem;
}

/* Table Mobile */
.table-sm th,
.table-sm td {
    padding: 0.5rem 0.25rem;
    font-size: 0.75rem;
}

/* Scrollbar for mobile filter */
.d-flex.overflow-auto {
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}

.d-flex.overflow-auto::-webkit-scrollbar {
    display: none; /* Chrome, Safari and Opera */
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 0;
        padding-right: 0;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .modal-dialog {
        margin: 0.5rem;
    }
}

/* Animation for modal */
@keyframes slideUp {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal.fade .modal-dialog {
    animation: slideUp 0.3s ease-out;
}
</style>

<style>
/* Dropdown Fix */
.dropdown-menu {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    padding: 0.5rem;
}

.dropdown-item {
    border-radius: 6px;
    margin: 0.1rem 0;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
}

.dropdown-item:hover {
    background-color: #e9ecef;
    color: #495057;
}

.dropdown-item.active {
    background-color: #007bff;
    color: white;
}

.dropdown-item.active:hover {
    background-color: #0056b3;
    color: white;
}

/* Pastikan dropdown show bekerja */
.dropdown-menu.show {
    display: block;
}

/* Mobile optimization */
@media (max-width: 576px) {
    .dropdown-menu {
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        width: 80% !important;
        max-width: 250px !important;
    }
}
</style>

<script>
// Dropdown fallback solution
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing dropdown...');
    
    // Cek jika Bootstrap tersedia
    if (typeof bootstrap !== 'undefined') {
        console.log('Bootstrap JS loaded');
        
        // Inisialisasi dropdown Bootstrap
        const dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
        const dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });
        
        console.log('Bootstrap dropdowns initialized:', dropdownList.length);
    } else {
        console.log('Bootstrap not found, using manual dropdown');
        initManualDropdown();
    }
    
    // Manual dropdown fallback
    function initManualDropdown() {
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const dropdownMenu = this.nextElementSibling;
                const isShowing = dropdownMenu.classList.contains('show');
                
                // Close all other dropdowns
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    if (menu !== dropdownMenu) {
                        menu.classList.remove('show');
                    }
                });
                
                // Toggle current dropdown
                dropdownMenu.classList.toggle('show', !isShowing);
                this.setAttribute('aria-expanded', !isShowing);
            });
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
                document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                    toggle.setAttribute('aria-expanded', 'false');
                });
            }
        });
    }
    
    // Touch support for mobile
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('touchstart', function(e) {
            e.preventDefault();
            this.click();
        });
    });
});
</script>

<script>
// Mobile-specific enhancements
document.addEventListener('DOMContentLoaded', function() {
    // Add touch feedback to cards
    const cards = document.querySelectorAll('.tagihan-card-mobile');
    cards.forEach(card => {
        card.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        card.addEventListener('touchend', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Handle modal backdrop tap to close
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                const modalInstance = bootstrap.Modal.getInstance(this);
                modalInstance.hide();
            }
        });
    });

    // Improve form input experience on mobile
    const formInputs = document.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.fontSize = '16px'; // Prevent zoom on iOS
        });
    });
});
</script>
@endsection