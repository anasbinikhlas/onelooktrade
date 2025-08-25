<?php

namespace App\Http\Controllers;

use App\Services\CryptoService;
use App\Services\WhaleService;
use App\Services\NewsService;
use App\Models\Alert;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private CryptoService $crypto,
        private WhaleService $whales,
        private NewsService $news
    ) {}

    public function index()
    {
        // Everything wrapped in try/catch so the dashboard never hard-fails.
        try {
            $market = $this->crypto->getMarketOverview();
        } catch (\Throwable $e) {
            $market = [
                'btc' => ['price' => null, 'change' => null],
                'eth' => ['price' => null, 'change' => null],
                'top' => [],
            ];
        }

        try {
            $whaleSeries  = $this->whales->movementSeries();   // labels/inflow/outflow
            $whaleLatest  = $this->whales->latestTransfers(5); // latest transfers list
        } catch (\Throwable $e) {
            $whaleSeries = ['labels'=>[], 'inflow'=>[], 'outflow'=>[]];
            $whaleLatest = [];
        }

        try {
            $news = $this->news->latest(6);
        } catch (\Throwable $e) {
            $news = [];
        }

        // Recent alerts for the logged-in user (show a small list on dashboard)
        $userAlerts = Alert::where('user_id', Auth::id())
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', [
            'market'      => $market,
            'whaleSeries' => $whaleSeries,
            'whaleLatest' => $whaleLatest,
            'news'        => $news,
            'userAlerts'  => $userAlerts,
        ]);
    }
}
