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
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

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

                    <!-- Form SPP -->
                    <div id="formSpp">
                        <!-- Data Murid untuk SPP -->
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
                                    @for($i = date('Y'); $i >= date('Y')-2; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
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
                                        data-keterangan="{{ $t->keterangan }}"
                                        data-user-id="{{ $t->user_id }}">
                                        {{ $t->user->nama }} - {{ $t->keterangan }} - Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Jumlah dan Metode -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <!-- UBAH: readonly dan tambah hidden input untuk jumlah -->
                                <input type="text" class="form-control" id="jumlah_display" readonly style="background-color: #f8f9fa;">
                                <input type="hidden" id="jumlah" name="jumlah" required>
                            </div>
                            <small class="text-muted">Jumlah otomatis terisi berdasarkan pilihan</small>
                        </div>
                        <div class="col-md-6">
                            <label for="metode" class="form-label">Metode <span class="text-danger">*</span></label>
                            <select class="form-select" id="metode" name="metode" required>
                                <option value="Tunai">Tunai</option>
                                <option value="Transfer">Transfer</option>
                                <option value="QRIS">QRIS</option>
                            </select>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="keterangan" name="keterangan" required readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- Tanggal dan Catatan -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tanggal_bayar" class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_bayar" name="tanggal_bayar" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="catatan_admin" class="form-label">Catatan Admin</label>
                            <textarea class="form-control" id="catatan_admin" name="catatan_admin" rows="2"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Simpan Pembayaran
                    </button>
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

                <div class="alert alert-success" id="infoSpp">
                    <h6 class="alert-heading">Pembayaran SPP</h6>
                    <p class="small mb-0">• Pilih murid dan periode SPP<br>• Sistem hitung otomatis total<br>• Jumlah tidak bisa diubah</p>
                </div>

                <div class="alert alert-warning" id="infoTagihan" style="display: none;">
                    <h6 class="alert-heading">Pembayaran Tagihan</h6>
                    <p class="small mb-0">• Pilih tagihan yang sudah ada<br>• Murid otomatis terpilih<br>• Jumlah otomatis dari tagihan</p>
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
    const userSelect = document.getElementById('user_id');
    const tagihanSelect = document.getElementById('tagihan_id');
    const bulanMulai = document.getElementById('bulanMulai');
    const bulanAkhir = document.getElementById('bulanAkhir');
    const tahun = document.getElementById('tahun');
    const jumlahDisplay = document.getElementById('jumlah_display');
    const jumlahInput = document.getElementById('jumlah');
    const keteranganInput = document.getElementById('keterangan');
    const infoSpp = document.getElementById('infoSpp');
    const infoTagihan = document.getElementById('infoTagihan');

    // Ambil nominal SPP dari PHP (dilewatkan dari controller)
    const nominalSpp = {{ $nominalSpp }};

    // Toggle form
    function toggleForms() {
        if (tipeSpp.checked) {
            formSpp.style.display = 'block';
            formTagihan.style.display = 'none';
            infoSpp.style.display = 'block';
            infoTagihan.style.display = 'none';
            
            // Reset dan set required fields
            tagihanSelect.required = false;
            userSelect.required = true;
            bulanMulai.required = true;
            bulanAkhir.required = true;
            tahun.required = true;
            
            resetTagihan();
            calculateSPP();
        } else {
            formSpp.style.display = 'none';
            formTagihan.style.display = 'block';
            infoSpp.style.display = 'none';
            infoTagihan.style.display = 'block';
            
            // Reset dan set required fields
            tagihanSelect.required = true;
            userSelect.required = false;
            bulanMulai.required = false;
            bulanAkhir.required = false;
            tahun.required = false;
            
            resetSPP();
        }
    }

    function resetSPP() {
        bulanMulai.value = '';
        bulanAkhir.value = '';
        tahun.value = '';
        jumlahDisplay.value = '';
        jumlahInput.value = '';
        keteranganInput.value = '';
    }

    function resetTagihan() {
        tagihanSelect.value = '';
        jumlahDisplay.value = '';
        jumlahInput.value = '';
        keteranganInput.value = '';
    }

    // Event listeners untuk toggle
    tipeSpp.addEventListener('change', toggleForms);
    tipeTagihan.addEventListener('change', toggleForms);

    // Auto calculate SPP - PERBAIKAN DI SINI
    function calculateSPP() {
        const mulai = parseInt(bulanMulai.value);
        const akhir = parseInt(bulanAkhir.value);
        const tahunVal = tahun.value;

        if (mulai && akhir && tahunVal && mulai <= akhir) {
            const jumlahBulan = (akhir - mulai) + 1;
            const total = jumlahBulan * nominalSpp; // Gunakan nominalSpp dari PHP
            
            // Update display dan hidden input
            jumlahDisplay.value = 'Rp ' + total.toLocaleString('id-ID');
            jumlahInput.value = total;
            
            const bulanMulaiNama = new Date(2000, mulai-1).toLocaleString('id-ID', { month: 'long' });
            const bulanAkhirNama = new Date(2000, akhir-1).toLocaleString('id-ID', { month: 'long' });
            
            if (jumlahBulan === 1) {
                keteranganInput.value = `Bayar SPP Bulan ${bulanMulaiNama} ${tahunVal}`;
            } else {
                keteranganInput.value = `Bayar SPP ${jumlahBulan} bulan (${bulanMulaiNama} - ${bulanAkhirNama} ${tahunVal})`;
            }
        } else {
            jumlahDisplay.value = '';
            jumlahInput.value = '';
            keteranganInput.value = '';
        }
    }

    // Auto fill tagihan
    tagihanSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            const jumlah = selected.getAttribute('data-jumlah');
            const keterangan = selected.getAttribute('data-keterangan');
            const userId = selected.getAttribute('data-user-id');
            
            // Set hidden user_id untuk controller
            document.querySelector('input[name="user_id"]')?.remove();
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'user_id';
            hiddenInput.value = userId;
            formPembayaran.appendChild(hiddenInput);
            
            // Update display dan hidden input
            jumlahDisplay.value = 'Rp ' + parseInt(jumlah).toLocaleString('id-ID');
            jumlahInput.value = jumlah;
            keteranganInput.value = 'Pembayaran ' + keterangan;
        } else {
            resetTagihan();
        }
    });

    // Event listeners untuk SPP - HAPUS userSelect dari sini
    bulanMulai.addEventListener('change', calculateSPP);
    bulanAkhir.addEventListener('change', calculateSPP);
    tahun.addEventListener('change', calculateSPP);

    // Set default metode
    document.getElementById('metode').value = 'Tunai';

    // Initialize
    toggleForms();
});
</script>
@endsection