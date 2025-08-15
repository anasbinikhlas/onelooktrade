<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WhaleService
{
    public function latest(): array
    {
        return Cache::remember('whales_latest', 60, function () {
            $apiKey = config('services.whalealert.key'); // set in .env if you have
            try {
                if ($apiKey) {
                    // Example Whale Alert API call (adjust as needed)
                    // https://docs.whale-alert.io/
                    $url = 'https://api.whale-alert.io/v1/transactions?api_key=' . $apiKey . '&min_value=500000&currency=btc';
                    $res = Http::timeout(8)->get($url);
                    if ($res->failed()) throw new \RuntimeException('whalealert failed');

                    $txs = $res->json()['transactions'] ?? [];
                    $out = [];
                    foreach ($txs as $t) {
                        $out[] = [
                            'wallet' => substr(($t['from']['address'] ?? 'unknown'), 0, 6) . '...' . substr(($t['to']['address'] ?? 'unknown'), -4),
                            'token'  => strtoupper($t['symbol'] ?? 'BTC'),
                            'amount' => (float)($t['amount'] ?? 0),
                            'type'   => (($t['from']['owner_type'] ?? '') === 'exchange') ? 'outflow' : 'inflow',
                            'time'   => isset($t['timestamp']) ? date('H:i', $t['timestamp']) : '',
                        ];
                        if (count($out) >= 10) break;
                    }
                    return $out;
                }

                // fallback dummy (matches your table)
                return [
                    ['wallet'=>'0xAb12...89Cd','token'=>'BTC','amount'=>500,'type'=>'inflow','time'=>'5 mins ago'],
                    ['wallet'=>'0xEf34...67Gh','token'=>'ETH','amount'=>1200,'type'=>'outflow','time'=>'15 mins ago'],
                    ['wallet'=>'0x12cd...A1b2','token'=>'USDT','amount'=>2500000,'type'=>'inflow','time'=>'30 mins ago'],
                ];
            } catch (\Throwable $e) {
                return [
                    ['wallet'=>'0xError...0000','token'=>'BTC','amount'=>0,'type'=>'inflow','time'=>'now'],
                ];
            }
        });
    }
}
