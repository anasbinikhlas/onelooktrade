<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NewsService
{
    public function getLatest(): array
    {
        return Cache::remember('news_latest', 120, function () {
            $apiKey = config('services.cryptopanic.key'); // set in .env if you have
            try {
                if ($apiKey) {
                    $url = 'https://cryptopanic.com/api/v1/posts/?kind=news&filter=rising&public=true&auth_token='.$apiKey;
                    $res = Http::timeout(8)->get($url);
                    if ($res->failed()) throw new \RuntimeException('cryptopanic failed');

                    $items = $res->json()['results'] ?? [];
                    $clean = [];
                    foreach ($items as $it) {
                        $clean[] = [
                            'title'  => $it['title'] ?? 'News',
                            'source' => $it['domain'] ?? 'source',
                            'time'   => $it['published_at'] ?? '',
                            'url'    => $it['url'] ?? '#',
                        ];
                        if (count($clean) >= 10) break;
                    }
                    return $clean;
                }
                // fallback: minimal static set
                return [
                    ['title'=>'Bitcoin hits new high','source'=>'CoinDesk','time'=>'1h ago','url'=>'https://coindesk.com/'],
                    ['title'=>'Ethereum upgrade scheduled','source'=>'CryptoNews','time'=>'3h ago','url'=>'https://cryptonews.com/'],
                ];
            } catch (\Throwable $e) {
                return [
                    ['title'=>'Market update unavailable (fallback)','source'=>'System','time'=>now()->toDateTimeString(),'url'=>'#'],
                ];
            }
        });
    }
}
