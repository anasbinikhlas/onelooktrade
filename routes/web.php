<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlertController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Dashboard (auth + verified)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Auth-only group
Route::middleware('auth')->group(function () {

    // JSON stubs for dashboard panels (replace later with controllers)
    Route::get('/market-data', function () {
        return response()->json([
            'btc' => ['price' => 65430, 'change' => 2.5],
            'eth' => ['price' => 3240, 'change' => -1.2],
            'top' => [
                ['symbol'=>'SOL','price'=>120,'change'=>5.2],
                ['symbol'=>'ADA','price'=>0.52,'change'=>3.1],
                ['symbol'=>'XRP','price'=>0.92,'change'=>-2.4],
            ]
        ]);
    })->name('market.data');

    Route::get('/whales-data', function () {
        return response()->json([
            ['wallet'=>'0xAb12...89Cd','token'=>'BTC','amount'=>500,'type'=>'inflow','time'=>'5 mins ago'],
            ['wallet'=>'0xEf34...67Gh','token'=>'ETH','amount'=>1200,'type'=>'outflow','time'=>'15 mins ago'],
            ['wallet'=>'0x12cd...A1b2','token'=>'USDT','amount'=>2500000,'type'=>'inflow','time'=>'30 mins ago'],
        ]);
    })->name('whales.data');

    Route::get('/news-data', function () {
        return response()->json([
            ['title'=>'Bitcoin hits new high','source'=>'CoinDesk','time'=>'1 hour ago','url'=>'https://coindesk.com/...'],
            ['title'=>'Ethereum upgrade scheduled','source'=>'CryptoNews','time'=>'3 hours ago','url'=>'https://cryptonews.com/...'],
        ]);
    })->name('news.data');

    // Placeholder pages
    Route::view('/markets', 'pages.markets')->name('markets');
    Route::view('/whales', 'pages.whales')->name('whales');
    Route::view('/news', 'pages.news')->name('news');

    // Alerts (Controller-backed)
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts');
    Route::post('/alerts', [AlertController::class, 'store'])->name('alerts.store');
    Route::delete('/alerts/{alert}', [AlertController::class, 'destroy'])->name('alerts.destroy');

    // Breeze profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Breeze auth routes
require __DIR__.'/auth.php';
