<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayarans';

    protected $fillable = [
        'tagihan_id',
        'user_id',
        'admin_id',
        'metode',
        'bukti',
        'jumlah',
        'status',
        'alasan_reject',
        'keterangan',
        'jenis_bayar',
        'tanggal_upload',
        'tanggal_bayar',
        'tanggal_proses',
        'catatan_admin',
        'tahun',
        'bulan_mulai',
        'bulan_akhir'
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
        'tanggal_proses' => 'datetime',
        'tanggal_bayar' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'jumlah' => 'decimal:2',
        'tahun' => 'integer',
        'bulan_mulai' => 'integer',
        'bulan_akhir' => 'integer'
    ];

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

    public function getJenisPembayaranAttribute()
    {
        return $this->tagihan_id ? 'Tagihan' : 'SPP Fleksibel';
    }

    public function getAlasanRejectSingkatAttribute()
    {
        if (!$this->alasan_reject) return null;
        
        return strlen($this->alasan_reject) > 50 
            ? substr($this->alasan_reject, 0, 50) . '...'
            : $this->alasan_reject;
    }

    public function getNamaBulan($bulan)
    {
        $bulanArr = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulanArr[$bulan] ?? '';
    }

    public function getRangeBulanAttribute()
    {
        if ($this->bulan_mulai && $this->bulan_akhir) {
            if ($this->bulan_mulai == $this->bulan_akhir) {
                return $this->getNamaBulan($this->bulan_mulai) . ' ' . $this->tahun;
            } else {
                return $this->getNamaBulan($this->bulan_mulai) . ' - ' . $this->getNamaBulan($this->bulan_akhir) . ' ' . $this->tahun;
            }
        }
        return null;
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

    public function dapatDiuploadUlang()
    {
        return $this->isRejected() && $this->alasan_reject;
    }
}