<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run()
    {
        $cars = Car::limit(3)->get();
        
        if ($cars->count() < 3) {
            echo "Niet genoeg auto's in database om appointments aan te maken.\n";
            return;
        }
        
        // Vandaag 14:00 - Proefrit
        Appointment::create([
            'car_id' => $cars->first()->id,
            'customer_name' => 'P. van der Berg',
            'type' => 'proefrit',
            'date' => Carbon::today()->format('Y-m-d'),
            'time' => '14:00',
            'notes' => 'Klant wil uitgebreide proefrit'
        ]);

        // Morgen 10:30 - Aflevering
        Appointment::create([
            'car_id' => $cars->get(1)->id,
            'customer_name' => 'J. Bakker',
            'type' => 'aflevering',
            'date' => Carbon::tomorrow()->format('Y-m-d'),
            'time' => '10:30',
            'notes' => 'Alle papieren gereed'
        ]);

        // Donderdag - Inspectie
        Appointment::create([
            'car_id' => $cars->get(2)->id,
            'customer_name' => 'M. de Vries',
            'type' => 'proefrit',
            'date' => Carbon::now()->next(Carbon::THURSDAY)->format('Y-m-d'),
            'time' => '15:30',
            'notes' => 'APK keuring'
        ]);

        echo "Appointments toegevoegd!\n";
    }
}
