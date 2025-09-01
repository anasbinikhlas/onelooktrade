<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MarketController extends Controller
{
    /**
     * Fetch crypto market data
     */
    public function index()
    {
        try {
            // Example using CoinGecko free API
            $response = Http::get('https://api.coingecko.com/api/v3/coins/markets', [
                'vs_currency' => 'usd',
                'order' => 'market_cap_desc',
                'per_page' => 10,
                'page' => 1,
                'sparkline' => false,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return view('markets.index', ['markets' => $data]);
            }

            return view('markets.index', ['markets' => []])
                ->withErrors(['error' => 'Failed to fetch market data.']);
        } catch (\Exception $e) {
            return view('markets.index', ['markets' => []])
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
