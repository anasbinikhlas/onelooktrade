<?php

namespace App\Http\Controllers;

use App\Services\CryptoService;

class MarketController extends Controller
{
    public function __construct(private CryptoService $crypto) {}

    public function getData()
    {
        return response()->json($this->crypto->getMarketOverview());
    }
}
