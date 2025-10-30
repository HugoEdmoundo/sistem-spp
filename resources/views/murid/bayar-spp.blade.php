@extends('layouts.app')

@section('title', 'Bayar SPP')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Bayar SPP</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('murid.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Bayar SPP</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- resources/views/murid/bayar-spp.blade.php --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>❌ Terjadi Kesalahan:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Gagal:</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>✅ Berhasil:</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>Informasi:</strong> {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>⚠️ Peringatan:</strong> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Form Pembayaran SPP</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Terdapat kesalahan dalam pengisian form. Silakan periksa kembali.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('murid.bayar.spp') }}" enctype="multipart/form-data" id="formBayarSpp">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
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
                        
                        <div class="col-md-4 mb-3">
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
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tahun *</label>
                            <select name="tahun" class="form-control @error('tahun') is-invalid @enderror" id="tahunSelect" required>
                                <option value="">Pilih Tahun</option>
                                @for($i = date('Y'); $i <= date('Y') + 1; $i++)
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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah Bulan</label>
                            <input type="text" id="jumlahBulan" class="form-control" readonly>
                            <small class="text-muted">Otomatis terhitung dari pilihan bulan</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total yang Harus Dibayar</label>
                            <input type="text" id="totalHarusBayar" class="form-control" readonly>
                            <small class="text-muted">Otomatis terhitung berdasarkan nominal SPP</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah Bayar *</label>
                            <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" 
                                id="jumlahBayar" min="0" value="{{ old('jumlah') }}" required>
                            <small class="text-muted">Masukkan jumlah yang dibayar</small>
                            <div id="errorJumlah" class="text-danger small mt-1" style="display: none;">
                                Jumlah bayar kurang dari total yang harus dibayar!
                            </div>
                            @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Metode Pembayaran *</label>
                            <select name="metode" class="form-control @error('metode') is-invalid @enderror" required>
                                <option value="">Pilih Metode</option>
                                <option value="Transfer Bank" {{ old('metode') == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="Tunai" {{ old('metode') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                <option value="E-Wallet" {{ old('metode') == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                            </select>
                            @error('metode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bukti Pembayaran *</label>
                            <input type="file" name="bukti" class="form-control @error('bukti') is-invalid @enderror" 
                                accept=".jpg,.jpeg,.png,.pdf" required>
                            <small class="text-muted">Format: JPG, PNG, PDF (max 2MB)</small>
                            @error('bukti')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Keterangan (Otomatis)</label>
                            <input type="text" name="keterangan" class="form-control" id="keteranganOtomatis" readonly value="{{ old('keterangan') }}">
                        </div>
                    </div>

                    <!-- Alert untuk jumlah bayar kurang -->
                    <div class="alert alert-warning" id="alertKurangBayar" style="display: none;">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Peringatan:</strong> Jumlah bayar kurang dari total yang harus dibayar. Pastikan Anda membayar sesuai nominal.
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Informasi:</strong> Pembayaran akan diverifikasi oleh admin dalam 1x24 jam. Pastikan bukti pembayaran jelas dan valid.
                    </div>

                    <div class="d-flex flex-column flex-md-row gap-3 mt-3">
                        <button type="submit" class="btn btn-primary w-100 w-md-auto" id="submitBtn">
                            <i class="bi bi-upload me-2"></i>Upload Bukti Bayar
                        </button>
                        <a href="{{ route('murid.dashboard') }}" class="btn btn-secondary w-100 w-md-auto">
                            <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi SPP</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Nominal SPP per bulan:</span>
                    <strong class="text-primary">Rp {{ number_format($nominalSpp, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Status Akun:</span>
                    <span class="badge bg-success">Aktif</span>
                </div>
                <hr>
                <h6 class="mb-3">Cara Pembayaran:</h6>
                <ol class="small ps-3">
                    <li>Pilih periode bulan yang akan dibayar</li>
                    <li>Transfer sesuai nominal ke rekening sekolah</li>
                    <li>Upload bukti pembayaran</li>
                    <li>Tunggu verifikasi admin</li>
                </ol>
                
                <div class="alert alert-warning small mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Pastikan bukti transfer jelas terbaca dan sesuai dengan nominal.
                </div>
            </div>
        </div>

        <!-- Info Rekening Sekolah -->
        <div class="material-card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bank me-2"></i>Rekening Sekolah</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Bank BCA</small>
                    <div class="fw-bold">123-456-7890</div>
                    <div class="small">a.n. SMP NEGERI 1 CONTOH</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Bank Mandiri</small>
                    <div class="fw-bold">098-765-4321</div>
                    <div class="small">a.n. SMP NEGERI 1 CONTOH</div>
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
    const jumlahBulan = document.getElementById('jumlahBulan');
    const totalHarusBayar = document.getElementById('totalHarusBayar');
    const jumlahBayar = document.getElementById('jumlahBayar');
    const keteranganOtomatis = document.getElementById('keteranganOtomatis');
    const errorJumlah = document.getElementById('errorJumlah');
    const alertKurangBayar = document.getElementById('alertKurangBayar');
    const submitBtn = document.getElementById('submitBtn');
    const formBayarSpp = document.getElementById('formBayarSpp');
    const nominalSpp = {{ $nominalSpp }};

    let totalYangHarusDibayar = 0;

    function updatePerhitungan() {
        const mulai = parseInt(bulanMulai.value);
        const akhir = parseInt(bulanAkhir.value);
        
        if (mulai && akhir && mulai <= akhir) {
            const jumlah = (akhir - mulai) + 1;
            totalYangHarusDibayar = jumlah * nominalSpp;
            
            jumlahBulan.value = `${jumlah} bulan`;
            totalHarusBayar.value = `Rp ${totalYangHarusDibayar.toLocaleString('id-ID')}`;
            
            // Update keterangan otomatis
            const bulanMulaiNama = new Date(2000, mulai - 1).toLocaleString('id-ID', { month: 'long' });
            const bulanAkhirNama = new Date(2000, akhir - 1).toLocaleString('id-ID', { month: 'long' });
            const tahun = tahunSelect.value;
            
            if (jumlah === 1) {
                keteranganOtomatis.value = `Bayar SPP Bulan ${bulanMulaiNama} ${tahun}`;
            } else {
                keteranganOtomatis.value = `Bayar SPP ${jumlah} bulan (${bulanMulaiNama} - ${bulanAkhirNama} ${tahun})`;
            }

            // Set nilai default untuk jumlah bayar
            if (!jumlahBayar.value || jumlahBayar.value == 0) {
                jumlahBayar.value = totalYangHarusDibayar;
            }

            // Validasi jumlah bayar
            validateJumlahBayar();
        } else {
            jumlahBulan.value = '';
            totalHarusBayar.value = '';
            keteranganOtomatis.value = '';
            totalYangHarusDibayar = 0;
            hideAlerts();
        }
    }

    function validateJumlahBayar() {
        const jumlahBayarValue = parseInt(jumlahBayar.value) || 0;
        
        if (totalYangHarusDibayar > 0) {
            if (jumlahBayarValue < totalYangHarusDibayar) {
                // Tampilkan error dan alert
                errorJumlah.style.display = 'block';
                alertKurangBayar.style.display = 'block';
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-x-circle me-2"></i>Jumlah Bayar Kurang';
                return false;
            } else {
                // Sembunyikan error dan alert
                hideAlerts();
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-upload me-2"></i>Upload Bukti Bayar';
                return true;
            }
        }
        return true;
    }

    function hideAlerts() {
        errorJumlah.style.display = 'none';
        alertKurangBayar.style.display = 'none';
    }

    // Event listeners
    bulanMulai.addEventListener('change', updatePerhitungan);
    bulanAkhir.addEventListener('change', updatePerhitungan);
    tahunSelect.addEventListener('change', updatePerhitungan);
    
    // Validasi real-time saat input jumlah bayar berubah
    jumlahBayar.addEventListener('input', function() {
        validateJumlahBayar();
    });

    // Validasi sebelum form submit
    formBayarSpp.addEventListener('submit', function(e) {
        if (!validateJumlahBayar()) {
            e.preventDefault();
            alert('Jumlah bayar kurang dari total yang harus dibayar. Silakan periksa kembali.');
        }
    });

    // Inisialisasi pertama kali
    updatePerhitungan();
});
</script>
{{-- Tambahkan di file blade --}}
<script>
// Validasi real-time sebelum submit
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const bulanMulai = parseInt(document.getElementById('bulan_mulai').value);
            const bulanAkhir = parseInt(document.getElementById('bulan_akhir').value);
            const tahun = parseInt(document.getElementById('tahun').value);
            
            // Validasi bulan
            if (bulanMulai > bulanAkhir) {
                e.preventDefault();
                showAlert('❌ Bulan akhir harus lebih besar atau sama dengan bulan mulai!', 'danger');
                return;
            }
            
            // Validasi tahun
            const currentYear = new Date().getFullYear();
            if (tahun < currentYear - 1 || tahun > currentYear + 1) {
                e.preventDefault();
                showAlert('❌ Tahun harus antara ' + (currentYear - 1) + ' dan ' + (currentYear + 1) + '!', 'danger');
                return;
            }
        });
    }
    
    // Fungsi untuk show alert
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.querySelector('.container').prepend(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
});
</script>

@endsection