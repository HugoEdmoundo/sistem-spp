<!-- resources/views/admin/tagihan/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Edit Tagihan')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2 text-gray-800">Edit Tagihan</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.tagihan.index') }}">Tagihan</a></li>
                            <li class="breadcrumb-item active">Edit Tagihan</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.tagihan.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card material-card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pencil-square me-2"></i>Edit Tagihan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.tagihan.update', $tagihan->id) }}" id="tagihanForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Student Info (Readonly) -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Murid</label>
                                <div class="form-control bg-light">
                                    <strong>{{ $tagihan->user->nama }}</strong> - {{ $tagihan->user->email }} ({{ $tagihan->user->kelas ?? 'Tidak ada kelas' }})
                                </div>
                                <div class="form-text text-muted">
                                    Murid tidak dapat diubah karena tagihan sudah terkait dengan data pembayaran.
                                </div>
                            </div>
                        </div>

                        <!-- Tagihan Type & Amount -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jenis Tagihan <span class="text-danger">*</span></label>
                                <select name="jenis" class="form-select material-form" required>
                                    <option value="spp" {{ $tagihan->jenis == 'spp' ? 'selected' : '' }}>SPP</option>
                                    <option value="custom" {{ $tagihan->jenis == 'custom' ? 'selected' : '' }}>Custom Tagihan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah Tagihan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="number" 
                                           name="jumlah" 
                                           class="form-control material-form" 
                                           min="0" 
                                           required 
                                           value="{{ old('jumlah', $tagihan->jumlah) }}"
                                           placeholder="0">
                                </div>
                                @error('jumlah')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Keterangan Tagihan <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="keterangan" 
                                       class="form-control material-form" 
                                       placeholder="Contoh: Denda keterlambatan, Seragam sekolah, Kegiatan study tour, dll" 
                                       required
                                       value="{{ old('keterangan', $tagihan->keterangan) }}">
                                @error('keterangan')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Period Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Bulan</label>
                                <select name="bulan" class="form-select material-form">
                                    <option value="">-- Pilih Bulan --</option>
                                    @php
                                        $months = [
                                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                        ];
                                    @endphp
                                    @foreach($months as $key => $month)
                                    <option value="{{ $key }}" {{ old('bulan', $tagihan->bulan) == $key ? 'selected' : '' }}>
                                        {{ $month }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tahun</label>
                                <select name="tahun" class="form-select material-form">
                                    <option value="">-- Pilih Tahun --</option>
                                    @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}" {{ old('tahun', $tagihan->tahun) == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Status Info -->
                        @if($tagihan->pembayaran)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Informasi:</strong> Tagihan ini sudah memiliki pembayaran dengan status 
                                    <span class="badge bg-{{ $tagihan->pembayaran->status == 'success' ? 'success' : ($tagihan->pembayaran->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ $tagihan->pembayaran->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Preview Section -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="bi bi-eye me-2"></i>Pratinjau Perubahan
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small class="text-muted">Murid:</small>
                                                <div class="fw-semibold">{{ $tagihan->user->nama }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Keterangan:</small>
                                                <div id="previewKeterangan" class="fw-semibold">{{ $tagihan->keterangan }}</div>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <small class="text-muted">Periode:</small>
                                                <div id="previewPeriode" class="fw-semibold">
                                                    @if($tagihan->bulan && $tagihan->tahun)
                                                        {{ DateTime::createFromFormat('!m', $tagihan->bulan)->format('F') }} {{ $tagihan->tahun }}
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <small class="text-muted">Jumlah:</small>
                                                <div id="previewJumlah" class="fw-bold text-success">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('admin.tagihan.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-warning" id="submitBtn">
                                        <i class="bi bi-check-circle me-2"></i>Update Tagihan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const keteranganInput = document.querySelector('input[name="keterangan"]');
        const jumlahInput = document.querySelector('input[name="jumlah"]');
        const bulanSelect = document.querySelector('select[name="bulan"]');
        const tahunSelect = document.querySelector('select[name="tahun"]');
        
        const previewKeterangan = document.getElementById('previewKeterangan');
        const previewPeriode = document.getElementById('previewPeriode');
        const previewJumlah = document.getElementById('previewJumlah');
        
        // Update preview function
        function updatePreview() {
            // Update description
            previewKeterangan.textContent = keteranganInput.value || '-';
            
            // Update amount
            const amount = jumlahInput.value ? parseInt(jumlahInput.value).toLocaleString('id-ID') : '0';
            previewJumlah.textContent = `Rp ${amount}`;
            
            // Update period
            const bulan = bulanSelect.value;
            const tahun = tahunSelect.value;
            if (bulan && tahun) {
                const monthNames = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                previewPeriode.textContent = `${monthNames[bulan - 1]} ${tahun}`;
            } else {
                previewPeriode.textContent = '-';
            }
        }
        
        // Add event listeners
        keteranganInput.addEventListener('input', updatePreview);
        jumlahInput.addEventListener('input', updatePreview);
        bulanSelect.addEventListener('change', updatePreview);
        tahunSelect.addEventListener('change', updatePreview);
        
        // Format amount input
        jumlahInput.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseInt(this.value).toString();
            }
        });
        
        // Form validation
        const form = document.getElementById('tagihanForm');
        const submitBtn = document.getElementById('submitBtn');
        
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                
                // Add Bootstrap validation styles
                const inputs = form.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                    }
                });
                
                alert('Harap lengkapi semua field yang wajib diisi!');
            } else {
                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memperbarui...';
            }
        });
        
        // Remove validation styles on input
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.checkValidity()) {
                    this.classList.remove('is-invalid');
                }
            });
        });
    });
</script>
@endsection