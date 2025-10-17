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

class PembayaranManualDibuat implements ShouldBroadcast
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
        return [
            'id' => $this->pembayaran->id,
            'message' => "Admin telah membuat pembayaran manual untuk {$this->pembayaran->keterangan} sebesar Rp " . number_format($this->pembayaran->jumlah, 0, ',', '.'),
            'status' => $this->pembayaran->status,
            'time' => now()->diffForHumans(),
            'url' => route('murid.pembayaran.history')
        ];
    }

    public function broadcastAs()
    {
        return 'pembayaran.manual.dibuat';
    }
}