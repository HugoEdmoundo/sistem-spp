@extends('layouts.app')

@section('title', 'Tambah Murid')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Tambah Murid Baru</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.murid.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>NIP (Opsional)</label>
                    <input type="text" name="nip" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <small class="text-muted">Password default: 123456789</small>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.murid.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection