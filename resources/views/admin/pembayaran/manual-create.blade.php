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

<div class="row">
    <div class="col-md-8">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0">Form Pembayaran Manual</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pembayaran.manual.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">Murid <span class="text-danger">*</span></label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Pilih Murid</option>
                                @foreach($murid as $m)
                                    <option value="{{ $m->id }}" {{ old('user_id') == $m->id ? 'selected' : '' }}>
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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jenis_bayar" class="form-label">Jenis Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-select @error('jenis_bayar') is-invalid @enderror" id="jenis_bayar" name="jenis_bayar" required>
                                <option value="">Pilih Jenis</option>
                                <option value="SPP" {{ old('jenis_bayar') == 'SPP' ? 'selected' : '' }}>SPP</option>
                                <option value="Uang Bangunan" {{ old('jenis_bayar') == 'Uang Bangunan' ? 'selected' : '' }}>Uang Bangunan</option>
                                <option value="Uang Seragam" {{ old('jenis_bayar') == 'Uang Seragam' ? 'selected' : '' }}>Uang Seragam</option>
                                <option value="Uang Kegiatan" {{ old('jenis_bayar') == 'Uang Kegiatan' ? 'selected' : '' }}>Uang Kegiatan</option>
                                <option value="Lainnya" {{ old('jenis_bayar') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('jenis_bayar')
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

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                  id="keterangan" name="keterangan" rows="3" required
                                  placeholder="Detail keterangan pembayaran">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('jumlah') is-invalid @enderror" 
                                       id="jumlah" name="jumlah" value="{{ old('jumlah') }}" required min="0" step="1000">
                            </div>
                            @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tanggal_bayar" class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                                   id="tanggal_bayar" name="tanggal_bayar" value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
                            @error('tanggal_bayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="catatan_admin" class="form-label">Catatan Admin (Opsional)</label>
                        <textarea class="form-control @error('catatan_admin') is-invalid @enderror" 
                                  id="catatan_admin" name="catatan_admin" rows="2"
                                  placeholder="Catatan internal untuk pembayaran ini">{{ old('catatan_admin') }}</textarea>
                        @error('catatan_admin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-outline-secondary me-md-2">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Simpan Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
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
                    <h6 class="alert-heading">Pembayaran Tunai</h6>
                    <p class="small mb-0">Untuk pembayaran tunai langsung, pilih metode <strong>"Tunai"</strong> dan isi data dengan benar.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tagihanSelect = document.getElementById('tagihan_id');
        const jumlahInput = document.getElementById('jumlah');
        const keteranganInput = document.getElementById('keterangan');
        const jenisBayarSelect = document.getElementById('jenis_bayar');

        tagihanSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value) {
                // Ambil data dari atribut data-*
                const jumlah = selectedOption.getAttribute('data-jumlah');
                const keterangan = selectedOption.getAttribute('data-keterangan');
                
                // Isi otomatis field jumlah dan keterangan
                if (jumlah) {
                    jumlahInput.value = jumlah;
                }
                
                if (keterangan) {
                    keteranganInput.value = keterangan;
                    
                    // Coba deteksi jenis bayar dari keterangan
                    if (keterangan.toLowerCase().includes('spp')) {
                        jenisBayarSelect.value = 'SPP';
                    } else if (keterangan.toLowerCase().includes('bangunan')) {
                        jenisBayarSelect.value = 'Uang Bangunan';
                    } else if (keterangan.toLowerCase().includes('seragam')) {
                        jenisBayarSelect.value = 'Uang Seragam';
                    } else if (keterangan.toLowerCase().includes('kegiatan')) {
                        jenisBayarSelect.value = 'Uang Kegiatan';
                    }
                }
            } else {
                // Kosongkan jika tidak ada tagihan dipilih
                jumlahInput.value = '';
                keteranganInput.value = '';
            }
        });

        // Set default metode ke Tunai
        document.getElementById('metode').value = 'Tunai';
    });
</script>
@endsection