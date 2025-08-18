<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'symbol',
        'condition',
        'threshold',
        'channel',
        'active',
        'last_triggered_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'last_triggered_at' => 'datetime',
        'threshold' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
