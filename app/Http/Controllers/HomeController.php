<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        
        return view('welcome');
    }

    public function dashboard()
    {
        $this->middleware('auth');
        
        $user = auth()->user();
        $recentConversions = $user->conversions()->latest()->take(5)->get();
        $dailyConversionsRemaining = $user->remaining_daily_conversions;
        
        return view('dashboard', compact('recentConversions', 'dailyConversionsRemaining'));
    }
}