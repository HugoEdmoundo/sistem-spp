<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';
    
    protected $fillable = [
        'kategori',
        'keterangan',
        'jumlah',
        'tanggal',
        'admin_id'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}