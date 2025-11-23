<!-- resources/views/murid/profile.blade.php -->
@extends('layouts.app')

@section('title', 'Profile Murid')

@section('content')
<div class="container-fluid px-0">
    <!-- Mobile Header -->
    <div class="bg-primary text-white p-3 sticky-top">
        <div class="d-flex align-items-center">
            {{-- <a href="{{ route('murid.dashboard') }}" class="text-white me-3">
                <i class="bi bi-arrow-left fs-5"></i>
            </a> --}}
            <div class="flex-grow-1">
                <h6 class="mb-0 fw-bold">Profile Saya</h6>
                <small class="opacity-75">Kelola informasi akun Anda</small>
            </div>
            <div class="bg-white bg-opacity-20 rounded p-1">
                <i class="bi bi-person text-white"></i>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-3">
        <!-- Profile Card Mobile -->
        <div class="card profile-card-mobile border-0 shadow-sm mb-4 position-relative overflow-hidden">
        <!-- Background Effects -->
        <div class="bg-shapes"></div>
        <div class="bg-glow"></div>
        
        <div class="card-body p-4 text-center position-relative" style="z-index: 2;">
            <!-- Profile Photo -->
            <div class="mb-3">
                @if(auth()->user()->foto)
                    <img src="{{ asset('storage/' . auth()->user()->foto) }}" 
                        class="profile-photo-mobile" 
                        alt="Foto Profil">
                @else
                    <div class="profile-photo-placeholder-mobile mx-auto mb-3">
                        <i class="bi bi-person-fill text-white"></i>
                    </div>
                @endif
            </div>

            <!-- User Info -->
            <h5 class="fw-bold mb-1 text-white">{{ auth()->user()->nama }}</h5>
            <p class="text-white-50 mb-2 small">{{ auth()->user()->email }}</p>
            <span class="badge bg-white text-primary mb-3 shadow-sm">Murid</span>

            <!-- Additional Info -->
            <div class="profile-info-mobile">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="info-item">
                            <i class="bi bi-person-badge text-white mb-1"></i>
                            <small class="text-white-75 d-block">NIP</small>
                            <div class="fw-medium small text-white">{{ auth()->user()->nip ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-item">
                            <i class="bi bi-calendar text-white mb-1"></i>
                            <small class="text-white-75 d-block">Bergabung</small>
                            <div class="fw-medium small text-white">{{ auth()->user()->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Edit Profile Form Mobile -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 d-flex align-items-center">
                    <i class="bi bi-pencil-square text-primary me-2"></i>
                    Edit Profile
                </h6>
            </div>
            <div class="card-body p-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <div class="flex-grow-1">{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div class="flex-grow-1">
                                Terdapat kesalahan dalam pengisian form. Silakan cek kembali.
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('murid.profile.update') }}" enctype="multipart/form-data" id="profileForm">
                    @csrf
                    
                    <!-- Nama -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="nama" 
                               class="form-control form-control-lg @error('nama') is-invalid @enderror" 
                               value="{{ old('nama', auth()->user()->nama) }}" 
                               required
                               placeholder="Masukkan nama lengkap">
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               class="form-control form-control-lg @error('email') is-invalid @enderror" 
                               value="{{ old('email', auth()->user()->email) }}" 
                               required
                               placeholder="Masukkan alamat email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Username -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Username <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="username" 
                               class="form-control form-control-lg @error('username') is-invalid @enderror" 
                               value="{{ old('username', auth()->user()->username) }}" 
                               required
                               placeholder="Masukkan username">
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Foto Profile -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Foto Profile</label>
                        <div class="file-upload-wrapper">
                            <input type="file" 
                                   name="foto" 
                                   class="form-control @error('foto') is-invalid @enderror" 
                                   accept="image/jpeg,image/png"
                                   id="fotoInput">
                            <div class="form-text small">
                                <i class="bi bi-info-circle me-1"></i>
                                Format: JPG, PNG (Maksimal: 2MB)
                            </div>
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-2 text-center" style="display: none;">
                            <img id="preview" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeImage()">
                                <i class="bi bi-trash me-1"></i>Hapus
                            </button>
                        </div>
                    </div>
                    
                    <!-- Password Section -->
                    <div class="password-section mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small fw-semibold mb-0">Password</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="togglePasswordSection()">
                                <i class="bi bi-key me-1"></i>Ubah
                            </button>
                        </div>
                        
                        <div id="passwordFields" style="display: none;">
                            <!-- Password Baru -->
                            <div class="mb-2">
                                <label class="form-label small">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" 
                                           name="password" 
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Masukkan password baru"
                                           id="passwordInput">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('passwordInput')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text small">
                                    Kosongkan jika tidak ingin mengubah password
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Konfirmasi Password -->
                            <div class="mb-2">
                                <label class="form-label small">Konfirmasi Password</label>
                                <div class="input-group">
                                    <input type="password" 
                                           name="password_confirmation" 
                                           class="form-control"
                                           placeholder="Konfirmasi password baru"
                                           id="passwordConfirmationInput">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('passwordConfirmationInput')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Info NIP -->
                    <div class="alert alert-info mb-3">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-info-circle me-2 mt-1"></i>
                            <div>
                                <small class="fw-semibold d-block">Informasi NIP</small>
                                <small>Untuk mengubah NIP, hubungi administrator.</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="sticky-bottom bg-white border-top p-3 mt-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg py-3 fw-semibold">
                                <i class="bi bi-check-circle me-2"></i>
                                Update Profile
                            </button>
                            <a href="{{ route('murid.dashboard') }}" class="btn btn-outline-secondary py-3">
                                <i class="bi bi-arrow-left me-2"></i>
                                Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Mobile Optimizations */
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 1020;
}

.sticky-bottom {
    position: sticky;
    bottom: 0;
    z-index: 1020;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
}

/* Profile Card Mobile */
.profile-card-mobile {
    border-radius: 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    position: relative;
    overflow: hidden;
}

/* Animated Background Shapes */
.bg-shapes {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.4) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.2) 0%, transparent 50%);
    animation: floatShapes 6s ease-in-out infinite;
}

/* Background Glow Effect */
.bg-glow {
    position: absolute;
    top: -50%;
    left: -50%;
    right: -50%;
    bottom: -50%;
    background: 
        radial-gradient(circle at center, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    animation: rotateGlow 8s linear infinite;
    opacity: 0.5;
}

.profile-photo-mobile {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 4px solid rgba(255,255,255,0.3);
    object-fit: cover;
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.profile-photo-mobile:hover {
    transform: scale(1.05);
    border-color: rgba(255, 255, 255, 0.5);
    box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.4),
        0 0 0 2px rgba(255, 255, 255, 0.2);
}

.profile-photo-placeholder-mobile {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #198754, #20c997);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 4px solid rgba(255,255,255,0.3);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.profile-photo-placeholder-mobile:hover {
    transform: scale(1.05);
    border-color: rgba(255, 255, 255, 0.5);
    box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.4),
        0 0 0 2px rgba(255, 255, 255, 0.2);
}

.profile-photo-placeholder-mobile i {
    font-size: 2.5rem;
    transition: transform 0.3s ease;
}

.profile-photo-placeholder-mobile:hover i {
    transform: scale(1.1);
}

.profile-info-mobile {
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 1rem;
    margin-top: 1rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.profile-info-mobile:hover {
    background: rgba(255,255,255,0.15);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

.info-item {
    padding: 0.5rem;
    transition: all 0.3s ease;
}

.info-item:hover {
    transform: translateY(-2px);
}

.info-item i {
    font-size: 1.5rem;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
    transition: all 0.3s ease;
}

.info-item:hover i {
    transform: scale(1.2);
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.4));
}

/* Animations */
@keyframes floatShapes {
    0%, 100% {
        transform: translateY(0px) scale(1);
    }
    50% {
        transform: translateY(-10px) scale(1.02);
    }
}

@keyframes rotateGlow {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

/* Card Hover Effect */
.profile-card-mobile {
    transition: all 0.4s ease;
}

.profile-card-mobile:hover {
    transform: translateY(-5px);
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(255, 255, 255, 0.1);
}

/* Text Effects */
.text-white {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.badge {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.badge:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

/* Mobile Optimizations */
@media (max-width: 576px) {
    .profile-card-mobile:hover {
        transform: none; /* Disable hover transform on mobile */
    }
    
    .profile-photo-mobile,
    .profile-photo-placeholder-mobile {
        width: 80px;
        height: 80px;
    }
    
    .profile-photo-placeholder-mobile i {
        font-size: 2rem;
    }
    
    .profile-info-mobile {
        padding: 0.75rem;
    }
}

/* Pulse Animation for Attention */
@keyframes gentlePulse {
    0%, 100% {
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    50% {
        box-shadow: 0 8px 32px rgba(255, 255, 255, 0.1);
    }
}

.profile-card-mobile {
    animation: gentlePulse 4s ease-in-out infinite;
}

/* Gradient Text Effect */
h5.fw-bold {
    background: linear-gradient(135deg, #ffffff, #e3f2fd);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Form Styles */
.form-control-lg {
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Card Styles */
.card {
    border-radius: 16px;
}

.card-header {
    border-bottom: 1px solid #e9ecef;
    border-radius: 16px 16px 0 0 !important;
}

/* Button Styles */
.btn {
    border-radius: 12px;
    font-weight: 600;
}

.btn-lg {
    padding: 1rem 1.5rem;
    font-size: 1.1rem;
}

/* Alert Styles */
.alert {
    border-radius: 12px;
    border: none;
}

/* File Upload */
.file-upload-wrapper {
    position: relative;
}

.file-upload-wrapper .form-control {
    padding: 0.75rem 1rem;
    border-radius: 12px;
}

/* Password Toggle */
.input-group .btn {
    border-radius: 0 12px 12px 0;
}

/* Image Preview */
.img-thumbnail {
    border-radius: 12px;
    border: 2px solid #dee2e6;
}

/* Responsive Adjustments */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 0;
        padding-right: 0;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .profile-photo-mobile {
        width: 80px;
        height: 80px;
    }
    
    .profile-photo-placeholder-mobile {
        width: 80px;
        height: 80px;
    }
    
    .profile-photo-placeholder-mobile i {
        font-size: 2rem;
    }
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.profile-card-mobile,
.card {
    animation: fadeIn 0.5s ease-in-out;
}

/* Touch Feedback */
.btn:active {
    transform: scale(0.98);
    transition: transform 0.1s ease;
}
</style>

<script>
// Additional interactive effects
document.addEventListener('DOMContentLoaded', function() {
    const profileCard = document.querySelector('.profile-card-mobile');
    
    // Add click ripple effect
    profileCard.addEventListener('click', function(e) {
        const ripple = document.createElement('div');
        ripple.style.cssText = `
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
            z-index: 1;
        `;
        
        const rect = profileCard.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        
        profileCard.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

// Add ripple animation to CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>

<script>
// Image Preview Functionality
document.getElementById('fotoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});

function removeImage() {
    document.getElementById('fotoInput').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}

// Password Section Toggle
let passwordSectionVisible = false;

function togglePasswordSection() {
    const passwordFields = document.getElementById('passwordFields');
    const toggleButton = document.querySelector('.password-section .btn');
    
    passwordSectionVisible = !passwordSectionVisible;
    
    if (passwordSectionVisible) {
        passwordFields.style.display = 'block';
        toggleButton.innerHTML = '<i class="bi bi-x me-1"></i>Batal';
        toggleButton.classList.remove('btn-outline-primary');
        toggleButton.classList.add('btn-outline-danger');
    } else {
        passwordFields.style.display = 'none';
        toggleButton.innerHTML = '<i class="bi bi-key me-1"></i>Ubah';
        toggleButton.classList.remove('btn-outline-danger');
        toggleButton.classList.add('btn-outline-primary');
        
        // Clear password fields
        document.getElementById('passwordInput').value = '';
        document.getElementById('passwordConfirmationInput').value = '';
    }
}

// Password Visibility Toggle
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Form Validation
document.getElementById('profileForm').addEventListener('submit', function(e) {
    const password = document.getElementById('passwordInput').value;
    const passwordConfirmation = document.getElementById('passwordConfirmationInput').value;
    
    if (password && password !== passwordConfirmation) {
        e.preventDefault();
        alert('Password dan konfirmasi password tidak cocok!');
        return false;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memproses...';
    submitBtn.disabled = true;
});

// Mobile-specific enhancements
document.addEventListener('DOMContentLoaded', function() {
    // Add touch feedback to interactive elements
    const interactiveElements = document.querySelectorAll('.btn, .card');
    interactiveElements.forEach(el => {
        el.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        el.addEventListener('touchend', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Prevent zoom on input focus (iOS)
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.fontSize = '16px';
        });
    });
});
</script>
@endsection