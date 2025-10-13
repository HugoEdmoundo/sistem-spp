@extends('layouts.app')

@section('title', 'Profile Murid')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="material-card">
            <div class="card-body text-center">
                @if(auth()->user()->foto)
                    <img src="{{ asset('storage/' . auth()->user()->foto) }}" 
                        class="profile-photo mb-3" 
                        alt="Foto Profil">
                @else
                    <div class="d-flex align-items-center justify-content-center mb-3"
                        style="width: 150px; height: 150px; border-radius: 50%; background-color: #198754;">
                        <i class="bi bi-person-fill text-white fs-1"></i>
                    </div>
                @endif
                <h4>{{ auth()->user()->nama }}</h4>
                <p class="text-muted">{{ auth()->user()->email }}</p>
                <span class="badge bg-success">Murid</span>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="bi bi-person-badge me-1"></i>
                        NIP: {{ auth()->user()->nip ?? '-' }}
                    </small>
                </div>
                
                <div class="mt-1">
                    <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i>
                        Bergabung: {{ auth()->user()->created_at->format('d/m/Y') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="material-card">
            <div class="card-header">
                <h5 class="mb-0">Edit Profile</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Terdapat kesalahan dalam pengisian form. Silakan cek kembali.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('murid.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                   value="{{ old('nama', auth()->user()->nama) }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                                   value="{{ old('username', auth()->user()->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Foto Profile</label>
                            <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/jpeg,image/png">
                            <div class="form-text">Format: JPG, PNG (Maksimal: 2MB)</div>
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Untuk mengubah NIP, hubungi administrator.
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Profile
                        </button>
                        <a href="{{ route('murid.dashboard') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection