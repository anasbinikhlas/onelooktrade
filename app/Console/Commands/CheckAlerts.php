<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alert;
use App\Notifications\PriceAlertNotification;
use Illuminate\Support\Facades\Http;

class CheckAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example: php artisan alerts:check
     */
    protected $signature = 'alerts:check';

    /**
     * The console command description.
     */
    protected $description = 'Check all user alerts and trigger notifications if conditions are met';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking alerts...');

        // Fetch market data (stubbed or real API)
        $marketData = $this->fetchMarketData();

        // Loop alerts
        $alerts = Alert::where('is_active', true)->get();

        foreach ($alerts as $alert) {
            $symbol = strtoupper($alert->symbol);

            if (! isset($marketData[$symbol])) {
                $this->warn("No price found for {$symbol}");
                continue;
            }

            $price = $marketData[$symbol];

            // Condition check
            $triggered = false;
            if ($alert->operator === '>=' && $price >= $alert->target_price) {
                $triggered = true;
            } elseif ($alert->operator === '<=' && $price <= $alert->target_price) {
                $triggered = true;
            }

            if ($triggered) {
                $this->info("Alert triggered for {$symbol} at {$price}");

                // Send notification
                $alert->user->notify(new PriceAlertNotification($alert, $price));

                // Mark as inactive (optional)
                // $alert->update(['is_active' => false]);
            }
        }

        $this->info('Done.');
    }

    /**
     * Fetch market data (stubbed).
     */
    private function fetchMarketData(): array
    {
        // Replace with real API (e.g., CoinGecko)
        return [
            'BTC' => 65430,
            'ETH' => 3240,
            'SOL' => 120,
        ];
    }
}
