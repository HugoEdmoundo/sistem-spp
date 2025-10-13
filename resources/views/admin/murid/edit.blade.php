<!-- resources/views/admin/murid/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Edit Murid')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit Data Murid</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.murid.update', $murid->id) }}">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" value="{{ $murid->nama }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $murid->email }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="{{ $murid->username }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>NIP (Opsional)</label>
                    <input type="text" name="nip" class="form-control" value="{{ $murid->nip }}">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.murid.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection