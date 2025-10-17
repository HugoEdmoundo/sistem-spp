@extends('layouts.app')

@section('title', 'Profile Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Profile Saya</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Info -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    @if(auth()->user()->foto)
                        <img src="{{ asset('storage/' . auth()->user()->foto) }}" 
                            class="rounded-circle mb-3" 
                            alt="Foto Profil"
                            style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #dee2e6;">
                    @else
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 120px; height: 120px; border: 3px solid #dee2e6;">
                            <i class="bi bi-person text-muted" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    <h5 class="mb-1">{{ auth()->user()->nama }}</h5>
                    <p class="text-muted mb-2">{{ auth()->user()->email }}</p>
                    <span class="badge bg-primary">{{ ucfirst(auth()->user()->role) }}</span>
                    
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="fw-bold text-dark">{{ $totalMurid ?? 0 }}</div>
                                    <small class="text-muted">Murid</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="fw-bold text-dark">{{ $pembayaranPending ?? 0 }}</div>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold text-dark">{{ \App\Models\Tagihan::count() }}</div>
                                <small class="text-muted">Tagihan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" 
                                       value="{{ auth()->user()->nama }}" required>
                                @error('nama')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" 
                                       value="{{ auth()->user()->email }}" required>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" 
                                       value="{{ auth()->user()->username }}" required>
                                @error('username')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Foto Profile</label>
                                <input type="file" name="foto" class="form-control" 
                                       accept="image/*">
                                <div class="form-text">Format: JPG, PNG, JPEG. Maksimal 2MB</div>
                                @error('foto')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <hr>
                                <h6 class="mb-3">Ubah Password</h6>
                                <p class="text-muted small mb-3">Kosongkan jika tidak ingin mengubah password</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password" class="form-control" 
                                       placeholder="Masukkan password baru">
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" 
                                       placeholder="Konfirmasi password baru">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card-header {
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.25rem;
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
    }
    
    .form-control:focus {
        border-color: #1E8449;
        box-shadow: 0 0 0 0.2rem rgba(30, 132, 73, 0.25);
    }
    
    .btn-primary {
        background-color: #1E8449;
        border-color: #1E8449;
    }
    
    .btn-primary:hover {
        background-color: #197c3d;
        border-color: #197c3d;
    }
</style>
@endsection