<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Car;
use App\Models\CarStage;
use App\Models\Repair;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Totaal aantal auto's
        $totalCars = Car::count();
        
        // Aantal auto's in intake (eerste stage)
        $intakeStage = CarStage::where('name', 'Intake')->first();
        $intakeCars = $intakeStage ? Car::where('stage_id', $intakeStage->id)->count() : 0;
        
        // Aantal advertenties live (Commercieel gereed + Verkoop klaar)
        $commercialStage = CarStage::where('name', 'Commercieel gereed')->first();
        $salesReadyStage = CarStage::where('name', 'Verkoop klaar')->first();
        
        $liveAds = 0;
        if ($commercialStage) {
            $liveAds += Car::where('stage_id', $commercialStage->id)->count();
        }
        if ($salesReadyStage) {
            $liveAds += Car::where('stage_id', $salesReadyStage->id)->count();
        }
        
        // Aantal open reparaties (alles behalve 'gereed')
        $openRepairs = Repair::whereIn('status', ['gepland', 'bezig', 'wachten_op_onderdeel'])->count();
        
        // Weekagenda - haal afspraken op voor de komende 7 dagen
        $startOfWeek = Carbon::now()->startOfDay();
        $endOfWeek = Carbon::now()->addDays(7)->endOfDay();
        
        $weekAppointments = Appointment::with(['car', 'customer'])
            ->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->map(function ($appointment) {
                $appointmentDate = Carbon::parse($appointment->date . ' ' . $appointment->time);
                $appointment->formatted_date = $appointmentDate;
                $appointment->day_label = $this->getDayLabel($appointmentDate);
                return $appointment;
            });

        return view('dashboard.index', [
            'totalCars' => $totalCars,
            'intakeCars' => $intakeCars,
            'liveAds' => $liveAds,
            'openRepairs' => $openRepairs,
            'weekAppointments' => $weekAppointments,
        ]);
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
