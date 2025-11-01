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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    {{-- Icon Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
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
          --font-primary: "Poppins", sans-serif;
          --font-secondary: "Montserrat", sans-serif;
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

        /* ================ LAYOUT ================ */
        .app-container {
          display: flex;
          min-height: 100vh;
        }

        /* ================ SIDEBAR ================ */
        .sidebar {
          width: 280px;
          background: linear-gradient(180deg, var(--secondary), var(--primary));
          color: white;
          transition: var(--transition);
          position: fixed;
          height: 100vh;
          overflow-y: auto;
          z-index: 1000;
          box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
          transform: translateX(0);
          display: flex;
          flex-direction: column;
        }

        .sidebar-header {
          padding: 1.5rem 1.25rem;
          border-bottom: 1px solid rgba(255, 255, 255, 0.1);
          display: flex;
          align-items: center;
          justify-content: space-between;
          position: relative;
        }

        .sidebar-header .brand {
          font-weight: 700;
          font-size: 1.5rem;
          color: white;
          display: flex;
          align-items: center;
        }

        .sidebar-header .brand i {
          margin-right: 0.5rem;
          font-size: 1.8rem;
          color: var(--accent);
        }

        .sidebar-toggle {
          background: none;
          border: none;
          color: white;
          font-size: 1.25rem;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          width: 40px;
          height: 40px;
          border-radius: 50%;
          transition: var(--transition);
        }

        .sidebar-toggle:hover {
          background: rgba(255, 255, 255, 0.1);
          transform: scale(1.1);
        }

        .sidebar-nav {
          padding: 1rem 0;
          flex: 1;
          overflow-y: auto;
        }

        .nav-section {
          margin-bottom: 1.5rem;
        }

        .nav-section-title {
          font-size: 0.75rem;
          text-transform: uppercase;
          letter-spacing: 1px;
          padding: 0 1.25rem;
          margin-bottom: 0.5rem;
          color: rgba(255, 255, 255, 0.6);
          font-weight: 600;
        }

        .nav-item {
          margin-bottom: 0.25rem;
          position: relative;
        }

        .nav-link {
          display: flex;
          align-items: center;
          padding: 0.75rem 1.25rem;
          color: rgba(255, 255, 255, 0.85);
          transition: var(--transition);
          border-left: 3px solid transparent;
          position: relative;
          overflow: hidden;
        }

        .nav-link::before {
          content: "";
          position: absolute;
          top: 0;
          left: -100%;
          width: 100%;
          height: 100%;
          background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
          transition: var(--transition);
        }

        .nav-link:hover::before {
          left: 100%;
        }

        .nav-link:hover {
          background: rgba(255, 255, 255, 0.1);
          color: white;
          border-left-color: rgba(255, 255, 255, 0.3);
          transform: translateX(5px);
        }

        .nav-link.active {
          background: rgba(255, 255, 255, 0.15);
          color: white;
          border-left-color: var(--accent);
          box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .nav-link i {
          margin-right: 0.75rem;
          font-size: 1.1rem;
          width: 20px;
          text-align: center;
          transition: var(--transition);
        }

        .nav-link:hover i {
          transform: scale(1.2);
          color: var(--accent);
        }

        .nav-badge {
          margin-left: auto;
          background: var(--danger);
          color: white;
          border-radius: 50px;
          padding: 0.25rem 0.5rem;
          font-size: 0.75rem;
          font-weight: 600;
          animation: pulse 2s infinite;
        }

        @keyframes pulse {
          0% {
            transform: scale(1);
          }
          50% {
            transform: scale(1.1);
          }
          100% {
            transform: scale(1);
          }
        }

        .sidebar-footer {
          padding: 1.25rem;
          border-top: 1px solid rgba(255, 255, 255, 0.1);
          margin-top: auto;
        }

        .user-profile {
          display: flex;
          align-items: center;
          position: relative;
        }

        .user-avatar {
          width: 45px;
          height: 45px;
          border-radius: 50%;
          object-fit: cover;
          margin-right: 0.75rem;
          border: 2px solid rgba(255, 255, 255, 0.3);
          transition: var(--transition);
          box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .user-avatar:hover {
          transform: scale(1.1);
          border-color: var(--accent);
        }

        .user-info {
          flex: 1;
        }

        .user-name {
          font-weight: 600;
          font-size: 0.9rem;
          margin-bottom: 0.1rem;
        }

        .user-role {
          font-size: 0.75rem;
          color: rgba(255, 255, 255, 0.7);
        }

        .logout-btn {
          background: rgba(231, 76, 60, 0.2);
          border: 1px solid rgba(231, 76, 60, 0.3);
          color: rgba(255, 255, 255, 0.9);
          padding: 0.5rem 1rem;
          border-radius: 25px;
          font-size: 0.875rem;
          font-weight: 500;
          transition: var(--transition);
          display: flex;
          align-items: center;
          gap: 0.5rem;
          cursor: pointer;
          width: 100%;
          justify-content: center;
          margin-top: 1rem;
        }

        .logout-btn:hover {
          background: rgba(231, 76, 60, 0.3);
          border-color: rgba(231, 76, 60, 0.5);
          color: white;
          transform: translateY(-2px);
          box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
        }

        .logout-btn i {
          font-size: 1rem;
        }

        /* ================ MAIN CONTENT ================ */
        .main-content {
          flex: 1;
          margin-left: 280px;
          transition: var(--transition);
          min-height: 100vh;
          display: flex;
          flex-direction: column;
          width: calc(100% - 280px);
        }

        .top-navbar {
          background: white;
          box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
          padding: 1rem 1.5rem;
          display: flex;
          justify-content: space-between;
          align-items: center;
          position: sticky;
          top: 0;
          z-index: 100;
        }

        .page-title h1 {
          margin: 0;
          font-size: 1.5rem;
          font-weight: 600;
          color: var(--dark);
        }

        .page-title .breadcrumb {
          margin: 0;
          padding: 0;
          list-style: none;
          display: flex;
          font-size: 0.875rem;
          color: var(--muted);
        }

        .page-title .breadcrumb li:not(:last-child)::after {
          content: "/";
          margin: 0 0.5rem;
        }

        .top-nav-actions {
          display: flex;
          align-items: center;
          gap: 1rem;
        }

        .content-wrapper {
          flex: 1;
          padding: 1.5rem;
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

        /* Mobile Toggle Button */
        .mobile-toggle {
          display: none;
          background: var(--primary);
          color: white;
          border: none;
          border-radius: 5px;
          padding: 0.5rem 0.75rem;
          font-size: 1.25rem;
          cursor: pointer;
          margin-right: 1rem;
          transition: var(--transition);
        }

        .mobile-toggle:hover {
          background: var(--secondary);
          transform: scale(1.05);
        }

        /* Sidebar Toggle Button */
        .sidebar-toggle {
          background: none;
          border: none;
          color: white;
          font-size: 1.25rem;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          width: 40px;
          height: 40px;
          border-radius: 50%;
          transition: var(--transition);
        }

        .sidebar-toggle:hover {
          background: rgba(255, 255, 255, 0.1);
          transform: scale(1.1);
        }

        /* Responsive */
        @media (max-width: 1200px) {
          .sidebar {
            width: 70px;
            overflow: visible;
          }
          
          .sidebar-header .brand span,
          .nav-section-title,
          .nav-link span,
          .nav-badge,
          .user-info,
          .logout-btn span {
            display: none;
          }
          
          .sidebar-header {
            justify-content: center;
            padding: 1rem;
          }
          
          .sidebar-toggle {
            display: block;
            position: absolute;
            right: -40px;
            top: 15px;
            background: var(--primary);
            width: 40px;
            height: 40px;
            border-radius: 0 5px 5px 0;
            display: flex;
            align-items: center;
            justify-content: center;
          }
          
          .nav-link {
            justify-content: center;
            padding: 0.75rem;
            border-left: none;
            border-right: 3px solid transparent;
          }
          
          .nav-link.active {
            border-left: none;
            border-right-color: var(--accent);
          }
          
          .nav-link i {
            margin-right: 0;
            font-size: 1.25rem;
          }
          
          .sidebar-footer {
            padding: 1rem;
          }
          
          .user-profile {
            justify-content: center;
          }
          
          .user-avatar {
            margin-right: 0;
            width: 36px;
            height: 36px;
          }
          
          .logout-btn {
            justify-content: center;
            padding: 0.5rem;
            width: auto;
          }
          
          .main-content {
            margin-left: 70px;
            width: calc(100% - 70px);
          }
          
          .sidebar.expanded {
            width: 280px;
            z-index: 1100;
          }
          
          .sidebar.expanded .brand span,
          .sidebar.expanded .nav-section-title,
          .sidebar.expanded .nav-link span,
          .sidebar.expanded .nav-badge,
          .sidebar.expanded .user-info,
          .sidebar.expanded .logout-btn span {
            display: block;
          }
          
          .sidebar.expanded .nav-link {
            justify-content: flex-start;
            padding: 0.75rem 1.25rem;
            border-right: none;
            border-left: 3px solid transparent;
          }
          
          .sidebar.expanded .nav-link.active {
            border-right: none;
            border-left-color: var(--accent);
          }
          
          .sidebar.expanded .nav-link i {
            margin-right: 0.75rem;
          }
          
          .sidebar.expanded .sidebar-header {
            justify-content: space-between;
            padding: 1.5rem 1.25rem;
          }
          
          .sidebar.expanded .user-profile {
            justify-content: flex-start;
          }
          
          .sidebar.expanded .user-avatar {
            margin-right: 0.75rem;
          }
          
          .sidebar.expanded .logout-btn {
            justify-content: flex-start;
            padding: 0.5rem 1rem;
            width: 100%;
          }
          
          .main-content.sidebar-expanded {
            margin-left: 280px;
            width: calc(100% - 280px);
          }
        }

        @media (max-width: 768px) {
          .mobile-toggle {
            display: block;
          }
          
          .sidebar {
            width: 280px;
            transform: translateX(-100%);
            z-index: 1100;
          }
          
          .sidebar.expanded {
            transform: translateX(0);
          }
          
          .main-content {
            margin-left: 0;
            width: 100%;
          }
          
          .material-card .card-body {
            padding: 1rem;
          }
          
          .stat-card {
            padding: 1rem;
          }
          
          .stat-card .stat-value {
            font-size: 1.5rem;
          }
          
          .content-wrapper {
            padding: 1rem;
          }
          
          .top-navbar {
            padding: 1rem;
          }
          
          /* Overlay untuk mobile */
          .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            display: none;
          }
          
          .sidebar-overlay.active {
            display: block;
          }
        }
        ' !!}
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- App Container -->
    <div class="app-container">
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ auth()->check() ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('murid.dashboard')) : route('login') }}" class="brand">
                    <i class="bi bi-wallet2"></i>
                    <span>SPP App</span>
                </a>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-chevron-left"></i>
                </button>
            </div>

            @auth
            <nav class="sidebar-nav">
              @if(auth()->user()->isAdmin())
              <div class="nav-section">
                  <div class="nav-section-title">Menu Utama</div>
                  <div class="nav-item">
                      <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                          <i class="bi bi-speedometer2"></i>
                          <span>Dashboard</span>
                      </a>
                  </div>
                  <div class="nav-item">
                      <a class="nav-link {{ request()->routeIs('admin.murid.*') ? 'active' : '' }}" href="{{ route('admin.murid.index') }}">
                          <i class="bi bi-people"></i>
                          <span>Murid</span>
                      </a>
                  </div>
                  <div class="nav-item">
                      <a class="nav-link {{ request()->routeIs('admin.tagihan.*') ? 'active' : '' }}" href="{{ route('admin.tagihan.index') }}">
                          <i class="bi bi-receipt"></i>
                          <span>Tagihan</span>
                      </a>
                  </div>
                  
                  <!-- Menu Pembayaran (Verifikasi) -->
                  <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.pembayaran.index') || request()->routeIs('admin.pembayaran.show') ? 'active' : '' }}" 
                      href="{{ route('admin.pembayaran.index') }}">
                        <i class="bi bi-credit-card"></i>
                        <span>Verifikasi Pembayaran</span>
                        @php
                            $pendingCount = $pembayaranPendingCount ?? \App\Models\Pembayaran::where('status', 'pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                        <span class="nav-badge">{{ $pendingCount }}</span>
                        @endif
                    </a>
                  </div>

                  
                  <!-- Menu Pembayaran Manual -->
                  <div class="nav-item">
                      <a class="nav-link {{ request()->routeIs('admin.pembayaran.manual.*') ? 'active' : '' }}" href="{{ route('admin.pembayaran.manual.create') }}">
                          <i class="bi bi-cash-coin"></i>
                          <span>Pembayaran Manual</span>
                      </a>
                  </div>
             
                  <div class="nav-item">
                      <a class="nav-link {{ request()->routeIs('admin.pengeluaran.*') ? 'active' : '' }}" href="{{ route('admin.pengeluaran.index') }}">
                          <i class="bi bi-cash-coin"></i>
                          <span>Pengeluaran</span>
                      </a>
                  </div>

                 <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.pembayaran.history') ? 'active' : '' }}" 
                      href="{{ route('admin.pembayaran.history') }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Riwayat Pembayaran</span>
                    </a>
                  </div>



                  {{-- resources/views/layouts/admin/sidebar.blade.php --}}
                  <div class="nav-item">
                      <a class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}" href="{{ route('admin.laporan.index') }}">
                          <i class="bi bi-bar-chart"></i>
                          <span>Laporan</span>
                      </a>
                  </div>
              </div>
              
              <div class="nav-section">
                  <div class="nav-section-title">Lainnya</div>
                  <div class="nav-item">
                      <a class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}" href="{{ route('admin.profile') }}">
                          <i class="bi bi-person"></i>
                          <span>Profile Saya</span>
                      </a>
                  </div>
                  <div class="nav-item">
                      <a class="nav-link {{ request()->routeIs('admin.spp-setting') ? 'active' : '' }}" href="{{ route('admin.spp-setting') }}">
                          <i class="bi bi-gear"></i>
                          <span>Pengaturan</span>
                      </a>
                  </div>
              </div>
              @else
              <div class="nav-section">
                  <div class="nav-section-title">Menu Murid</div>
                  <div class="nav-item">
                      <a class="nav-link {{ request()->routeIs('murid.dashboard') ? 'active' : '' }}" href="{{ route('murid.dashboard') }}">
                          <i class="bi bi-speedometer2"></i>
                          <span>Dashboard</span>
                      </a>
                  </div>
                  <div class="nav-item">
                      <a class="nav-link {{ request()->routeIs('murid.tagihan.*') ? 'active' : '' }}" href="{{ route('murid.tagihan.index') }}">
                          <i class="bi bi-receipt"></i>
                          <span>Tagihan Saya</span>
                      </a>
                  </div>
                  <!-- TAMBAH MENU BAYAR SPP -->
                  <div class="nav-item">
                      <a class="nav-link {{ request()->routeIs('murid.bayar.spp.page') ? 'active' : '' }}" href="{{ route('murid.bayar.spp.page') }}">
                          <i class="bi bi-credit-card"></i>
                          <span>Bayar SPP</span>
                      </a>
                  </div>
                  <div class="nav-item">
                      <a class="nav-link {{ request()->routeIs('murid.pembayaran.*') ? 'active' : '' }}" href="{{ route('murid.pembayaran.history') }}">
                          <i class="bi bi-clock-history"></i>
                          <span>Riwayat</span>
                      </a>
                  </div>
              </div>

              <div class="nav-section">
                  <div class="nav-section-title">Lainnya</div>
                  <div class="nav-item">
                      <a class="nav-link {{ request()->routeIs('murid.profile') ? 'active' : '' }}" href="{{ route('murid.profile') }}">
                          <i class="bi bi-person"></i>
                          <span>Profile Saya</span>
                      </a>
                  </div>
              </div>
              @endif
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile">
                    @if(auth()->user()->foto)
                        <img src="{{ asset('storage/' . auth()->user()->foto) }}" 
                            alt="Foto Profil"
                            class="user-avatar">
                    @else
                        <div class="user-avatar d-flex align-items-center justify-content-center" 
                            style="background-color: rgba(255, 255, 255, 0.2);">
                            <i class="bi bi-person text-white"></i>
                        </div>
                    @endif
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->nama }}</div>
                        <div class="user-role">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Murid' }}</div>
                    </div>
                </div>
                
                <!-- Tombol Logout Langsung -->
                <button class="logout-btn" onclick="document.getElementById('logout-form').submit()">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </div>
            @endauth
        </aside>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            {{-- @auth
            <div class="top-navbar">
                <button class="mobile-toggle" id="mobileToggle">
                    <i class="bi bi-list"></i>
                </button>
                <div class="page-title">
                    <h1>@yield('title', 'Dashboard')</h1>
                </div>
            </div>
            @endauth --}}

            <div class="content-wrapper">
                {{-- @if(session('success'))
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
                @endif --}}

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileToggle = document.getElementById('mobileToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            // Function to toggle sidebar
            function toggleSidebar() {
                sidebar.classList.toggle('expanded');
                mainContent.classList.toggle('sidebar-expanded');
                
                // For mobile, also toggle overlay
                if (window.innerWidth <= 768) {
                    sidebarOverlay.classList.toggle('active');
                }
                
                // Update toggle button icon
                const icon = sidebarToggle.querySelector('i');
                if (sidebar.classList.contains('expanded')) {
                    icon.classList.remove('bi-chevron-left');
                    icon.classList.add('bi-chevron-right');
                } else {
                    icon.classList.remove('bi-chevron-right');
                    icon.classList.add('bi-chevron-left');
                }
            }
            
            // Desktop toggle
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }
            
            // Mobile toggle
            if (mobileToggle) {
                mobileToggle.addEventListener('click', toggleSidebar);
            }
            
            // Close sidebar when clicking on overlay (mobile)
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('expanded');
                        mainContent.classList.remove('sidebar-expanded');
                        sidebarOverlay.classList.remove('active');
                        
                        // Update toggle button icon
                        const icon = sidebarToggle.querySelector('i');
                        icon.classList.remove('bi-chevron-right');
                        icon.classList.add('bi-chevron-left');
                    }
                });
            }
            
            // Close sidebar when clicking on a nav link (mobile)
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('expanded');
                        mainContent.classList.remove('sidebar-expanded');
                        sidebarOverlay.classList.remove('active');
                        
                        // Update toggle button icon
                        const icon = sidebarToggle.querySelector('i');
                        icon.classList.remove('bi-chevron-right');
                        icon.classList.add('bi-chevron-left');
                    }
                });
            });
            
            // Material Design Ripple Effect
            const buttons = document.querySelectorAll('.btn, .logout-btn');
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
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebarOverlay.classList.remove('active');
                }
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
    
    <!-- Hidden logout form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
    
    @stack('scripts')
</body>
</html>