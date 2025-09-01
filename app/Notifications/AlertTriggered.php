<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AlertTriggered extends Notification implements ShouldQueue
{
    use Queueable;

    public $alert;
    public $currentPrice;

    public function __construct($alert, $currentPrice)
    {
        $this->alert = $alert;
        $this->currentPrice = $currentPrice;
    }

    public function via($notifiable)
    {
        // default channels: mail + database
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $cond = $this->alert->condition === 'price_above' ? 'above' : 'below';
        return (new MailMessage)
            ->subject("Alert triggered — {$this->alert->symbol}")
            ->line("Your alert for {$this->alert->symbol} was triggered.")
            ->line("Condition: price {$cond} {$this->alert->target_price}")
            ->line("Current price: {$this->currentPrice}")
            ->action('Open Dashboard', url('/alerts')) // adjust route
            ->line('You can edit or disable the alert from your Alerts page.');
    }

    public function toArray($notifiable)
    {
        return [
            'alert_id' => $this->alert->id,
            'symbol' => $this->alert->symbol,
            'condition' => $this->alert->condition,
            'target_price' => (string)$this->alert->target_price,
            'current_price' => (string)$this->currentPrice,
        ];
    }
}
