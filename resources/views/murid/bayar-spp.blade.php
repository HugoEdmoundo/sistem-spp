<!-- resources/views/murid/bayar-spp.blade.php -->
@extends('layouts.app')

@section('title', 'Buat Tagihan SPP')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="bg-primary rounded p-2 me-3">
                <i class="bi bi-credit-card text-white fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">Buat Tagihan SPP</h4>
                <p class="text-muted mb-0">Buat tagihan SPP baru dan bayar secara cicilan</p>
            </div>
        </div>
        <a href="{{ route('murid.tagihan.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Tagihan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="bi bi-plus-circle text-primary me-2"></i>
                        Form Tagihan SPP Baru
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('murid.bayar.spp') }}" enctype="multipart/form-data" id="formSpp">
                        @csrf
                        
                        <!-- Periode SPP -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Dari Bulan *</label>
                                <select name="bulan_mulai" class="form-control @error('bulan_mulai') is-invalid @enderror" id="bulanMulai" required>
                                    <option value="">Pilih Bulan Mulai</option>
                                    @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('bulan_mulai') == $i ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                    @endfor
                                </select>
                                @error('bulan_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Sampai Bulan *</label>
                                <select name="bulan_akhir" class="form-control @error('bulan_akhir') is-invalid @enderror" id="bulanAkhir" required>
                                    <option value="">Pilih Bulan Akhir</option>
                                    @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('bulan_akhir') == $i ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                    @endfor
                                </select>
                                @error('bulan_akhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Tahun *</label>
                                <select name="tahun" class="form-control @error('tahun') is-invalid @enderror" id="tahunSelect" required>
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
                        </div>

                        <!-- Info Tagihan Otomatis -->
                        <div class="card border-info mb-4" id="infoTagihan" style="display: none;">
                            <div class="card-header bg-info text-white py-2">
                                <h6 class="mb-0">Informasi Tagihan SPP</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted">Jumlah Bulan</small>
                                        <div class="fw-bold" id="infoJumlahBulan">-</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Nominal per Bulan</small>
                                        <div class="fw-bold">Rp {{ number_format($nominalSpp, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Total Tagihan</small>
                                        <div class="fw-bold text-primary" id="infoTotalTagihan">-</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">Keterangan:</small>
                                    <div class="fw-semibold" id="infoKeterangan">-</div>
                                </div>
                            </div>
                        </div>

                        <!-- Pembayaran Pertama -->
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="mb-0">Pembayaran Pertama</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Bayar *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" 
                                               class="form-control" 
                                               name="jumlah" 
                                               id="jumlahBayar"
                                               value="{{ old('jumlah') }}" 
                                               min="1000"
                                               required>
                                    </div>
                                    <div class="form-text">
                                        Bayar berapapun (minimal Rp 1.000). Bisa dicicil sampai lunas.
                                    </div>
                                    
                                    <!-- Info Sisa -->
                                    <div id="infoSisa" class="mt-2 alert alert-info py-2" style="display: none;">
                                        <small>
                                            <i class="bi bi-info-circle me-1"></i>
                                            Sisa setelah bayar: <span id="sisaAmount" class="fw-bold"></span>
                                        </small>
                                    </div>
                                    
                                    <!-- Info Akan Lunas -->
                                    <div id="infoLunas" class="mt-2 alert alert-success py-2" style="display: none;">
                                        <small><i class="bi bi-check-circle me-1"></i>Pembayaran ini akan melunasi tagihan!</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Metode Pembayaran *</label>
                                    <select name="metode" class="form-control @error('metode') is-invalid @enderror" required>
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

                                <div class="mb-3">
                                    <label class="form-label">Bukti Pembayaran *</label>
                                    <input type="file" name="bukti" class="form-control @error('bukti') is-invalid @enderror" 
                                           accept=".jpg,.jpeg,.png,.pdf" required>
                                    <div class="form-text">Format: JPG, PNG, PDF (max 2MB)</div>
                                    @error('bukti')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Keterangan *</label>
                                    <input type="text" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                           value="{{ old('keterangan', 'Pembayaran SPP') }}" required>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong> Tagihan SPP akan dibuat dan bisa dibayar secara cicilan seperti tagihan biasa.
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-save me-2"></i>Buat Tagihan & Upload Bukti
                            </button>
                            <a href="{{ route('murid.tagihan.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Info SPP -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informasi SPP
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Nominal per bulan:</span>
                        <strong class="text-primary">Rp {{ number_format($nominalSpp, 0, ',', '.') }}</strong>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">Cara Kerja Sistem:</h6>
                    <ol class="small ps-3 mb-0">
                        <li>Pilih periode SPP yang akan dibayar</li>
                        <li>System buat tagihan SPP otomatis</li>
                        <li>Bayar berapapun (minimal Rp 1.000)</li>
                        <li>Bisa cicil berkali-kali sampai lunas</li>
                        <li>Progress pembayaran bisa dilihat di menu Tagihan</li>
                    </ol>
                </div>
            </div>

            <!-- Status Tagihan -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Status Tagihan</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-danger">BELUM LUNAS</span>
                        <small>Menunggu pembayaran</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-info">CICILAN</span>
                        <small>Sedang dicicil</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-success">LUNAS</span>
                        <small>Sudah lunas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
            infoJumlahBulan.textContent = jumlahBulan + ' bulan';
            infoTotalTagihan.textContent = 'Rp ' + totalTagihan.toLocaleString('id-ID');
            
            // Buat keterangan
            const bulanMulaiNama = new Date(2000, mulai-1).toLocaleString('id-ID', { month: 'long' });
            const bulanAkhirNama = new Date(2000, akhir-1).toLocaleString('id-ID', { month: 'long' });
            
            if (jumlahBulan === 1) {
                infoKeterangan.textContent = 'SPP Bulan ' + bulanMulaiNama + ' ' + tahun;
            } else {
                infoKeterangan.textContent = 'SPP ' + jumlahBulan + ' bulan (' + bulanMulaiNama + ' - ' + bulanAkhirNama + ' ' + tahun + ')';
            }
            
            // Tampilkan info
            infoTagihan.style.display = 'block';
            
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
            infoSisa.style.display = 'block';
            
            // Update info lunas
            if (sisa <= 0) {
                infoLunas.style.display = 'block';
            } else {
                infoLunas.style.display = 'none';
            }
        } else {
            infoSisa.style.display = 'none';
            infoLunas.style.display = 'none';
        }
    }

    // Event listeners
    bulanMulai.addEventListener('change', updateInfoTagihan);
    bulanAkhir.addEventListener('change', updateInfoTagihan);
    tahunSelect.addEventListener('change', updateInfoTagihan);
    jumlahBayar.addEventListener('input', updateInfoPembayaran);

    // Validasi sebelum submit
    document.getElementById('formSpp').addEventListener('submit', function(e) {
        const mulai = parseInt(bulanMulai.value);
        const akhir = parseInt(bulanAkhir.value);
        
        if (mulai > akhir) {
            e.preventDefault();
            alert('Bulan akhir harus lebih besar atau sama dengan bulan mulai!');
            return;
        }
        
        const jumlah = parseInt(jumlahBayar.value) || 0;
        if (jumlah < 1000) {
            e.preventDefault();
            alert('Jumlah bayar minimal Rp 1.000!');
            return;
        }
    });

    // Initialize
    updateInfoTagihan();
});
</script>
@endsection