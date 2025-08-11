<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Dummy data
        $whales = [
            ['coin' => 'BTC', 'amount' => '500 BTC', 'from' => 'Unknown Wallet', 'to' => 'Binance', 'time' => '5 mins ago'],
            ['coin' => 'ETH', 'amount' => '2,000 ETH', 'from' => 'Binance', 'to' => 'Coinbase', 'time' => '15 mins ago'],
        ];

        $trends = [
            ['coin' => 'BTC', 'price' => '$62,500', 'change' => '+2.5%'],
            ['coin' => 'ETH', 'price' => '$3,100', 'change' => '-1.2%'],
        ];

        $news = [
            ['title' => 'Bitcoin hits new high!', 'source' => 'CoinDesk', 'time' => '1 hour ago'],
            ['title' => 'Ethereum upgrade scheduled', 'source' => 'CryptoNews', 'time' => '3 hours ago'],
        ];

        $alerts = [
            ['message' => 'BTC dropped by 5%', 'time' => '10 mins ago'],
            ['message' => 'ETH whale movement detected', 'time' => '30 mins ago'],
        ];

        return view('dashboard', compact('whales', 'trends', 'news', 'alerts'));
    }
}
