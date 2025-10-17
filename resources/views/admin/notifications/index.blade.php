<!-- resources/views/admin/notifications/index.blade.php -->
@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Notifikasi</h4>
    <div>
        <form action="{{ route('admin.notifications.read-all') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-check-all"></i> Tandai Semua Dibaca
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($notifications->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($notifications as $notification)
                <div class="list-group-item {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">{{ $notification->title }}</h6>
                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-1">{{ $notification->message }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">{{ $notification->created_at->format('d/m/Y H:i') }}</small>
                        @if(is_null($notification->read_at))
                        <form action="{{ route('admin.notifications.read', $notification->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success">Tandai Dibaca</button>
                        </form>
                        @else
                        <span class="badge bg-success">Sudah Dibaca</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @if($notifications->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $notifications->links() }}
            </div>
            @endif
        @else
        <div class="text-center py-5">
            <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
            <h5 class="text-muted mt-3">Tidak ada notifikasi</h5>
            <p class="text-muted">Semua notifikasi sudah dibaca.</p>
        </div>
        @endif
    </div>
</div>
@endsection