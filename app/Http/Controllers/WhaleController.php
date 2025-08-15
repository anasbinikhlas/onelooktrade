<?php

namespace App\Http\Controllers;

use App\Services\WhaleService;

class WhaleController extends Controller
{
    public function __construct(private WhaleService $whales) {}

    public function getWhaleMoves()
    {
        return response()->json($this->whales->latest());
    }
}
