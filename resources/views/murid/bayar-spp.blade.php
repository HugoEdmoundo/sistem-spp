<!-- resources/views/murid/bayar-spp.blade.php -->
@extends('layouts.app')

@section('title', 'Pembayaran SPP')

@section('content')
<div class="container-fluid px-0">
    <!-- Mobile Header -->
    <div class="bg-primary text-white p-3 sticky-top">
        <div class="d-flex align-items-center">
            {{-- <a href="{{ route('murid.tagihan.index') }}" class="text-white me-3">
                <i class="bi bi-arrow-left fs-5"></i>
            </a> --}}
            <div class="flex-grow-1">
                <h6 class="mb-0 fw-bold">Bayar SPP</h6>
                <small class="opacity-75">Pembayaran SPP baru & cicilan</small>
            </div>
            <div class="bg-white bg-opacity-20 rounded p-1">
                <i class="bi bi-credit-card text-white"></i>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div class="flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Form Section -->
    <div class="p-3">
        <form method="POST" action="{{ route('murid.bayar.spp') }}" enctype="multipart/form-data" id="formSpp">
            @csrf
            
            <!-- Card: Periode SPP -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-calendar-range text-primary me-2"></i>
                        Periode SPP
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Tahun -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tahun *</label>
                        <select name="tahun" class="form-select @error('tahun') is-invalid @enderror" id="tahunSelect" required>
                            <option value="">Pilih Tahun</option>
                            @for($i = 2024; $i <= 2030; $i++)
                            <option value="{{ $i }}" {{ old('tahun') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                            @endfor
                        </select>
                        @error('tahun')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Bulan Mulai & Akhir -->
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Dari Bulan *</label>
                            <select name="bulan_mulai" class="form-select @error('bulan_mulai') is-invalid @enderror" id="bulanMulai" required>
                                <option value="">Pilih</option>
                                @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('bulan_mulai') == $i ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('M') }}
                                </option>
                                @endfor
                            </select>
                            @error('bulan_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-6">
                            <label class="form-label fw-semibold">Sampai Bulan *</label>
                            <select name="bulan_akhir" class="form-select @error('bulan_akhir') is-invalid @enderror" id="bulanAkhir" required>
                                <option value="">Pilih</option>
                                @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('bulan_akhir') == $i ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('M') }}
                                </option>
                                @endfor
                            </select>
                            @error('bulan_akhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Info Tagihan (Auto Show) -->
            <div class="card border-info mb-3" id="infoTagihan" style="display: none;">
                <div class="card-header bg-info text-white py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="fw-semibold">Informasi Tagihan</small>
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <small class="text-muted d-block">Jumlah Bulan</small>
                            <div class="fw-bold text-dark" id="infoJumlahBulan">-</div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Per Bulan</small>
                            <div class="fw-bold">Rp {{ number_format($nominalSpp, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Total</small>
                            <div class="fw-bold text-primary" id="infoTotalTagihan">-</div>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top">
                        <small class="text-muted d-block">Keterangan:</small>
                        <div class="fw-semibold small" id="infoKeterangan">-</div>
                    </div>
                </div>
            </div>

            <!-- Card: Pembayaran -->
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="fw-semibold">Pembayaran</small>
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Jumlah Bayar -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah Bayar *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" 
                                   class="form-control" 
                                   name="jumlah" 
                                   id="jumlahBayar"
                                   value="{{ old('jumlah') }}" 
                                   min="1000"
                                   placeholder="1000"
                                   required>
                        </div>
                        <div class="form-text small">
                            Bayar berapapun (minimal Rp 1.000)
                        </div>
                        
                        <!-- Info Sisa -->
                        <div id="infoSisa" class="mt-2 alert alert-info py-2 small" style="display: none;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle me-2"></i>
                                <span>Sisa: <span id="sisaAmount" class="fw-bold"></span></span>
                            </div>
                        </div>
                        
                        <!-- Info Akan Lunas -->
                        <div id="infoLunas" class="mt-2 alert alert-success py-2 small" style="display: none;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle me-2"></i>
                                <span>Pembayaran ini akan melunasi!</span>
                            </div>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Pembayaran *</label>
                        <select name="metode" class="form-select @error('metode') is-invalid @enderror" required>
                            <option value="">Pilih Metode</option>
                            <option value="Transfer" {{ old('metode') == 'Transfer' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="Tunai" {{ old('metode') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="QRIS" {{ old('metode') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                            <option value="E-Wallet" {{ old('metode') == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                        </select>
                        @error('metode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Bukti Pembayaran -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bukti Pembayaran *</label>
                        <input type="file" name="bukti" class="form-control @error('bukti') is-invalid @enderror" 
                               accept=".jpg,.jpeg,.png,.pdf" required>
                        <div class="form-text small">
                            Format: JPG, PNG, PDF (max 2MB)
                        </div>
                        @error('bukti')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan *</label>
                        <input type="text" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                               value="{{ old('keterangan', 'Pembayaran SPP') }}" required>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Info Cards Stack -->
            <div class="row g-2 mb-4">
                <!-- Info SPP -->
                <div class="col-12">
                    <div class="card border-warning">
                        <div class="card-header bg-warning bg-opacity-10 py-2">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle text-warning me-2"></i>
                                <small class="fw-semibold">Info SPP</small>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Nominal per bulan:</small>
                                <strong class="text-primary">Rp {{ number_format($nominalSpp, 0, ',', '.') }}</strong>
                            </div>
                            <hr class="my-2">
                            <div class="small">
                                <strong>Cara Kerja:</strong>
                                <ol class="mb-0 mt-1 ps-3">
                                    <li>Pilih periode SPP</li>
                                    <li>Bayar berapapun (min Rp 1.000)</li>
                                    <li>Bisa cicil sampai lunas</li>
                                    <li>Progress bisa dilihat di menu Tagihan</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Info -->
                <div class="col-12">
                    <div class="card border-secondary">
                        <div class="card-header bg-secondary bg-opacity-10 py-2">
                            <small class="fw-semibold">Status Tagihan</small>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2 text-center">
                                <div class="col-4">
                                    <span class="badge bg-danger w-100">BELUM</span>
                                    <small class="text-muted d-block mt-1">Menunggu</small>
                                </div>
                                <div class="col-4">
                                    <span class="badge bg-info w-100">CICILAN</span>
                                    <small class="text-muted d-block mt-1">Dicicil</small>
                                </div>
                                <div class="col-4">
                                    <span class="badge bg-success w-100">LUNAS</span>
                                    <small class="text-muted d-block mt-1">Selesai</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warning Alert -->
            <div class="alert alert-warning small">
                <div class="d-flex align-items-start">
                    <i class="bi bi-exclamation-triangle me-2 mt-1"></i>
                    <div>
                        <strong>Perhatian:</strong> Tagihan SPP akan dibuat dan bisa dibayar secara cicilan seperti tagihan biasa.
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="sticky-bottom bg-white border-top p-3">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg py-3 fw-semibold" id="submitBtn">
                        <i class="bi bi-send-check me-2"></i>
                        Bayar SPP & Upload Bukti
                    </button>
                    <a href="{{ route('murid.tagihan.index') }}" class="btn btn-outline-secondary py-3">
                        <i class="bi bi-x-circle me-2"></i>
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 1020;
}

.sticky-bottom {
    position: sticky;
    bottom: 0;
    z-index: 1020;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
}

.card {
    border-radius: 12px;
}

.form-select, .form-control {
    border-radius: 8px;
}

.btn {
    border-radius: 10px;
}

.alert {
    border-radius: 10px;
    border: none;
}

.input-group-text {
    border-radius: 8px 0 0 8px;
}

/* Mobile optimizations */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 0;
        padding-right: 0;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .btn-lg {
        padding: 12px 16px;
        font-size: 1rem;
    }
}

/* Smooth animations */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:active {
    transform: scale(0.98);
}

/* Custom scroll for mobile */
.form-select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bulanMulai = document.getElementById('bulanMulai');
    const bulanAkhir = document.getElementById('bulanAkhir');
    const tahunSelect = document.getElementById('tahunSelect');
    const infoTagihan = document.getElementById('infoTagihan');
    const infoJumlahBulan = document.getElementById('infoJumlahBulan');
    const infoTotalTagihan = document.getElementById('infoTotalTagihan');
    const infoKeterangan = document.getElementById('infoKeterangan');
    const jumlahBayar = document.getElementById('jumlahBayar');
    const infoSisa = document.getElementById('infoSisa');
    const infoLunas = document.getElementById('infoLunas');
    const sisaAmount = document.getElementById('sisaAmount');
    const nominalSpp = {{ $nominalSpp }};

    let totalTagihan = 0;

    function updateInfoTagihan() {
        const mulai = parseInt(bulanMulai.value);
        const akhir = parseInt(bulanAkhir.value);
        const tahun = parseInt(tahunSelect.value);
        
        if (mulai && akhir && tahun && mulai <= akhir) {
            const jumlahBulan = (akhir - mulai) + 1;
            totalTagihan = jumlahBulan * nominalSpp;
            
            // Update info
            infoJumlahBulan.textContent = jumlahBulan;
            infoTotalTagihan.textContent = 'Rp ' + totalTagihan.toLocaleString('id-ID');
            
            // Buat keterangan mobile-friendly
            const bulanMulaiNama = new Date(2000, mulai-1).toLocaleString('id-ID', { month: 'short' });
            const bulanAkhirNama = new Date(2000, akhir-1).toLocaleString('id-ID', { month: 'short' });
            
            if (jumlahBulan === 1) {
                infoKeterangan.textContent = 'SPP ' + bulanMulaiNama + ' ' + tahun;
            } else {
                infoKeterangan.textContent = 'SPP ' + bulanMulaiNama + '-' + bulanAkhirNama + ' ' + tahun;
            }
            
            // Tampilkan info dengan animasi
            infoTagihan.style.display = 'block';
            infoTagihan.style.animation = 'fadeIn 0.3s ease-in';
            
            // Update max jumlah bayar
            jumlahBayar.max = totalTagihan;
            
            // Update info pembayaran
            updateInfoPembayaran();
            
        } else {
            infoTagihan.style.display = 'none';
            infoSisa.style.display = 'none';
            infoLunas.style.display = 'none';
        }
    }

    function updateInfoPembayaran() {
        const jumlah = parseInt(jumlahBayar.value) || 0;
        const sisa = totalTagihan - jumlah;
        
        if (jumlah > 0 && totalTagihan > 0) {
            // Update info sisa
            sisaAmount.textContent = 'Rp ' + sisa.toLocaleString('id-ID');
            infoSisa.style.display = 'flex';
            
            // Update info lunas
            if (sisa <= 0) {
                infoLunas.style.display = 'flex';
                infoSisa.style.display = 'none';
            } else {
                infoLunas.style.display = 'none';
            }
            
            // Validasi real-time
            if (jumlah < 1000) {
                jumlahBayar.classList.add('is-invalid');
            } else {
                jumlahBayar.classList.remove('is-invalid');
            }
        } else {
            infoSisa.style.display = 'none';
            infoLunas.style.display = 'none';
        }
    }

    // Event listeners dengan debounce untuk performa mobile
    let timeout;
    function debouncedUpdate() {
        clearTimeout(timeout);
        timeout = setTimeout(updateInfoTagihan, 300);
    }

    bulanMulai.addEventListener('change', debouncedUpdate);
    bulanAkhir.addEventListener('change', debouncedUpdate);
    tahunSelect.addEventListener('change', debouncedUpdate);
    
    // Real-time update untuk input jumlah
    jumlahBayar.addEventListener('input', function() {
        updateInfoPembayaran();
    });

    // Validasi sebelum submit
    document.getElementById('formSpp').addEventListener('submit', function(e) {
        const mulai = parseInt(bulanMulai.value);
        const akhir = parseInt(bulanAkhir.value);
        
        if (mulai > akhir) {
            e.preventDefault();
            showMobileAlert('Bulan akhir harus lebih besar atau sama dengan bulan mulai!', 'danger');
            return;
        }
        
        const jumlah = parseInt(jumlahBayar.value) || 0;
        if (jumlah < 1000) {
            e.preventDefault();
            showMobileAlert('Jumlah bayar minimal Rp 1.000!', 'danger');
            return;
        }
        
        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memproses...';
        submitBtn.disabled = true;
    });

    // Mobile alert function
    function showMobileAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-0 end-0 m-3`;
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-${type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                <div class="flex-grow-1">${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.body.appendChild(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    }

    // Initialize
    updateInfoTagihan();
    
    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);
});
</script>
@endsection