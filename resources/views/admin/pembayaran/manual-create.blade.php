<!-- resources/views/admin/pembayaran/manual-create.blade.php -->
@extends('layouts.app')

@section('title', 'Buat Pembayaran Manual')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="bi bi-cash-coin me-2"></i>Buat Pembayaran Manual</h4>
            <a href="{{ route('admin.pembayaran.history') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0">Form Pembayaran Manual</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pembayaran.manual.store') }}" method="POST" id="formPembayaran">
                    @csrf
                    
                    <!-- Tipe Pembayaran -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label fw-bold">Tipe Pembayaran <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipe_pembayaran" id="tipe_spp" value="spp" checked>
                                    <label class="form-check-label" for="tipe_spp">SPP</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipe_pembayaran" id="tipe_tagihan" value="tagihan">
                                    <label class="form-check-label" for="tipe_tagihan">Tagihan</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Murid -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="user_id" class="form-label">Murid <span class="text-danger">*</span></label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Pilih Murid</option>
                                @foreach($murid as $m)
                                    <option value="{{ $m->id }}">
                                        {{ $m->nama }} ({{ $m->username }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Form SPP -->
                    <div id="formSpp">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="bulan_mulai" class="form-label">Dari Bulan <span class="text-danger">*</span></label>
                                <select name="bulan_mulai" class="form-select" id="bulanMulai" required>
                                    <option value="">Pilih Bulan</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="bulan_akhir" class="form-label">Sampai Bulan <span class="text-danger">*</span></label>
                                <select name="bulan_akhir" class="form-select" id="bulanAkhir" required>
                                    <option value="">Pilih Bulan</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                                <select name="tahun" class="form-select" id="tahun" required>
                                    <option value="">Pilih Tahun</option>
                                    @foreach($tahunUntukSelect as $tahunItem)
                                        <option value="{{ $tahunItem }}" {{ $tahunItem == date('Y') ? 'selected' : '' }}>
                                            {{ $tahunItem }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Info SPP Otomatis -->
                        <div class="card border-info mb-3" id="infoSpp" style="display: none;">
                            <div class="card-body">
                                <h6 class="card-title">Informasi SPP</h6>
                                <div class="row">
                                    <div class="col-4">
                                        <small class="text-muted">Jumlah Bulan</small>
                                        <div class="fw-bold" id="infoJumlahBulan">-</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Nominal/Bulan</small>
                                        <div class="fw-bold">Rp {{ number_format($nominalSpp, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Total Tagihan</small>
                                        <div class="fw-bold text-primary" id="infoTotalSpp">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Tagihan -->
                    <div id="formTagihan" style="display: none;">
                        <div class="mb-3">
                            <label for="tagihan_id" class="form-label">Pilih Tagihan <span class="text-danger">*</span></label>
                            <select class="form-select" id="tagihan_id" name="tagihan_id">
                                <option value="">Pilih Tagihan</option>
                                @foreach($tagihan as $t)
                                    <option value="{{ $t->id }}" 
                                        data-jumlah="{{ $t->jumlah }}"
                                        data-dibayar="{{ $t->total_dibayar }}"
                                        data-sisa="{{ $t->sisa_tagihan }}"
                                        data-user-id="{{ $t->user_id }}">
                                        {{ $t->user->nama }} - {{ $t->keterangan }} - 
                                        Sisa: Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @if($tagihan->count() == 0)
                            <div class="text-muted mt-2">
                                <i class="bi bi-info-circle"></i> Tidak ada tagihan yang bisa dicicil
                            </div>
                            @endif
                        </div>

                        <!-- Info Tagihan -->
                        <div class="card border-primary mb-3" id="infoTagihan" style="display: none;">
                            <div class="card-body">
                                <h6 class="card-title">Informasi Tagihan</h6>
                                <div class="row">
                                    <div class="col-4">
                                        <small class="text-muted">Total Tagihan</small>
                                        <div class="fw-bold" id="infoTotalTagihan">-</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Sudah Dibayar</small>
                                        <div class="fw-bold text-success" id="infoDibayar">-</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Sisa Tagihan</small>
                                        <div class="fw-bold text-warning" id="infoSisaTagihan">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Pembayaran -->
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">Informasi Pembayaran</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="jumlah" class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="jumlah" name="jumlah" 
                                               value="1000" min="1000" required>
                                    </div>
                                    <div class="form-text" id="infoMinMax">
                                        Minimal bayar: Rp 1.000
                                    </div>

                                    <!-- Info Sisa Setelah Bayar -->
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
                                
                                <div class="col-md-6">
                                    <label for="metode" class="form-label">Metode <span class="text-danger">*</span></label>
                                    <select class="form-select" id="metode" name="metode" required>
                                        <option value="Tunai">Tunai</option>
                                        <option value="Transfer">Transfer</option>
                                        <option value="QRIS">QRIS</option>
                                    </select>

                                    <label for="tanggal_bayar" class="form-label mt-3">Tanggal Bayar <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal_bayar" name="tanggal_bayar" 
                                           value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="keterangan" name="keterangan" 
                                       value="Pembayaran manual oleh admin" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-circle me-2"></i>Simpan Pembayaran Manual
                        </button>
                        <a href="{{ route('admin.pembayaran.history') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading">Pembayaran Manual</h6>
                    <p class="mb-2 small">Fitur untuk mencatat pembayaran yang dilakukan secara langsung/tunai di tempat.</p>
                </div>

                <div class="alert alert-success" id="infoCardSpp">
                    <h6 class="alert-heading">Pembayaran SPP</h6>
                    <p class="small mb-0">• Buat tagihan SPP otomatis<br>• Bisa bayar cicilan<br>• System hitung otomatis</p>
                </div>

                <div class="alert alert-warning" id="infoCardTagihan" style="display: none;">
                    <h6 class="alert-heading">Pembayaran Tagihan</h6>
                    <p class="small mb-0">• Pilih tagihan yang sudah ada<br>• Bisa bayar cicilan<br>• Progress otomatis update</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipeSpp = document.getElementById('tipe_spp');
    const tipeTagihan = document.getElementById('tipe_tagihan');
    const formSpp = document.getElementById('formSpp');
    const formTagihan = document.getElementById('formTagihan');
    const infoCardSpp = document.getElementById('infoCardSpp');
    const infoCardTagihan = document.getElementById('infoCardTagihan');
    const userSelect = document.getElementById('user_id');
    const tagihanSelect = document.getElementById('tagihan_id');
    const bulanMulai = document.getElementById('bulanMulai');
    const bulanAkhir = document.getElementById('bulanAkhir');
    const tahun = document.getElementById('tahun');
    const jumlahInput = document.getElementById('jumlah');
    const infoMinMax = document.getElementById('infoMinMax');
    const infoSisa = document.getElementById('infoSisa');
    const infoLunas = document.getElementById('infoLunas');
    const sisaAmount = document.getElementById('sisaAmount');
    const nominalSpp = {{ $nominalSpp }};

    let maxJumlah = 0;
    let currentSisa = 0;

    // Toggle form
    function toggleForms() {
        if (tipeSpp.checked) {
            formSpp.style.display = 'block';
            formTagihan.style.display = 'none';
            infoCardSpp.style.display = 'block';
            infoCardTagihan.style.display = 'none';
            
            // Reset dan set required fields
            tagihanSelect.required = false;
            bulanMulai.required = true;
            bulanAkhir.required = true;
            tahun.required = true;
            
            // Reset tagihan
            tagihanSelect.value = '';
            document.getElementById('infoTagihan').style.display = 'none';
            
            // Update info SPP
            updateInfoSpp();
            
        } else {
            formSpp.style.display = 'none';
            formTagihan.style.display = 'block';
            infoCardSpp.style.display = 'none';
            infoCardTagihan.style.display = 'block';
            
            // Reset dan set required fields
            tagihanSelect.required = true;
            bulanMulai.required = false;
            bulanAkhir.required = false;
            tahun.required = false;
            
            // Reset SPP
            document.getElementById('infoSpp').style.display = 'none';
        }
        
        // Reset jumlah
        jumlahInput.value = 1000;
        jumlahInput.min = 1000;
        maxJumlah = 0;
        currentSisa = 0;
        updateInfoPembayaran();
    }

    // Update info SPP
    function updateInfoSpp() {
        const mulai = parseInt(bulanMulai.value);
        const akhir = parseInt(bulanAkhir.value);
        const tahunVal = parseInt(tahun.value);
        
        if (mulai && akhir && tahunVal && mulai <= akhir) {
            const jumlahBulan = (akhir - mulai) + 1;
            const totalTagihan = jumlahBulan * nominalSpp;
            
            // Update info
            document.getElementById('infoJumlahBulan').textContent = jumlahBulan + ' bulan';
            document.getElementById('infoTotalSpp').textContent = 'Rp ' + totalTagihan.toLocaleString('id-ID');
            
            // Tampilkan info
            document.getElementById('infoSpp').style.display = 'block';
            
            // Set max jumlah
            maxJumlah = totalTagihan;
            currentSisa = totalTagihan;
            jumlahInput.max = maxJumlah;
            infoMinMax.textContent = 'Minimal: Rp 1.000, Maksimal: Rp ' + totalTagihan.toLocaleString('id-ID');
            
        } else {
            document.getElementById('infoSpp').style.display = 'none';
            maxJumlah = 0;
            currentSisa = 0;
        }
        
        updateInfoPembayaran();
    }

    // Update info tagihan
    tagihanSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const infoTagihan = document.getElementById('infoTagihan');
        
        if (selected.value) {
            const total = parseFloat(selected.getAttribute('data-jumlah'));
            const dibayar = parseFloat(selected.getAttribute('data-dibayar'));
            const sisa = parseFloat(selected.getAttribute('data-sisa'));
            
            // Update info
            document.getElementById('infoTotalTagihan').textContent = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('infoDibayar').textContent = 'Rp ' + dibayar.toLocaleString('id-ID');
            document.getElementById('infoSisaTagihan').textContent = 'Rp ' + sisa.toLocaleString('id-ID');
            
            // Tampilkan info
            infoTagihan.style.display = 'block';
            
            // Set max jumlah
            maxJumlah = sisa;
            currentSisa = sisa;
            jumlahInput.max = sisa;
            jumlahInput.min = 1000;
            jumlahInput.value = Math.min(1000, sisa); // Set nilai default
            infoMinMax.textContent = 'Minimal: Rp 1.000, Maksimal: Rp ' + sisa.toLocaleString('id-ID');
            
        } else {
            infoTagihan.style.display = 'none';
            maxJumlah = 0;
            currentSisa = 0;
            jumlahInput.value = 1000;
        }
        
        updateInfoPembayaran();
    });

    // Update info pembayaran
    function updateInfoPembayaran() {
        const jumlah = parseFloat(jumlahInput.value) || 0;
        const sisaSetelahBayar = currentSisa - jumlah;
        
        if (jumlah > 0 && maxJumlah > 0) {
            // Update info sisa
            sisaAmount.textContent = 'Rp ' + (sisaSetelahBayar > 0 ? sisaSetelahBayar.toLocaleString('id-ID') : '0');
            infoSisa.style.display = 'block';
            
            // Update info lunas
            if (sisaSetelahBayar <= 0) {
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
    tipeSpp.addEventListener('change', toggleForms);
    tipeTagihan.addEventListener('change', toggleForms);
    bulanMulai.addEventListener('change', updateInfoSpp);
    bulanAkhir.addEventListener('change', updateInfoSpp);
    tahun.addEventListener('change', updateInfoSpp);
    jumlahInput.addEventListener('input', updateInfoPembayaran);

    // Validasi sebelum submit
    document.getElementById('formPembayaran').addEventListener('submit', function(e) {
        const jumlah = parseFloat(jumlahInput.value) || 0;
        
        if (jumlah < 1000) {
            e.preventDefault();
            alert('Jumlah bayar minimal Rp 1.000!');
            return;
        }
        
        if (maxJumlah > 0 && jumlah > maxJumlah) {
            e.preventDefault();
            alert('Jumlah bayar melebihi maksimal yang diizinkan!');
            return;
        }
        
        if (tipeTagihan.checked && !tagihanSelect.value) {
            e.preventDefault();
            alert('Pilih tagihan terlebih dahulu!');
            return;
        }
        
        if (tipeSpp.checked && (!bulanMulai.value || !bulanAkhir.value || !tahun.value)) {
            e.preventDefault();
            alert('Lengkapi periode SPP terlebih dahulu!');
            return;
        }
    });

    // Initialize
    toggleForms();
});
</script>
@endsection