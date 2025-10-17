<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'tagihan_id', // Bisa null
        'user_id',
        'metode',
        'bukti',
        'jumlah',
        'status',
        'keterangan',
        'jenis_bayar',
        'tanggal_proses',
        'admin_id'
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
        'tanggal_proses' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'jumlah' => 'decimal:2'
    ];

    // Relasi - tagihan bisa null
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Scope untuk pembayaran yang diterima
    public function scopeDiterima($query)
    {
        return $query->where('status', 'accepted');
    }

    // Scope untuk pembayaran tanpa tagihan (fleksibel)
    public function scopeTanpaTagihan($query)
    {
        return $query->whereNull('tagihan_id');
    }
}