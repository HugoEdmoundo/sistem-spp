<?php
// app/Models/Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'related_type',
        'related_id'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi polymorphic ke model terkait
    public function related()
    {
        return $this->morphTo();
    }

    // Scope untuk notifikasi belum dibaca
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // Scope untuk notifikasi berdasarkan user
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Method untuk menandai sebagai sudah dibaca
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
        return $this;
    }

    // Method untuk menandai sebagai belum dibaca
    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
        return $this;
    }

    // Accessor untuk cek status baca
    public function getIsReadAttribute()
    {
        return !is_null($this->read_at);
    }

    public function getIsUnreadAttribute()
    {
        return is_null($this->read_at);
    }
}