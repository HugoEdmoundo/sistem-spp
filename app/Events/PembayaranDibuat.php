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

class PembayaranDibuat implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pembayaran;

    public function __construct(Pembayaran $pembayaran)
    {
        $this->pembayaran = $pembayaran;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('admin.notifications');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->pembayaran->id,
            'message' => "Murid {$this->pembayaran->user->nama} mengupload bukti pembayaran baru",
            'tagihan' => $this->pembayaran->tagihan->keterangan,
            'jumlah' => number_format($this->pembayaran->jumlah, 0, ',', '.'),
            'time' => $this->pembayaran->tanggal_upload->diffForHumans(),
            'url' => route('admin.pembayaran.index')
        ];
    }

    public function broadcastAs()
    {
        return 'pembayaran.dibuat';
    }
}