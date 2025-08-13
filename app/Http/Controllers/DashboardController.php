<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the trading floor dashboard (uses Blade view).
     */
    public function index()
    {
        // You can pass initial data here if needed; for now Blade will fetch via AJAX
        return view('dashboard');
    }
}
