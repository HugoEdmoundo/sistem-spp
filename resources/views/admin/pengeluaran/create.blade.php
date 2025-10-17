@extends('layouts.app')

@section('title', 'Tambah Pengeluaran')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Tambah Pengeluaran Baru</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.pengeluaran.store') }}">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori *</label>
                    <select name="kategori" class="form-control" id="kategoriSelect" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($kategori as $kat)
                        <option value="{{ $kat }}">{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal *</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Keterangan *</label>
                <input type="text" name="keterangan" class="form-control" id="keteranganInput" 
                       value="{{ old('keterangan') }}" placeholder="Contoh: Bayar listrik bulan Januari" required>
            </div>

            <div class="mb-3" id="customKeterangan" style="display: none;">
                <label class="form-label">Keterangan Custom *</label>
                <input type="text" name="keterangan_custom" class="form-control" 
                       placeholder="Masukkan keterangan pengeluaran">
            </div>

            <div class="mb-3">
                <label class="form-label">Jumlah *</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="jumlah" class="form-control" 
                           value="{{ old('jumlah') }}" placeholder="100000" min="0" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-2"></i>Simpan Pengeluaran
            </button>
            <a href="{{ route('admin.pengeluaran.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<script>
document.getElementById('kategoriSelect').addEventListener('change', function() {
    const customDiv = document.getElementById('customKeterangan');
    const keteranganInput = document.getElementById('keteranganInput');
    
    if (this.value === 'Other') {
        customDiv.style.display = 'block';
        keteranganInput.required = false;
        keteranganInput.disabled = true;
    } else {
        customDiv.style.display = 'none';
        keteranganInput.required = true;
        keteranganInput.disabled = false;
    }
});
</script>
@endsection