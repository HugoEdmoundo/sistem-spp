<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPP App - @yield('title')</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        {!! $cssContent ?? '
        /* ================ VARIABLES ================ */
        :root {
          --primary: #1E8449;   /* Hijau Segar (utama banget) */
          --secondary: #145A32; /* Hijau Gelap (navbar/footer) */
          --accent: #F6A21E;    /* Oranye aksen */
          --light: #FFFFFF;     /* Putih */
          --dark: #1C1C1C;      /* Hitam Abu */
          --success: #27AE60;   /* Bootstrap Success style */
          --info: #2ECC71;      /* Hijau Muda (hover/link) */
          --warning: #f39c12;   /* Kuning/Oranye */
          --danger: #e74c3c;    /* Merah */
          --white: #ffffff;
          --black: #000000;
          --gray: #95a5a6;
          --dark-gray: #7f8c8d;
          --light-gray: #bdc3c7;
          --font-primary: \"Poppins\", sans-serif;
          --font-secondary: \"Montserrat\", sans-serif;
          --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
          --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
          --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
          --transition: all 0.3s ease;
          --border-radius: 0.375rem;
          --border-radius-lg: 0.5rem;
          --border-radius-xl: 1rem;
          --toska-1: #1E8449;   /* Hijau utama */
          --toska-2: #2ECC71;   /* Hijau muda pendukung */
          --deep: #0B3D2E;      /* Hijau sangat gelap */
          --muted: #6b7280;
        }

        /* ================ BASE STYLES ================ */
        *,
        *::before,
        *::after {
          box-sizing: border-box;
          margin: 0;
          padding: 0;
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #198754; /* opsional: biar ada garis hijau rapi */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        html {
          scroll-behavior: smooth;
        }

        body {
          font-family: var(--font-primary);
          color: var(--dark);
          line-height: 1.6;
          background-color: #f8fafc;
          overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
          font-family: var(--font-secondary);
          font-weight: 700;
          line-height: 1.2;
          margin-bottom: 1rem;
        }

        a {
          text-decoration: none;
          color: var(--secondary);
          transition: var(--transition);
        }

        a:hover {
          color: var(--info);
          text-decoration: none;
        }

        img {
          max-width: 100%;
          height: auto;
          vertical-align: middle;
        }

        ul, ol {
          padding-left: 1.5rem;
        }

        /* ================ MATERIAL DESIGN COMPONENTS ================ */

        /* Cards */
        .material-card {
          background: var(--white);
          border-radius: var(--border-radius-lg);
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
          border: none;
          transition: var(--transition);
          overflow: hidden;
        }

        .material-card:hover {
          box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
          transform: translateY(-2px);
        }

        .material-card .card-header {
          background: transparent;
          border-bottom: 1px solid rgba(0, 0, 0, 0.1);
          padding: 1.25rem 1.5rem;
        }

        .material-card .card-body {
          padding: 1.5rem;
        }

        .material-card .card-footer {
          background: transparent;
          border-top: 1px solid rgba(0, 0, 0, 0.1);
          padding: 1rem 1.5rem;
        }

        /* Buttons */
        .btn {
          font-weight: 500;
          padding: 0.5rem 1.25rem;
          border-radius: var(--border-radius);
          transition: var(--transition);
          display: inline-flex;
          align-items: center;
          justify-content: center;
          gap: 0.5rem;
          border: none;
          cursor: pointer;
          position: relative;
          overflow: hidden;
        }

        .btn::before {
          content: \'\';
          position: absolute;
          top: 50%;
          left: 50%;
          width: 0;
          height: 0;
          background: rgba(255, 255, 255, 0.2);
          border-radius: 50%;
          transition: var(--transition);
          transform: translate(-50%, -50%);
        }

        .btn:hover::before {
          width: 300px;
          height: 300px;
        }

        .btn:active {
          transform: translateY(1px);
        }

        .btn-lg {
          padding: 0.75rem 1.5rem;
          font-size: 1.1rem;
        }

        .btn-primary {
          background-color: var(--primary);
          color: white;
        }

        .btn-primary:hover {
          background-color: #197c3d;
          color: white;
          transform: translateY(-1px);
          box-shadow: 0 4px 12px rgba(30, 132, 73, 0.3);
        }

        .btn-success {
          background-color: var(--success);
          color: white;
        }

        .btn-warning {
          background-color: var(--warning);
          color: white;
        }

        .btn-danger {
          background-color: var(--danger);
          color: white;
        }

        .btn-info {
          background-color: var(--info);
          color: white;
        }

        .btn-outline-primary {
          border: 2px solid var(--primary);
          color: var(--primary);
          background: transparent;
        }

        .btn-outline-primary:hover {
          background-color: var(--primary);
          color: white;
        }

        /* Navigation */
        .material-navbar {
          background: linear-gradient(135deg, var(--secondary), var(--primary));
          box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
          padding: 0.75rem 0;
        }

        .material-navbar .navbar-brand {
          font-weight: 700;
          font-size: 1.5rem;
          color: white !important;
        }

        .material-navbar .nav-link {
          color: rgba(255, 255, 255, 0.9) !important;
          font-weight: 500;
          padding: 0.5rem 1rem;
          border-radius: var(--border-radius);
          margin: 0 0.25rem;
          transition: var(--transition);
        }

        .material-navbar .nav-link:hover {
          color: white !important;
          background: rgba(255, 255, 255, 0.1);
        }

        .material-navbar .nav-link.active {
          background: rgba(255, 255, 255, 0.2);
          color: white !important;
        }

        /* Badges */
        .badge {
          font-weight: 500;
          padding: 0.35em 0.65em;
          border-radius: 50px;
        }

        .badge-primary {
          background-color: var(--primary);
        }

        .badge-success {
          background-color: var(--success);
        }

        .badge-warning {
          background-color: var(--warning);
        }

        .badge-danger {
          background-color: var(--danger);
        }

        .badge-info {
          background-color: var(--info);
        }

        /* Tables */
        .material-table {
          background: white;
          border-radius: var(--border-radius-lg);
          overflow: hidden;
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .material-table table {
          margin: 0;
        }

        .material-table thead th {
          background: linear-gradient(135deg, var(--primary), var(--secondary));
          color: white;
          border: none;
          padding: 1rem;
          font-weight: 600;
          text-transform: uppercase;
          font-size: 0.875rem;
          letter-spacing: 0.5px;
        }

        .material-table tbody td {
          padding: 1rem;
          border-color: rgba(0, 0, 0, 0.05);
          vertical-align: middle;
        }

        .material-table tbody tr {
          transition: var(--transition);
        }

        .material-table tbody tr:hover {
          background: rgba(30, 132, 73, 0.04);
        }

        /* Stats Cards */
        .stat-card {
          background: linear-gradient(135deg, var(--white), #f8fafc);
          border-radius: var(--border-radius-lg);
          padding: 1.5rem;
          border-left: 4px solid var(--primary);
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
          transition: var(--transition);
        }

        .stat-card:hover {
          transform: translateY(-2px);
          box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card .stat-icon {
          width: 60px;
          height: 60px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 1.5rem;
          margin-bottom: 1rem;
        }

        .stat-card.primary .stat-icon {
          background: rgba(30, 132, 73, 0.1);
          color: var(--primary);
        }

        .stat-card.success .stat-icon {
          background: rgba(39, 174, 96, 0.1);
          color: var(--success);
        }

        .stat-card.warning .stat-icon {
          background: rgba(243, 156, 18, 0.1);
          color: var(--warning);
        }

        .stat-card.info .stat-icon {
          background: rgba(46, 204, 113, 0.1);
          color: var(--info);
        }

        .stat-card .stat-value {
          font-size: 2rem;
          font-weight: 700;
          color: var(--dark);
          margin-bottom: 0.25rem;
        }

        .stat-card .stat-label {
          color: var(--muted);
          font-weight: 500;
          text-transform: uppercase;
          font-size: 0.875rem;
          letter-spacing: 0.5px;
        }

        /* Forms */
        .material-form .form-control {
          border: 2px solid #e2e8f0;
          border-radius: var(--border-radius);
          padding: 0.75rem 1rem;
          transition: var(--transition);
          background: white;
        }

        .material-form .form-control:focus {
          border-color: var(--primary);
          box-shadow: 0 0 0 3px rgba(30, 132, 73, 0.1);
        }

        .material-form .form-label {
          font-weight: 600;
          color: var(--dark);
          margin-bottom: 0.5rem;
        }

        /* Alerts */
        .alert {
          border: none;
          border-radius: var(--border-radius);
          padding: 1rem 1.5rem;
          border-left: 4px solid;
        }

        .alert-success {
          background: rgba(39, 174, 96, 0.1);
          color: var(--success);
          border-left-color: var(--success);
        }

        .alert-danger {
          background: rgba(231, 76, 60, 0.1);
          color: var(--danger);
          border-left-color: var(--danger);
        }

        .alert-warning {
          background: rgba(243, 156, 18, 0.1);
          color: var(--warning);
          border-left-color: var(--warning);
        }

        .alert-info {
          background: rgba(46, 204, 113, 0.1);
          color: var(--info);
          border-left-color: var(--info);
        }

        /* Modals */
        .modal-content {
          border: none;
          border-radius: var(--border-radius-lg);
          box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
          background: linear-gradient(135deg, var(--primary), var(--secondary));
          color: white;
          border-bottom: none;
          border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
          padding: 1.5rem;
        }

        .modal-header .modal-title {
          font-weight: 600;
          color: white;
        }

        .modal-header .btn-close {
          filter: invert(1);
        }

        /* Utility Classes */
        .rounded-4 { border-radius: var(--border-radius-lg); }
        .rounded-5 { border-radius: var(--border-radius-xl); }
        .shadow-sm { box-shadow: var(--shadow-sm); }
        .shadow { box-shadow: var(--shadow); }
        .shadow-lg { box-shadow: var(--shadow-lg); }

        .text-primary { color: var(--primary) !important; }
        .text-success { color: var(--success) !important; }
        .text-warning { color: var(--warning) !important; }
        .text-danger { color: var(--danger) !important; }
        .text-info { color: var(--info) !important; }

        .bg-primary { background-color: var(--primary) !important; }
        .bg-success { background-color: var(--success) !important; }
        .bg-warning { background-color: var(--warning) !important; }
        .bg-danger { background-color: var(--danger) !important; }
        .bg-info { background-color: var(--info) !important; }

        /* Loading Spinner */
        .loading-spinner {
          width: 40px;
          height: 40px;
          border: 4px solid #f3f3f3;
          border-top: 4px solid var(--primary);
          border-radius: 50%;
          animation: spin 1s linear infinite;
          margin: 2rem auto;
        }

        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
          .material-card .card-body {
            padding: 1rem;
          }
          
          .stat-card {
            padding: 1rem;
          }
          
          .stat-card .stat-value {
            font-size: 1.5rem;
          }
        }
        ' !!}
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg material-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ auth()->check() ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('murid.dashboard')) : route('login') }}">
                <i class="bi bi-wallet2 me-2"></i>SPP App
            </a>

            @auth
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                @if(auth()->user()->isAdmin())
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.murid.*') ? 'active' : '' }}" href="{{ route('admin.murid.index') }}">
                            <i class="bi bi-people me-1"></i>Murid
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.tagihan.*') ? 'active' : '' }}" href="{{ route('admin.tagihan.index') }}">
                            <i class="bi bi-receipt me-1"></i>Tagihan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.pembayaran.*') ? 'active' : '' }}" href="{{ route('admin.pembayaran.index') }}">
                            <i class="bi bi-credit-card me-1"></i>Pembayaran
                            @php
                                $pendingCount = $pembayaranPendingCount ?? \App\Models\Pembayaran::where('status', 'pending')->count();
                            @endphp
                            @if($pendingCount > 0)
                            <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.pengeluaran.*') ? 'active' : '' }}" href="{{ route('admin.pengeluaran.index') }}">
                            <i class="bi bi-cash-coin me-1"></i>Pengeluaran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.spp-setting') ? 'active' : '' }}" href="{{ route('admin.spp-setting') }}">
                            <i class="bi bi-gear me-1"></i>Setting
                        </a>
                    </li>
                </ul>
                @else
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('murid.dashboard') ? 'active' : '' }}" href="{{ route('murid.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('murid.tagihan.*') ? 'active' : '' }}" href="{{ route('murid.tagihan.index') }}">
                            <i class="bi bi-receipt me-1"></i>Tagihan Saya
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('murid.pembayaran.*') ? 'active' : '' }}" href="{{ route('murid.pembayaran.history') }}">
                            <i class="bi bi-clock-history me-1"></i>Riwayat
                        </a>
                    </li>
                </ul>
                @endif

                <!-- User Menu -->
                <ul class="navbar-nav ms-auto">
                  <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                          @if(auth()->user()->foto)
                              <img src="{{ asset('storage/' . auth()->user()->foto) }}" 
                                  alt="Foto Profil"
                                  class="me-2"
                                  style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #dee2e6;">
                          @else
                              <div class="d-flex align-items-center justify-content-center me-2" 
                                  style="width: 32px; height: 32px; border-radius: 50%; background-color: #f8f9fa; overflow: hidden; border: 1px solid #dee2e6;">
                                  <i class="bi bi-person text-primary"></i>
                              </div>
                          @endif
                          <span>{{ auth()->user()->nama }}</span>
                      </a>
                      <ul class="dropdown-menu dropdown-menu-end">
                          <li>
                              @if(auth()->user()->isAdmin())
                                  <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                      <i class="bi bi-person me-2"></i>Profile Saya
                                  </a>
                              @else
                                  <a class="dropdown-item" href="{{ route('murid.profile') }}">
                                      <i class="bi bi-person me-2"></i>Profile Saya
                                  </a>
                              @endif
                          </li>
                          <li><hr class="dropdown-divider"></li>
                          <li>
                              <a class="dropdown-item text-danger" href="{{ route('logout') }}" 
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                  <i class="bi bi-box-arrow-right me-2"></i>Logout
                              </a>
                              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                  @csrf
                              </form>
                          </li>
                      </ul>
                  </li>
              </ul>
            </div>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Material Design Ripple Effect
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = button.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        border-radius: 50%;
                        background: rgba(255, 255, 255, 0.6);
                        transform: scale(0);
                        animation: ripple 600ms linear;
                        pointer-events: none;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                    `;
                    
                    button.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        });

        // Add ripple animation
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
    
    @stack('scripts')
</body>
</html>