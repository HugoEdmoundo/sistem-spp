@extends('layouts.app')

@section('title', 'Dashboard Murid')

@section('content')
<div class="row mb-4">
    <div class="col-md-4 mb-4">
        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="bi bi-file-invoice"></i>
            </div>
            <div class="stat-value">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
            <div class="stat-label">Total Tagihan</div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="stat-card success">
            <div class="stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-value">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</div>
            <div class="stat-label">Total Dibayar</div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="stat-card info">
            <div class="stat-icon">
                <i class="bi bi-clock"></i>
            </div>
            <div class="stat-value">{{ $tagihanPending }}</div>
            <div class="stat-label">Menunggu Verifikasi</div>
        </div>
    </div>
</div>

<!-- Bayar SPP Fleksibel -->
<div class="row mb-4">
    <div class="col-12">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Bayar SPP Fleksibel</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('murid.bayar.spp') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Dari Bulan *</label>
                            <select name="bulan_mulai" class="form-control" id="bulanMulai" required>
                                <option value="">Pilih Bulan Mulai</option>
                                @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sampai Bulan *</label>
                            <select name="bulan_akhir" class="form-control" id="bulanAkhir" required>
                                <option value="">Pilih Bulan Akhir</option>
                                @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tahun *</label>
                            <select name="tahun" class="form-control" required>
                                <option value="">Pilih Tahun</option>
                                @for($i = date('Y'); $i <= date('Y') + 1; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
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
                            <small class="text-muted">Otomatis terhitung</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah Bayar *</label>
                            <input type="number" name="jumlah" class="form-control" 
                                   id="jumlahBayar" min="0" required>
                            <small class="text-muted">Masukkan jumlah yang dibayar</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Metode *</label>
                            <select name="metode" class="form-control" required>
                                <option value="Transfer Bank">Transfer Bank</option>
                                <option value="Tunai">Tunai</option>
                                <option value="E-Wallet">E-Wallet</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bukti Pembayaran *</label>
                            <input type="file" name="bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Keterangan (Otomatis)</label>
                            <input type="text" name="keterangan" class="form-control" id="keteranganOtomatis" readonly>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-2"></i>Upload Bukti Bayar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bulanMulai = document.getElementById('bulanMulai');
    const bulanAkhir = document.getElementById('bulanAkhir');
    const jumlahBulan = document.getElementById('jumlahBulan');
    const totalHarusBayar = document.getElementById('totalHarusBayar');
    const jumlahBayar = document.getElementById('jumlahBayar');
    const keteranganOtomatis = document.getElementById('keteranganOtomatis');
    const nominalSpp = {{ $nominalSpp }};

    function updatePerhitungan() {
        const mulai = parseInt(bulanMulai.value);
        const akhir = parseInt(bulanAkhir.value);
        
        if (mulai && akhir && mulai <= akhir) {
            const jumlah = (akhir - mulai) + 1;
            const total = jumlah * nominalSpp;
            
            jumlahBulan.value = `${jumlah} bulan`;
            totalHarusBayar.value = `Rp ${total.toLocaleString('id-ID')}`;
            
            // Update keterangan otomatis
            const bulanMulaiNama = new Date(2000, mulai - 1).toLocaleString('id-ID', { month: 'long' });
            const bulanAkhirNama = new Date(2000, akhir - 1).toLocaleString('id-ID', { month: 'long' });
            
            if (jumlah === 1) {
                keteranganOtomatis.value = `Bayar SPP Bulan ${bulanMulaiNama}`;
            } else {
                keteranganOtomatis.value = `Bayar SPP ${jumlah} bulan (${bulanMulaiNama} - ${bulanAkhirNama})`;
            }
        } else {
            jumlahBulan.value = '';
            totalHarusBayar.value = '';
            keteranganOtomatis.value = '';
        }
    }

    bulanMulai.addEventListener('change', updatePerhitungan);
    bulanAkhir.addEventListener('change', updatePerhitungan);
});
</script>

<!-- Tagihan Terbaru -->
@if(isset($tagihan) && $tagihan->count() > 0)
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Tagihan Terbaru</h5>
        <a href="{{ route('murid.tagihan.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Bulan/Tahun</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tagihan as $item)
                    <tr>
                        <td>{{ $item->periode }}</td>
                        <td>
                            <span class="badge {{ $item->jenis == 'spp' ? 'bg-primary' : 'bg-info' }}">
                                {{ ucfirst($item->jenis) }}
                            </span>
                        </td>
                        <td>{{ $item->keterangan }}</td>
                        <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td>
                            @if($item->status == 'unpaid')
                                <span class="badge bg-danger">Unpaid</span>
                            @elseif($item->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($item->status == 'success')
                                <span class="badge bg-success">Success</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>
                            @if($item->status == 'unpaid')
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                        data-bs-target="#uploadModal{{ $item->id }}">
                                    <i class="bi bi-upload"></i> Upload
                                </button>
                            @elseif($item->status == 'pending')
                                <span class="text-warning">Menunggu verifikasi</span>
                            @elseif($item->status == 'success')
                                <span class="text-success"><i class="bi bi-check"></i> Lunas</span>
                            @else
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                        data-bs-target="#uploadModal{{ $item->id }}">
                                    <i class="bi bi-redo"></i> Upload Ulang
                                </button>
                            @endif
                        </td>
                    </tr>

                    <!-- Modal Upload Bukti -->
                    <div class="modal fade" id="uploadModal{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('murid.upload.bukti', $item->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Metode Pembayaran</label>
                                            <select name="metode" class="form-control" required>
                                                <option value="Transfer Bank">Transfer Bank</option>
                                                <option value="Tunai">Tunai</option>
                                                <option value="E-Wallet">E-Wallet</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Bukti Pembayaran (JPG/PNG/PDF, max 2MB)</label>
                                            <input type="file" name="bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                                        </div>
                                        <div class="alert alert-info">
                                            <small>
                                                <i class="fas fa-info-circle"></i>
                                                Upload bukti pembayaran yang valid. Admin akan memverifikasi dalam 1x24 jam.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Upload</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Tagihan Terbaru</h5>
    </div>
    <div class="card-body text-center py-4">
        <i class="bi bi-file-earmark-text fs-1 text-muted mb-3"></i>
        <p class="text-muted">Belum ada tagihan.</p>
        <a href="{{ route('murid.tagihan.index') }}" class="btn btn-primary">Lihat Semua Tagihan</a>
    </div>
</div>
@endif
@endsection