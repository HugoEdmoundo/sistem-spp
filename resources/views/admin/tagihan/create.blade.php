<!-- resources/views/admin/tagihan/create.blade.php -->
@extends('layouts.app')

@section('title', 'Tambah Tagihan')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Tambah Tagihan Custom</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.tagihan.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Murid</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">Pilih Murid</option>
                        @foreach($murid as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }} ({{ $item->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Jenis Tagihan</label>
                    <select name="jenis" class="form-control" required>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Keterangan</label>
                    <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Denda, Seragam, Kegiatan, dll" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Bulan (Opsional untuk SPP)</label>
                    <select name="bulan" class="form-control">
                        <option value="">Pilih Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Tahun (Opsional untuk SPP)</label>
                    <select name="tahun" class="form-control">
                        <option value="">Pilih Tahun</option>
                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah" class="form-control" min="0" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.tagihan.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection