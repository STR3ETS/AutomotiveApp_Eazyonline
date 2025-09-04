<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Sale;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActiveSalesController extends Controller
{
    public function index()
    {
        // Proefritten (komende 7 dagen)
        $startOfWeek = Carbon::now()->startOfDay();
        $endOfWeek = Carbon::now()->addDays(7)->endOfDay();
        
        $testDrives = Appointment::with(['car', 'customer'])
            ->where('type', 'proefrit')
            ->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->map(function ($appointment) {
                $appointmentDate = Carbon::parse($appointment->date . ' ' . $appointment->time);
                $appointment->formatted_datetime = $appointmentDate;
                $appointment->day_label = $this->getDayLabel($appointmentDate);
                return $appointment;
            });

        // Verkochte auto's (nog niet opgeleverd)
        $soldCars = Sale::with(['car', 'customer'])
            ->whereIn('status', ['contract_signed', 'ready_for_delivery'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Geplande ophalingen/afleveringen (komende 7 dagen)
        $deliveries = Appointment::with(['car', 'customer'])
            ->where('type', 'aflevering')
            ->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->map(function ($appointment) {
                $appointmentDate = Carbon::parse($appointment->date . ' ' . $appointment->time);
                $appointment->formatted_datetime = $appointmentDate;
                $appointment->day_label = $this->getDayLabel($appointmentDate);
                return $appointment;
            });

        // Auto's in "Verkoop klaar" fase (klaar voor verkoop activiteiten)
        $readyForSale = Car::with(['stage'])
            ->whereHas('stage', function($query) {
                $query->where('name', 'Verkoop klaar');
            })
            ->get();

        return view('active-sales.index', compact('testDrives', 'soldCars', 'deliveries', 'readyForSale'));
    }
    
    private function getDayLabel($date)
    {
        $now = Carbon::now();
        
        if ($date->isToday()) {
            return 'Vandaag ' . $date->format('H:i');
        } elseif ($date->isTomorrow()) {
            return 'Morgen ' . $date->format('H:i');
        } else {
            $dayNames = [
                'Monday' => 'Maandag',
                'Tuesday' => 'Dinsdag', 
                'Wednesday' => 'Woensdag',
                'Thursday' => 'Donderdag',
                'Friday' => 'Vrijdag',
                'Saturday' => 'Zaterdag',
                'Sunday' => 'Zondag'
            ];
            
            $dayName = $dayNames[$date->format('l')] ?? $date->format('l');
            return $dayName . ' ' . $date->format('H:i');
        }
    }
}
