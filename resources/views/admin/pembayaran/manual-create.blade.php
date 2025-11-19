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
                                    <label class="form-check-label" for="tipe_spp">SPP Baru</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipe_pembayaran" id="tipe_spp_cicilan" value="spp_cicilan">
                                    <label class="form-check-label" for="tipe_spp_cicilan">SPP Cicilan</label>
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

                    <!-- Form SPP Baru -->
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
                                <h6 class="card-title">Informasi SPP Baru</h6>
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
                                        <small class="text-muted">Total Harus Bayar</small>
                                        <div class="fw-bold text-primary" id="infoTotalSpp">-</div>
                                    </div>
                                </div>
                                
                                <!-- Info Sudah Dibayar & Sisa -->
                                <div class="row mt-2" id="infoPembayaranSebelumnya" style="display: none;">
                                    <div class="col-6">
                                        <small class="text-muted">Sudah Dibayar</small>
                                        <div class="fw-bold text-success" id="infoSudahDibayar">-</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Sisa Tagihan</small>
                                        <div class="fw-bold text-warning" id="infoSisaSpp">-</div>
                                    </div>
                                </div>
                                
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i> 
                                        Murid bisa melanjutkan cicilan SPP ini di menu "Tagihan Saya"
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form SPP Cicilan -->
                    <div id="formSppCicilan" style="display: none;">
                        <div class="mb-3">
                            <label for="spp_cicilan_id" class="form-label">Pilih SPP yang Masih Cicilan <span class="text-danger">*</span></label>
                            <select class="form-select" id="spp_cicilan_id" name="spp_cicilan_id">
                                <option value="">Pilih Periode SPP</option>
                                <!-- Options akan diisi oleh JavaScript -->
                            </select>
                            <div class="form-text">
                                Pilih periode SPP yang masih memiliki cicilan
                            </div>
                        </div>

                        <!-- Info SPP Cicilan -->
                        <div class="card border-warning mb-3" id="infoSppCicilan" style="display: none;">
                            <div class="card-body">
                                <h6 class="card-title">Detail Cicilan SPP</h6>
                                <div class="row">
                                    <div class="col-4">
                                        <small class="text-muted">Periode</small>
                                        <div class="fw-bold" id="infoPeriodeCicilan">-</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Total Tagihan</small>
                                        <div class="fw-bold" id="infoTotalSppCicilan">-</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Jumlah Bulan</small>
                                        <div class="fw-bold" id="infoJumlahBulanCicilan">-</div>
                                    </div>
                                </div>
                                
                                <div class="row mt-2">
                                    <div class="col-4">
                                        <small class="text-muted">Sudah Dibayar</small>
                                        <div class="fw-bold text-success" id="infoSudahDibayarCicilan">-</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Sisa Tagihan</small>
                                        <div class="fw-bold text-warning" id="infoSisaSppCicilan">-</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Status</small>
                                        <div class="fw-bold" id="infoStatusCicilan">-</div>
                                    </div>
                                </div>

                                <!-- Detail Per Bulan -->
                                <div class="mt-3" id="detailBulanContainer" style="display: none;">
                                    <small class="text-muted d-block mb-2">Detail Per Bulan:</small>
                                    <div id="detailBulan" class="small">
                                        <!-- Detail bulan akan diisi oleh JavaScript -->
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
                                <input type="text" class="form-control" id="keterangan" name="keterangan" required>
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
                    <h6 class="alert-heading">SPP Baru</h6>
                    <p class="small mb-0">• Buat periode SPP baru<br>• Bisa bayar cicilan<br>• System hitung otomatis</p>
                </div>

                <div class="alert alert-warning" id="infoCardSppCicilan" style="display: none;">
                    <h6 class="alert-heading">SPP Cicilan</h6>
                    <p class="small mb-0">• Lanjutkan cicilan SPP<br>• Pilih dari yang sudah ada<br>• Progress otomatis</p>
                </div>

                <div class="alert alert-primary" id="infoCardTagihan" style="display: none;">
                    <h6 class="alert-heading">Tagihan</h6>
                    <p class="small mb-0">• Pilih tagihan yang sudah ada<br>• Bisa bayar cicilan<br>• Progress otomatis update</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipeSpp = document.getElementById('tipe_spp');
    const tipeSppCicilan = document.getElementById('tipe_spp_cicilan');
    const tipeTagihan = document.getElementById('tipe_tagihan');
    const formSpp = document.getElementById('formSpp');
    const formSppCicilan = document.getElementById('formSppCicilan');
    const formTagihan = document.getElementById('formTagihan');
    const userSelect = document.getElementById('user_id');
    const sppCicilanSelect = document.getElementById('spp_cicilan_id');
    const tagihanSelect = document.getElementById('tagihan_id');
    const bulanMulai = document.getElementById('bulanMulai');
    const bulanAkhir = document.getElementById('bulanAkhir');
    const tahun = document.getElementById('tahun');
    const jumlahInput = document.getElementById('jumlah');
    const keteranganInput = document.getElementById('keterangan');
    const infoMinMax = document.getElementById('infoMinMax');
    const infoSisa = document.getElementById('infoSisa');
    const infoLunas = document.getElementById('infoLunas');
    const sisaAmount = document.getElementById('sisaAmount');

    let maxJumlah = 0;
    let currentSisa = 0;
    const nominalSpp = {{ $nominalSpp }};

    // Toggle form
    function toggleForms() {
        // Reset semua form
        formSpp.style.display = 'none';
        formSppCicilan.style.display = 'none';
        formTagihan.style.display = 'none';
        
        // Reset required fields
        bulanMulai.required = false;
        bulanAkhir.required = false;
        tahun.required = false;
        sppCicilanSelect.required = false;
        tagihanSelect.required = false;
        
        // Reset info cards
        document.getElementById('infoCardSpp').style.display = 'none';
        document.getElementById('infoCardSppCicilan').style.display = 'none';
        document.getElementById('infoCardTagihan').style.display = 'none';

        if (tipeSpp.checked) {
            formSpp.style.display = 'block';
            document.getElementById('infoCardSpp').style.display = 'block';
            bulanMulai.required = true;
            bulanAkhir.required = true;
            tahun.required = true;
            keteranganInput.value = 'Pembayaran SPP manual oleh admin';
            
        } else if (tipeSppCicilan.checked) {
            formSppCicilan.style.display = 'block';
            document.getElementById('infoCardSppCicilan').style.display = 'block';
            sppCicilanSelect.required = true;
            keteranganInput.value = 'Pembayaran cicilan SPP manual oleh admin';
            
            // Load SPP cicilan options jika user sudah dipilih
            if (userSelect.value) {
                loadSppCicilanOptions();
            }
            
        } else if (tipeTagihan.checked) {
            formTagihan.style.display = 'block';
            document.getElementById('infoCardTagihan').style.display = 'block';
            tagihanSelect.required = true;
            keteranganInput.value = 'Pembayaran tagihan manual oleh admin';
        }
        
        // Reset jumlah
        resetJumlah();
    }

    // Load SPP cicilan options berdasarkan user
    // Ganti bagian loadSppCicilanOptions di JavaScript dengan ini:
    function loadSppCicilanOptions() {
        const userId = userSelect.value;
        
        if (!userId) {
            sppCicilanSelect.innerHTML = '<option value="">Pilih Murid terlebih dahulu</option>';
            return;
        }

        // Loading state
        sppCicilanSelect.innerHTML = '<option value="">Loading SPP yang masih cicilan...</option>';

        console.log('Loading SPP cicilan for user:', userId);

        fetch(`/admin/get-spp-cicilan/${userId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('SPP Cicilan Data:', data);
                
                if (data.length > 0) {
                    sppCicilanSelect.innerHTML = '<option value="">Pilih Periode SPP</option>';
                    data.forEach(spp => {
                        const option = document.createElement('option');
                        option.value = spp.id;
                        option.textContent = `${spp.periode} - Sisa: Rp ${spp.sisa_tagihan.toLocaleString('id-ID')}`;
                        option.setAttribute('data-info', JSON.stringify(spp));
                        sppCicilanSelect.appendChild(option);
                    });
                    
                    console.log('Loaded ' + data.length + ' SPP cicilan options');
                } else {
                    sppCicilanSelect.innerHTML = '<option value="">Tidak ada SPP yang masih cicilan</option>';
                    console.log('No SPP cicilan found');
                }
            })
            .catch(error => {
                console.error('Error loading SPP cicilan options:', error);
                sppCicilanSelect.innerHTML = '<option value="">Error: ' + error.message + '</option>';
            });
    }
    
    // Ganti bagian fetch di function loadSppCicilanOptions
    function loadSppCicilanOptions() {
        const userId = userSelect.value;
        
        if (!userId) {
            sppCicilanSelect.innerHTML = '<option value="">Pilih Murid terlebih dahulu</option>';
            return;
        }

        // Loading state
        sppCicilanSelect.innerHTML = '<option value="">Loading SPP yang masih cicilan...</option>';

        console.log('Loading SPP cicilan for user:', userId);

        // ✅ PERBAIKI URL INI - gunakan route name atau path yang benar
        fetch(`/admin/get-spp-cicilan/${userId}`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('SPP Cicilan Data:', data);
                
                if (data && data.length > 0) {
                    sppCicilanSelect.innerHTML = '<option value="">Pilih Periode SPP</option>';
                    data.forEach(spp => {
                        const option = document.createElement('option');
                        option.value = spp.id;
                        option.textContent = `${spp.periode} - Sisa: Rp ${spp.sisa_tagihan.toLocaleString('id-ID')}`;
                        option.setAttribute('data-info', JSON.stringify(spp));
                        sppCicilanSelect.appendChild(option);
                    });
                    
                    console.log('Loaded ' + data.length + ' SPP cicilan options');
                } else {
                    sppCicilanSelect.innerHTML = '<option value="">Tidak ada SPP yang masih cicilan</option>';
                    console.log('No SPP cicilan found');
                }
            })
            .catch(error => {
                console.error('Error loading SPP cicilan options:', error);
                sppCicilanSelect.innerHTML = '<option value="">Error loading data. Coba refresh halaman.</option>';
            });
    }

    // Update info SPP Baru
    function updateInfoSpp() {
        const mulai = parseInt(bulanMulai.value);
        const akhir = parseInt(bulanAkhir.value);
        const tahunVal = parseInt(tahun.value);
        const userId = userSelect.value;
        
        if (mulai && akhir && tahunVal && mulai <= akhir && userId) {
            const jumlahBulan = (akhir - mulai) + 1;
            const totalTagihan = jumlahBulan * nominalSpp;
            
            // Loading state
            document.getElementById('infoSpp').style.display = 'block';
            document.getElementById('infoJumlahBulan').textContent = 'Loading...';
            document.getElementById('infoTotalSpp').textContent = 'Loading...';
            
            fetch(`/admin/check-spp-payment?user_id=${userId}&tahun=${tahunVal}&bulan_mulai=${mulai}&bulan_akhir=${akhir}`)
                .then(response => response.json())
                .then(data => {
                    // Update info dasar
                    document.getElementById('infoJumlahBulan').textContent = data.jumlah_bulan + ' bulan';
                    document.getElementById('infoTotalSpp').textContent = 'Rp ' + data.total_harus_bayar.toLocaleString('id-ID');
                    
                    // Update info pembayaran sebelumnya
                    if (data.total_dibayar > 0) {
                        document.getElementById('infoSudahDibayar').textContent = 'Rp ' + data.total_dibayar.toLocaleString('id-ID');
                        document.getElementById('infoSisaSpp').textContent = 'Rp ' + data.sisa_tagihan.toLocaleString('id-ID');
                        document.getElementById('infoPembayaranSebelumnya').style.display = 'block';
                        
                        maxJumlah = data.sisa_tagihan;
                        currentSisa = data.sisa_tagihan;
                        jumlahInput.max = maxJumlah;
                        jumlahInput.value = Math.min(1000, data.sisa_tagihan);
                        
                        infoMinMax.textContent = 'Minimal: Rp 1.000, Maksimal: Rp ' + data.sisa_tagihan.toLocaleString('id-ID');
                        
                    } else {
                        document.getElementById('infoPembayaranSebelumnya').style.display = 'none';
                        
                        maxJumlah = totalTagihan;
                        currentSisa = totalTagihan;
                        jumlahInput.max = maxJumlah;
                        jumlahInput.value = 1000;
                        
                        infoMinMax.textContent = 'Minimal: Rp 1.000, Maksimal: Rp ' + totalTagihan.toLocaleString('id-ID');
                    }
                    
                    updateInfoPembayaran();
                    
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Fallback
                    document.getElementById('infoJumlahBulan').textContent = jumlahBulan + ' bulan';
                    document.getElementById('infoTotalSpp').textContent = 'Rp ' + totalTagihan.toLocaleString('id-ID');
                    document.getElementById('infoPembayaranSebelumnya').style.display = 'none';
                    
                    maxJumlah = totalTagihan;
                    currentSisa = totalTagihan;
                    jumlahInput.max = maxJumlah;
                    jumlahInput.value = 1000;
                    infoMinMax.textContent = 'Minimal: Rp 1.000, Maksimal: Rp ' + totalTagihan.toLocaleString('id-ID');
                    
                    updateInfoPembayaran();
                });
            
        } else {
            document.getElementById('infoSpp').style.display = 'none';
            document.getElementById('infoPembayaranSebelumnya').style.display = 'none';
            resetJumlah();
        }
    }

    // Update info SPP Cicilan ketika pilihan berubah
    sppCicilanSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const infoSppCicilan = document.getElementById('infoSppCicilan');
        
        if (selected.value && selected.getAttribute('data-info')) {
            const sppInfo = JSON.parse(selected.getAttribute('data-info'));
            
            // Update info
            document.getElementById('infoPeriodeCicilan').textContent = sppInfo.periode;
            document.getElementById('infoTotalSppCicilan').textContent = 'Rp ' + sppInfo.total_tagihan.toLocaleString('id-ID');
            document.getElementById('infoJumlahBulanCicilan').textContent = sppInfo.jumlah_bulan + ' bulan';
            document.getElementById('infoSudahDibayarCicilan').textContent = 'Rp ' + sppInfo.total_dibayar.toLocaleString('id-ID');
            document.getElementById('infoSisaSppCicilan').textContent = 'Rp ' + sppInfo.sisa_tagihan.toLocaleString('id-ID');
            document.getElementById('infoStatusCicilan').textContent = sppInfo.status;
            
            // Update detail per bulan
            if (sppInfo.detail_bulan && sppInfo.detail_bulan.length > 0) {
                let detailHtml = '';
                sppInfo.detail_bulan.forEach(bulan => {
                    const statusIcon = bulan.status === 'lunas' ? '✅' : 
                                     bulan.status === 'cicilan' ? '🟡' : '⚪';
                    const statusText = bulan.status === 'lunas' ? 'Lunas' : 
                                     bulan.status === 'cicilan' ? 'Cicilan' : 'Belum Bayar';
                    
                    detailHtml += `
                        <div class="d-flex justify-content-between border-bottom py-1">
                            <span>${statusIcon} ${bulan.nama_bulan}</span>
                            <span class="text-muted small">Rp ${bulan.sudah_dibayar.toLocaleString('id-ID')} / ${bulan.nominal_bulan.toLocaleString('id-ID')}</span>
                            <span class="badge bg-${bulan.status === 'lunas' ? 'success' : bulan.status === 'cicilan' ? 'warning' : 'secondary'}">${statusText}</span>
                        </div>
                    `;
                });
                document.getElementById('detailBulan').innerHTML = detailHtml;
                document.getElementById('detailBulanContainer').style.display = 'block';
            } else {
                document.getElementById('detailBulanContainer').style.display = 'none';
            }
            
            // Tampilkan info
            infoSppCicilan.style.display = 'block';
            
            // Set max jumlah
            maxJumlah = sppInfo.sisa_tagihan;
            currentSisa = sppInfo.sisa_tagihan;
            jumlahInput.max = maxJumlah;
            jumlahInput.min = 1000;
            jumlahInput.value = Math.min(1000, sppInfo.sisa_tagihan);
            infoMinMax.textContent = 'Minimal: Rp 1.000, Maksimal: Rp ' + sppInfo.sisa_tagihan.toLocaleString('id-ID');
            
        } else {
            infoSppCicilan.style.display = 'none';
            document.getElementById('detailBulanContainer').style.display = 'none';
            resetJumlah();
        }
        
        updateInfoPembayaran();
    });

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
            jumlahInput.value = Math.min(1000, sisa);
            infoMinMax.textContent = 'Minimal: Rp 1.000, Maksimal: Rp ' + sisa.toLocaleString('id-ID');
            
        } else {
            infoTagihan.style.display = 'none';
            resetJumlah();
        }
        
        updateInfoPembayaran();
    });

    // Update info pembayaran
    function updateInfoPembayaran() {
        const jumlah = parseFloat(jumlahInput.value) || 0;
        const sisaSetelahBayar = currentSisa - jumlah;
        
        if (jumlah > 0 && currentSisa > 0) {
            sisaAmount.textContent = 'Rp ' + (sisaSetelahBayar > 0 ? sisaSetelahBayar.toLocaleString('id-ID') : '0');
            infoSisa.style.display = 'block';
            
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

    // Reset jumlah input
    function resetJumlah() {
        jumlahInput.value = 1000;
        jumlahInput.min = 1000;
        jumlahInput.max = '';
        maxJumlah = 0;
        currentSisa = 0;
        infoMinMax.textContent = 'Minimal: Rp 1.000';
        updateInfoPembayaran();
    }

    // Event listeners
    tipeSpp.addEventListener('change', toggleForms);
    tipeSppCicilan.addEventListener('change', toggleForms);
    tipeTagihan.addEventListener('change', toggleForms);
    
    userSelect.addEventListener('change', function() {
        if (tipeSppCicilan.checked) {
            loadSppCicilanOptions();
        }
    });
    
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
            alert('Jumlah bayar melebihi sisa tagihan/SPP!');
            return;
        }
        
        if (tipeTagihan.checked && !tagihanSelect.value) {
            e.preventDefault();
            alert('Pilih tagihan terlebih dahulu!');
            return;
        }
        
        if (tipeSppCicilan.checked && !sppCicilanSelect.value) {
            e.preventDefault();
            alert('Pilih periode SPP terlebih dahulu!');
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