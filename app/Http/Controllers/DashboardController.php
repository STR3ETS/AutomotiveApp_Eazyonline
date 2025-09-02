<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Car;
use App\Models\Auto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Gebruik het Car model zoals in de dashboard view
        $total = Car::count();
        $intake = Car::where('status', 'Intake')->count();
        $readyForSale = Car::where('status', 'Verkoop klaar')->count();
        $sold = Car::where('status', 'sold')->count();

        return view('dashboard.index', [
            'total' => $total,
            'intake' => $intake,
            'readyForSale' => $readyForSale,
            'sold' => $sold,
        ]);
    }
}
