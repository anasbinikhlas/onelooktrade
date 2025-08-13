<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Keep dashboard as a view/controller for now
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Dummy API endpoints (for dashboard) - inside auth group so only logged users can access
Route::middleware('auth')->group(function () {

    // Dummy JSON endpoints (temporary)
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

    // Placeholder pages so nav links don't 404
    Route::get('/markets', function () {
        return view('pages.markets');
    })->name('markets');

    Route::get('/whales', function () {
        return view('pages.whales');
    })->name('whales');

    Route::get('/news', function () {
        return view('pages.news');
    })->name('news');

    Route::get('/alerts', function () {
        return view('pages.alerts');
    })->name('alerts');

    // Breeze profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
