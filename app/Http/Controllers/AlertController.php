<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alert::where('user_id', Auth::id())->latest()->get();
        return view('pages.alerts', compact('alerts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'symbol'    => 'required|string|max:10',
            'condition' => 'required|in:above,below',
            'threshold' => 'required|numeric',
            'channel'   => 'nullable|in:email,telegram',
        ]);

        $data['user_id'] = Auth::id();
        $data['active']  = true;

        Alert::create($data);

        return response()->json(['ok' => true]);
    }

    public function destroy(Alert $alert)
    {
        abort_unless($alert->user_id === Auth::id(), 403);
        $alert->delete();
        return redirect()->route('alerts')->with('status', 'Alert deleted');
    }
}
