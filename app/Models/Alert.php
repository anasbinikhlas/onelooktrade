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
    'one_shot' => 'boolean',
    'target_price' => 'decimal:8',
    'last_price' => 'decimal:8',
    'triggered_at' => 'datetime',
];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
