<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CryptoService
{
    /**
     * Return market overview (BTC, ETH + a few alts) from CoinGecko.
     * Falls back to dummy data if API fails.
     */
    public function getMarketOverview(): array
    {
        return Cache::remember('market_overview', 60, function () {
            try {
                // CoinGecko free endpoint (no API key)
                $ids = 'bitcoin,ethereum,solana,cardano,ripple';
                $vs  = 'usd';
                $url = "https://api.coingecko.com/api/v3/simple/price?ids={$ids}&vs_currencies={$vs}&include_24hr_change=true";

                $res = Http::timeout(8)->get($url);
                if ($res->failed()) {
                    throw new \RuntimeException('coingecko failed');
                }
                $d = $res->json();

                return [
                    'btc' => ['price' => $d['bitcoin']['usd'] ?? null, 'change' => $d['bitcoin']['usd_24h_change'] ?? null],
                    'eth' => ['price' => $d['ethereum']['usd'] ?? null, 'change' => $d['ethereum']['usd_24h_change'] ?? null],
                    'top' => [
                        ['symbol' => 'SOL', 'price' => $d['solana']['usd'] ?? null, 'change' => $d['solana']['usd_24h_change'] ?? null],
                        ['symbol' => 'ADA', 'price' => $d['cardano']['usd'] ?? null, 'change' => $d['cardano']['usd_24h_change'] ?? null],
                        ['symbol' => 'XRP', 'price' => $d['ripple']['usd'] ?? null, 'change' => $d['ripple']['usd_24h_change'] ?? null],
                    ],
                ];
            } catch (\Throwable $e) {
                // graceful fallback (same shape as dummy endpoints)
                return [
                    'btc' => ['price' => 65430, 'change' => 2.5],
                    'eth' => ['price' => 3240, 'change' => -1.2],
                    'top' => [
                        ['symbol'=>'SOL','price'=>120,'change'=>5.2],
                        ['symbol'=>'ADA','price'=>0.52,'change'=>3.1],
                        ['symbol'=>'XRP','price'=>0.92,'change'=>-2.4],
                    ]
                ];
            }
        });
    }

    /**
     * Get a single symbol price (used by alerts).
     */
    public function getPrice(string $symbol): ?float
    {
        $map = [
            'BTC' => 'bitcoin',
            'ETH' => 'ethereum',
            'SOL' => 'solana',
            'ADA' => 'cardano',
            'XRP' => 'ripple',
        ];
        $id = $map[strtoupper($symbol)] ?? null;
        if (!$id) return null;

        return Cache::remember("price_{$symbol}", 45, function () use ($id) {
            try {
                $url = "https://api.coingecko.com/api/v3/simple/price?ids={$id}&vs_currencies=usd";
                $res = Http::timeout(8)->get($url);
                return $res->json()[$id]['usd'] ?? null;
            } catch (\Throwable $e) {
                return null;
            }
        });
    }
}
