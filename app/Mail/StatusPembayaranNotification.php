<?php
namespace App\Mail;

use App\Models\Pembayaran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatusPembayaranNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $pembayaran;

    public function __construct(Pembayaran $pembayaran)
    {
        $this->pembayaran = $pembayaran;
    }

    public function build()
    {
        $status = $this->pembayaran->status == 'accepted' ? 'Diterima' : 'Ditolak';
        
        return $this->subject("Status Pembayaran {$status} - SPP App")
                    ->view('emails.status-pembayaran-notification')
                    ->with([
                        'pembayaran' => $this->pembayaran,
                        'status' => $status,
                    ]);
    }
}