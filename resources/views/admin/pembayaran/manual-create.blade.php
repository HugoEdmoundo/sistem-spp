@extends('layouts.app')

@section('title', 'Buat Pembayaran Manual')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="bi bi-cash-coin me-2"></i>Buat Pembayaran Manual</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0">Form Pembayaran Manual</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pembayaran.manual.store') }}" method="POST" id="formPembayaran">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">Murid <span class="text-danger">*</span></label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Pilih Murid</option>
                                @foreach($murid as $m)
                                    <option value="{{ $m->id }}" {{ old('user_id') == $m->id ? 'selected' : '' }} data-spp="{{ $m->spp_nominal ?? 0 }}">
                                        {{ $m->nama }} ({{ $m->username }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tagihan_id" class="form-label">Tagihan (Opsional)</label>
                            <select class="form-select @error('tagihan_id') is-invalid @enderror" id="tagihan_id" name="tagihan_id">
                                <option value="">Pilih Tagihan</option>
                                @foreach($tagihan as $t)
                                    <option value="{{ $t->id }}" 
                                        data-jumlah="{{ $t->jumlah }}"
                                        data-keterangan="{{ $t->keterangan }}"
                                        data-jenis="{{ $t->jenis }}"
                                        {{ old('tagihan_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->user->nama }} - {{ $t->keterangan }} - Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tagihan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Input tersembunyi untuk tipe_bayar -->
                    <input type="hidden" name="tipe_bayar" id="tipe_bayar" value="spp">

                    <!-- Pilihan Tipe Pembayaran -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Tipe Pembayaran <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipe_bayar_ui" id="tipe_spp" value="spp" checked>
                                    <label class="form-check-label" for="tipe_spp">SPP</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipe_bayar_ui" id="tipe_lain" value="lain">
                                    <label class="form-check-label" for="tipe_lain">Pembayaran Lainnya</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form SPP -->
                    <div id="formSpp">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="bulan_mulai" class="form-label">Dari Bulan <span class="text-danger">*</span></label>
                                <select name="bulan_mulai" class="form-select @error('bulan_mulai') is-invalid @enderror" id="bulanMulai" required>
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
                                <label for="bulan_akhir" class="form-label">Sampai Bulan <span class="text-danger">*</span></label>
                                <select name="bulan_akhir" class="form-select @error('bulan_akhir') is-invalid @enderror" id="bulanAkhir" required>
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
                                <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                                <select name="tahun" class="form-select @error('tahun') is-invalid @enderror" id="tahun" required>
                                    <option value="">Pilih Tahun</option>
                                    @for($i = date('Y'); $i >= date('Y') - 2; $i--)
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

                        <!-- Perhitungan Otomatis SPP -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jumlah Bulan</label>
                                <input type="text" id="jumlahBulan" class="form-control" readonly>
                                <small class="text-muted">Otomatis terhitung</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nominal SPP/Bulan</label>
                                <input type="text" id="nominalSpp" class="form-control" readonly value="Rp 0">
                                <small class="text-muted">Berdasarkan murid</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Total Harus Dibayar</label>
                                <input type="text" id="totalHarusBayar" class="form-control" readonly>
                                <small class="text-muted">Otomatis terhitung</small>
                            </div>
                        </div>
                    </div>

                    <!-- Form Pembayaran Lainnya -->
                    <div id="formLain" style="display: none;">
                        <div class="mb-3">
                            <label for="keterangan_lain" class="form-label">Keterangan Pembayaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('keterangan_lain') is-invalid @enderror" 
                                   id="keterangan_lain" name="keterangan_lain" value="{{ old('keterangan_lain') }}"
                                   placeholder="Contoh: Uang Bangunan, Uang Seragam, dll.">
                            @error('keterangan_lain')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Input Jumlah dan Metode -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('jumlah') is-invalid @enderror" 
                                       id="jumlah" name="jumlah" value="{{ old('jumlah') }}" required min="0" step="1000">
                            </div>
                            <div id="errorJumlah" class="text-danger small mt-1" style="display: none;">
                                Jumlah bayar tidak sesuai dengan total yang harus dibayar!
                            </div>
                            @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="metode" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-select @error('metode') is-invalid @enderror" id="metode" name="metode" required>
                                <option value="">Pilih Metode</option>
                                <option value="Tunai" {{ old('metode') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                <option value="Transfer" {{ old('metode') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="QRIS" {{ old('metode') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                            </select>
                            @error('metode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Keterangan Otomatis -->
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                  id="keterangan" name="keterangan" rows="2" required
                                  placeholder="Detail keterangan pembayaran" readonly>{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_bayar" class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                                   id="tanggal_bayar" name="tanggal_bayar" value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
                            @error('tanggal_bayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="catatan_admin" class="form-label">Catatan Admin (Opsional)</label>
                            <textarea class="form-control @error('catatan_admin') is-invalid @enderror" 
                                      id="catatan_admin" name="catatan_admin" rows="2"
                                      placeholder="Catatan internal untuk pembayaran ini">{{ old('catatan_admin') }}</textarea>
                            @error('catatan_admin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Alert untuk validasi -->
                    <div class="alert alert-warning" id="alertNominal" style="display: none;">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <span id="alertText"></span>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-outline-secondary me-md-2">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-circle me-2"></i>Simpan Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Informasi card (tetap sama) -->
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading">Pembayaran Manual</h6>
                    <p class="mb-2 small">Fitur untuk mencatat pembayaran yang dilakukan secara langsung/tunai di tempat.</p>
                    <hr>
                    <ul class="small mb-0 ps-3">
                        <li>Pembayaran langsung berstatus <strong>"Diterima"</strong></li>
                        <li>Tidak perlu upload bukti transfer</li>
                        <li>Tagihan terkait otomatis dilunasi</li>
                        <li>Notifikasi dikirim ke murid</li>
                        <li>Data tercatat dalam laporan</li>
                    </ul>
                </div>

                <div class="alert alert-success">
                    <h6 class="alert-heading">Validasi Otomatis</h6>
                    <p class="small mb-0">Sistem akan memvalidasi kesesuaian nominal pembayaran dengan total yang harus dibayar.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userSelect = document.getElementById('user_id');
    const tagihanSelect = document.getElementById('tagihan_id');
    const tipeSppRadio = document.getElementById('tipe_spp');
    const tipeLainRadio = document.getElementById('tipe_lain');
    const tipeBayarHidden = document.getElementById('tipe_bayar');
    const formSpp = document.getElementById('formSpp');
    const formLain = document.getElementById('formLain');
    const bulanMulai = document.getElementById('bulanMulai');
    const bulanAkhir = document.getElementById('bulanAkhir');
    const tahunSelect = document.getElementById('tahun');
    const jumlahBulan = document.getElementById('jumlahBulan');
    const nominalSppDisplay = document.getElementById('nominalSpp');
    const totalHarusBayar = document.getElementById('totalHarusBayar');
    const jumlahInput = document.getElementById('jumlah');
    const keteranganInput = document.getElementById('keterangan');
    const keteranganLainInput = document.getElementById('keterangan_lain');
    const errorJumlah = document.getElementById('errorJumlah');
    const alertNominal = document.getElementById('alertNominal');
    const alertText = document.getElementById('alertText');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('formPembayaran');

    let nominalSpp = 0;
    let totalDibutuhkan = 0;
    let isSppMode = true;

    // Toggle antara form SPP dan form Lainnya
    tipeSppRadio.addEventListener('change', function() {
        if (this.checked) {
            formSpp.style.display = 'block';
            formLain.style.display = 'none';
            isSppMode = true;
            tipeBayarHidden.value = 'spp';
            updateKeterangan();
            updatePerhitungan();
            validateRequiredFields();
        }
    });

    tipeLainRadio.addEventListener('change', function() {
        if (this.checked) {
            formSpp.style.display = 'none';
            formLain.style.display = 'block';
            isSppMode = false;
            tipeBayarHidden.value = 'lain';
            updateKeterangan();
            validateNominal();
            validateRequiredFields();
        }
    });

    // Update nominal SPP ketika murid dipilih
    userSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        nominalSpp = parseInt(selectedOption.getAttribute('data-spp')) || 0;
        nominalSppDisplay.value = 'Rp ' + nominalSpp.toLocaleString('id-ID');
        updatePerhitungan();
    });

    // Auto-fill dari tagihan
    tagihanSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const jumlah = selectedOption.getAttribute('data-jumlah');
            const keterangan = selectedOption.getAttribute('data-keterangan');
            const jenis = selectedOption.getAttribute('data-jenis');
            
            if (jumlah) {
                jumlahInput.value = jumlah;
                totalDibutuhkan = parseInt(jumlah);
            }
            
            if (keterangan) {
                // Deteksi apakah tagihan ini SPP atau lainnya
                if (jenis === 'spp' || keterangan.toLowerCase().includes('spp')) {
                    tipeSppRadio.checked = true;
                    tipeBayarHidden.value = 'spp';
                    formSpp.style.display = 'block';
                    formLain.style.display = 'none';
                    isSppMode = true;
                    updatePerhitungan();
                } else {
                    tipeLainRadio.checked = true;
                    tipeBayarHidden.value = 'lain';
                    formSpp.style.display = 'none';
                    formLain.style.display = 'block';
                    isSppMode = false;
                    keteranganLainInput.value = keterangan;
                }
                updateKeterangan();
            }
            
            // Validasi nominal setelah auto-fill
            validateNominal();
        } else {
            // Reset jika tidak ada tagihan dipilih
            if (isSppMode) {
                updatePerhitungan();
            } else {
                totalDibutuhkan = 0;
                validateNominal();
            }
        }
    });

    // Update perhitungan otomatis untuk SPP
    function updatePerhitungan() {
        if (!isSppMode) return;

        const mulai = parseInt(bulanMulai.value);
        const akhir = parseInt(bulanAkhir.value);
        const tahun = tahunSelect.value;
        
        if (mulai && akhir && tahun && mulai <= akhir) {
            const jumlahBulanValue = (akhir - mulai) + 1;
            totalDibutuhkan = jumlahBulanValue * nominalSpp;
            
            jumlahBulan.value = `${jumlahBulanValue} bulan`;
            totalHarusBayar.value = 'Rp ' + totalDibutuhkan.toLocaleString('id-ID');

            // Set nilai default untuk jumlah bayar jika kosong
            if (!jumlahInput.value || jumlahInput.value == 0) {
                jumlahInput.value = totalDibutuhkan;
            }

            updateKeterangan();
            validateNominal();
        } else {
            jumlahBulan.value = '';
            totalHarusBayar.value = '';
            totalDibutuhkan = 0;
            validateNominal();
        }
    }

    // Update keterangan otomatis
    function updateKeterangan() {
        if (isSppMode) {
            const mulai = parseInt(bulanMulai.value);
            const akhir = parseInt(bulanAkhir.value);
            const tahun = tahunSelect.value;
            
            if (mulai && akhir && tahun) {
                const jumlahBulanValue = (akhir - mulai) + 1;
                const bulanMulaiNama = new Date(2000, mulai - 1).toLocaleString('id-ID', { month: 'long' });
                const bulanAkhirNama = new Date(2000, akhir - 1).toLocaleString('id-ID', { month: 'long' });
                
                if (jumlahBulanValue === 1) {
                    keteranganInput.value = `Bayar SPP Bulan ${bulanMulaiNama} ${tahun}`;
                } else {
                    keteranganInput.value = `Bayar SPP ${jumlahBulanValue} bulan (${bulanMulaiNama} - ${bulanAkhirNama} ${tahun})`;
                }
            }
        } else {
            keteranganInput.value = keteranganLainInput.value || '';
        }
    }

    // Validasi nominal
    function validateNominal() {
        const jumlahBayar = parseInt(jumlahInput.value) || 0;
        
        if (isSppMode && totalDibutuhkan > 0) {
            if (jumlahBayar !== totalDibutuhkan) {
                errorJumlah.style.display = 'block';
                alertNominal.style.display = 'block';
                alertText.textContent = `Jumlah bayar (Rp ${jumlahBayar.toLocaleString('id-ID')}) tidak sesuai dengan total yang harus dibayar (Rp ${totalDibutuhkan.toLocaleString('id-ID')})`;
                submitBtn.disabled = true;
                return false;
            } else {
                errorJumlah.style.display = 'none';
                alertNominal.style.display = 'none';
                submitBtn.disabled = false;
                return true;
            }
        } else {
            errorJumlah.style.display = 'none';
            alertNominal.style.display = 'none';
            submitBtn.disabled = false;
            return true;
        }
    }

    // Validasi field required berdasarkan tipe
    function validateRequiredFields() {
        if (isSppMode) {
            bulanMulai.required = true;
            bulanAkhir.required = true;
            tahunSelect.required = true;
            keteranganLainInput.required = false;
        } else {
            bulanMulai.required = false;
            bulanAkhir.required = false;
            tahunSelect.required = false;
            keteranganLainInput.required = true;
        }
    }

    // Event listeners
    bulanMulai.addEventListener('change', updatePerhitungan);
    bulanAkhir.addEventListener('change', updatePerhitungan);
    tahunSelect.addEventListener('change', updatePerhitungan);
    jumlahInput.addEventListener('input', validateNominal);
    keteranganLainInput.addEventListener('input', updateKeterangan);

    // Validasi form sebelum submit
    form.addEventListener('submit', function(e) {
        if (isSppMode && !validateNominal()) {
            e.preventDefault();
            alert('Jumlah bayar tidak sesuai dengan total yang harus dibayar untuk SPP!');
            return false;
        }

        // Validasi untuk pembayaran lain
        if (!isSppMode && !keteranganLainInput.value.trim()) {
            e.preventDefault();
            alert('Keterangan pembayaran harus diisi untuk pembayaran lainnya!');
            return false;
        }

        return true;
    });

    // Set default metode ke Tunai
    document.getElementById('metode').value = 'Tunai';

    // Inisialisasi pertama kali
    validateRequiredFields();
    updatePerhitungan();
});
</script>
@endsection