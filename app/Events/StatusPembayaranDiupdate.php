<?php
namespace App\Events;

use App\Models\Pembayaran;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatusPembayaranDiupdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pembayaran;

    public function __construct(Pembayaran $pembayaran)
    {
        $this->pembayaran = $pembayaran;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->pembayaran->user_id);
    }

    public function broadcastWith()
    {
        $status = $this->pembayaran->status == 'accepted' ? 'diterima' : 'ditolak';
        
        return [
            'id' => $this->pembayaran->id,
            'message' => "Pembayaran Anda untuk {$this->pembayaran->tagihan->keterangan} telah {$status}",
            'status' => $this->pembayaran->status,
            'time' => now()->diffForHumans(),
            'url' => route('murid.pembayaran.history')
        ];
    }

    public function broadcastAs()
    {
        return 'status.pembayaran.diupdate';
    }
}