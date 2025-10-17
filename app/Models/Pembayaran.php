<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'tagihan_id',
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

    // Nilai default untuk atribut
    protected $attributes = [
        'status' => 'pending'
    ];

    // Relasi
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

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDiterima($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeTanpaTagihan($query)
    {
        return $query->whereNull('tagihan_id');
    }

    public function scopeDenganTagihan($query)
    {
        return $query->whereNotNull('tagihan_id');
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Menunggu',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak'
        ][$this->status] ?? $this->status;
    }

    public function getJumlahFormattedAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    // Methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isAccepted()
    {
        return $this->status === 'accepted';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}