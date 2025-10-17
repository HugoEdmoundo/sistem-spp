@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
                <div>
                    <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-primary me-2">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Laporan
                    </a>
                    <a href="{{ route('admin.backup.index') }}" class="btn btn-outline-info">
                        <i class="bi bi-database me-2"></i>Backup
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards - 3x3 Layout -->
    <div class="row mb-4">
        <!-- Baris Pertama -->
        <div class="col-xl-4 col-md-4 col-6 mb-4">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $totalMurid }}</div>
                    <div class="stat-label">Total Murid</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 col-6 mb-4">
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">Rp {{ number_format($totalTagihanBulanIni, 0, ',', '.') }}</div>
                    <div class="stat-label">Tagihan Bulan Ini</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 col-6 mb-4">
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">Rp {{ number_format($totalAkhir, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Akhir</div>
                </div>
            </div>
        </div>

        <!-- Baris Kedua -->
        <div class="col-xl-4 col-md-4 col-6 mb-4">
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                    <div class="stat-label">Pengeluaran</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 col-6 mb-4">
            <div class="stat-card secondary">
                <div class="stat-icon">
                    <i class="bi bi-arrow-up-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</div>
                    <div class="stat-label">Pemasukan</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 col-6 mb-4">
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $pembayaranPending }}</div>
                    <div class="stat-label">Menunggu Verifikasi</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Statistics -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="material-card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Tagihan Terbaru</h5>
                </div>
                <div class="card-body">
                    @php
                        $recentTagihan = \App\Models\Tagihan::with('user')
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @if($recentTagihan->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentTagihan as $tagihan)
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $tagihan->user->nama }}</h6>
                                    <p class="mb-1 text-muted small">{{ $tagihan->keterangan }}</p>
                                    <small class="text-muted">{{ $tagihan->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</div>
                                    <span class="badge bg-{{ $tagihan->status == 'success' ? 'success' : ($tagihan->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ $tagihan->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="bi bi-receipt"></i>
                        <p class="text-muted mb-0">Belum ada tagihan</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="material-card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Statistik Bulan Ini</h5>
                </div>
                <div class="card-body">
                    @php
                        $currentMonth = now()->month;
                        $currentYear = now()->year;
                        
                        $tagihanBulanIni = \App\Models\Tagihan::whereMonth('created_at', $currentMonth)
                            ->whereYear('created_at', $currentYear)
                            ->count();
                        
                        $pembayaranBulanIni = \App\Models\Pembayaran::whereMonth('tanggal_upload', $currentMonth)
                            ->whereYear('tanggal_upload', $currentYear)
                            ->count();
                        
                        $muridBaru = \App\Models\User::where('role', 'murid')
                            ->whereMonth('created_at', $currentMonth)
                            ->whereYear('created_at', $currentYear)
                            ->count();
                    @endphp
                    
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end pe-3">
                                <div class="h4 text-primary mb-1">{{ $tagihanBulanIni }}</div>
                                <small class="text-muted">Tagihan</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end pe-3">
                                <div class="h4 text-success mb-1">{{ $pembayaranBulanIni }}</div>
                                <small class="text-muted">Pembayaran</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="h4 text-info mb-1">{{ $muridBaru }}</div>
                            <small class="text-muted">Murid Baru</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mt-3">
                        <small class="text-muted d-block mb-2">Progress Pembayaran Bulan Ini:</small>
                        @php
                            $totalTagihan = \App\Models\Tagihan::whereMonth('created_at', $currentMonth)
                                ->whereYear('created_at', $currentYear)
                                ->count();
                            $lunasTagihan = \App\Models\Tagihan::whereMonth('created_at', $currentMonth)
                                ->whereYear('created_at', $currentYear)
                                ->where('status', 'success')
                                ->count();
                            $progress = $totalTagihan > 0 ? ($lunasTagihan / $totalTagihan) * 100 : 0;
                        @endphp
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="width: {{ $progress }}%"></div>
                        </div>
                        <small class="text-muted mt-1 d-block">{{ number_format($progress, 1) }}% lunas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Enhanced Stats Cards - 3x3 Layout */
    .stat-card {
        background: white;
        border-radius: var(--border-radius-lg);
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: var(--transition);
        border-left: 4px solid;
        height: 100%;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 70px;
        height: 70px;
        background: rgba(0, 0, 0, 0.03);
        border-radius: 0 0 0 70px;
        transition: var(--transition);
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stat-card:hover::before {
        width: 90px;
        height: 90px;
    }

    .stat-card.primary {
        border-left-color: var(--primary);
    }

    .stat-card.success {
        border-left-color: var(--success);
    }

    .stat-card.info {
        border-left-color: var(--info);
    }

    .stat-card.warning {
        border-left-color: var(--warning);
    }

    .stat-card.danger {
        border-left-color: var(--danger);
    }

    .stat-card.secondary {
        border-left-color: var(--secondary);
    }

    .stat-icon {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: white;
        flex-shrink: 0;
    }

    .stat-card.primary .stat-icon {
        background: linear-gradient(135deg, var(--primary), #2ECC71);
    }

    .stat-card.success .stat-icon {
        background: linear-gradient(135deg, var(--success), #58D68D);
    }

    .stat-card.info .stat-icon {
        background: linear-gradient(135deg, var(--info), #58D68D);
    }

    .stat-card.warning .stat-icon {
        background: linear-gradient(135deg, var(--warning), #F7DC6F);
    }

    .stat-card.danger .stat-icon {
        background: linear-gradient(135deg, var(--danger), #F1948A);
    }

    .stat-card.secondary .stat-icon {
        background: linear-gradient(135deg, var(--secondary), #1E8449);
    }

    .stat-content {
        flex: 1;
        min-width: 0;
    }

    .stat-value {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.25rem;
        line-height: 1.2;
        word-break: break-word;
        overflow-wrap: break-word;
    }

    .stat-label {
        color: var(--muted);
        font-weight: 500;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1.3;
    }

    /* Activity List */
    .activity-item {
        padding: 1rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        transition: var(--transition);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-item:hover {
        background: rgba(30, 132, 73, 0.02);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--muted);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* Progress Bar */
    .progress {
        height: 8px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar {
        background: linear-gradient(90deg, var(--success), var(--info));
        transition: width 0.6s ease;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .stat-card {
            padding: 1.25rem;
            gap: 0.75rem;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }
        
        .stat-value {
            font-size: 1.4rem;
        }
    }

    @media (max-width: 768px) {
        .stat-card {
            flex-direction: column;
            text-align: center;
            padding: 1.5rem;
            gap: 0.75rem;
        }
        
        .stat-icon {
            width: 55px;
            height: 55px;
            font-size: 1.4rem;
        }
        
        .stat-value {
            font-size: 1.6rem;
        }
        
        .stat-label {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .stat-card {
            padding: 1.25rem;
        }
        
        .stat-value {
            font-size: 1.4rem;
        }
        
        .stat-label {
            font-size: 0.75rem;
        }
    }

    /* For very large screens */
    @media (min-width: 1400px) {
        .stat-card {
            padding: 1.75rem;
        }
        
        .stat-value {
            font-size: 1.8rem;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add animation to stats cards on load
        const statsCards = document.querySelectorAll('.stat-card');
        statsCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
@endsection