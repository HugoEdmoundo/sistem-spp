<?php
// app/Models/Pembayaran.php
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
        'tanggal_proses',
        'admin_id'
    ];

    // Casting untuk dates
    protected $casts = [
        'tanggal_upload' => 'datetime',
        'tanggal_proses' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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

    // Accessor untuk memastikan tanggal_upload selalu return Carbon
    public function getTanggalUploadAttribute($value)
    {
        return \Carbon\Carbon::parse($value);
    }

    // Accessor untuk memastikan tanggal_proses selalu return Carbon
    public function getTanggalProsesAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value) : null;
    }
}