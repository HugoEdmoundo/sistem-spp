@section('title', 'Login')

@section('content')
<div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-md-4">
        <div class="material-card">
            <div class="card-header text-center bg-transparent border-0 pt-4">
                <h3 class="text-primary mb-2">
                    <i class="bi bi-wallet2 me-2"></i>SPP App
                </h3>
                <p class="text-muted">Sistem Pembayaran SPP</p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 @error('username') is-invalid @enderror" 
                                   id="username" name="username" value="{{ old('username') }}" 
                                   placeholder="Masukkan username" required autofocus>
                        </div>
                        @error('username')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-lock text-muted"></i>
                            </span>
                            <input type="password" class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                   id="password" name="password" placeholder="Masukkan password" required>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>
                </form>

                @if($errors->any())
                <div class="alert alert-danger mt-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Username atau password salah.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection