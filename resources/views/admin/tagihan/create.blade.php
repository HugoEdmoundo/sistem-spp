<!-- resources/views/admin/tagihan/create.blade.php -->
@extends('layouts.app')

@section('title', 'Tambah Tagihan')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Tambah Tagihan</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.tagihan.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Murid <span class="text-danger">*</span></label>
                    <select name="user_id" class="form-control" required>
                        <option value="">Pilih Murid</option>
                        @foreach($murid as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }} ({{ $item->username }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah" class="form-control" min="0" placeholder="0" required>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                    <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Denda, Seragam, Kegiatan, dll" required>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Tagihan</button>
                <a href="{{ route('admin.tagihan.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection