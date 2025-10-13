<?php
// app/Models/SppSetting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'nominal',
        'berlaku_mulai'
    ];
}