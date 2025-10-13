@extends('layouts.app')

@section('title', 'Setting SPP')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="bi bi-gear me-2"></i>Setting Nominal SPP</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0">Atur Nominal SPP</h5>
            </div>
            <div class="card-body">
                @if($setting)
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Setting Saat Ini:</strong> Rp {{ number_format($setting->nominal, 0, ',', '.') }} 
                    (berlaku mulai {{ \Carbon\Carbon::parse($setting->berlaku_mulai)->format('d/m/Y') }})
                </div>
                @endif

                <form method="POST" action="{{ route('admin.spp-setting.update') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nominal SPP *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="nominal" class="form-control" 
                                       value="{{ old('nominal', $setting->nominal ?? '') }}" 
                                       placeholder="500000" min="0" required>
                            </div>
                            @error('nominal')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Berlaku Mulai *</label>
                            <input type="date" name="berlaku_mulai" class="form-control" 
                                   value="{{ old('berlaku_mulai', $setting->berlaku_mulai ?? now()->format('Y-m-d')) }}" 
                                   required>
                            @error('berlaku_mulai')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-text">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            Setting ini akan berlaku untuk tagihan SPP yang dibuat setelah tanggal berlaku.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Simpan Setting
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0">Informasi</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6><i class="bi bi-lightbulb me-2"></i>Cara Kerja:</h6>
                    <ul class="small">
                        <li>Setting nominal SPP untuk periode mendatang</li>
                        <li>Tagihan SPP otomatis dibuat setiap tanggal 1</li>
                        <li>Nominal mengikuti setting yang aktif</li>
                    </ul>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-calendar-event me-2"></i>Jadwal Otomatis:</h6>
                    <ul class="small">
                        <li>Generate tagihan: Setiap tanggal 1</li>
                        <li>Status awal: Unpaid</li>
                        <li>Murid upload bukti pembayaran</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Perhatian:</strong> Setting baru tidak mempengaruhi tagihan yang sudah dibuat.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Setting -->
<div class="row mt-4">
    <div class="col-12">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0">Riwayat Setting SPP</h5>
            </div>
            <div class="card-body">
                @php
                    $allSettings = \App\Models\SppSetting::orderBy('berlaku_mulai', 'desc')->get();
                @endphp
                
                @if($allSettings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nominal</th>
                                <th>Berlaku Mulai</th>
                                <th>Tanggal Setting</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allSettings as $index => $sppSetting)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>Rp {{ number_format($sppSetting->nominal, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($sppSetting->berlaku_mulai)->format('d/m/Y') }}</td>
                                <td>{{ $sppSetting->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($sppSetting->id === $setting->id)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-gear fs-1 text-muted mb-3"></i>
                    <p class="text-muted">Belum ada setting SPP.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection