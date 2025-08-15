<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'user_id','symbol','condition','threshold','channel','active','last_triggered_at'
    ];

    protected $casts = [
        'active' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];
}
