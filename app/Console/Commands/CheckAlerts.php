<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Services\CryptoService;
use Illuminate\Console\Command;

class CheckAlerts extends Command
{
    protected $signature = 'alerts:check';
    protected $description = 'Check user alerts against current market prices';

    public function handle(CryptoService $crypto): int
    {
        $alerts = Alert::where('active', true)->get();
        if ($alerts->isEmpty()) {
            $this->info('No active alerts.');
            return Command::SUCCESS;
        }

        foreach ($alerts as $alert) {
            $price = $crypto->getPrice($alert->symbol);
            if ($price === null) continue;

            $triggered = false;
            if ($alert->condition === 'above' && $price > (float)$alert->threshold) $triggered = true;
            if ($alert->condition === 'below' && $price < (float)$alert->threshold) $triggered = true;

            if ($triggered) {
                // TODO: send notification (email or Telegram)
                $this->info("Triggered: {$alert->symbol} {$alert->condition} {$alert->threshold} (now {$price})");
                $alert->update(['last_triggered_at' => now()]);
            }
        }

        return Command::SUCCESS;
    }
}
