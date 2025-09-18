<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Car;
use App\Models\CarStage;
use App\Models\Checklist;

class ChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = CarStage::all();
        $cars = Car::all();

        $defaultTasks = [
            'Intake' => [
                'Kenteken controleren',
                'Eigendomspapieren verifiëren', 
                'APK-status checken',
                'Sleutels ontvangen en tellen',
                'Schade-inspectie uitvoeren'
            ],
            'Technische controle' => [
                'Volledige technische inspectie',
                'Olie en vloeistoffen controleren',
                'Remmen en banden checken',
                'Motor- en transmissie test',
                'Elektronica en verlichting testen'
            ],
            'Herstel & Onderhoud' => [
                'Reparaties plannen en uitvoeren',
                'Onderdelen bestellen',
                'Werkuren bijhouden',
                'Kwaliteitscontrole reparaties',
                'Kostenberekening finaliseren'
            ],
            'Commercieel gereed' => [
                'Auto volledig schoonmaken',
                'Professionele foto\'s maken',
                'Advertentie tekst opstellen',
                'Prijs bepalen en valideren',
                'Online plaatsen op verkoopplatforms'
            ],
            'Verkoop klaar' => [
                'Finale administratie afhandelen',
                'Overdracht plannen met klant',
                'Auto voorbereiden voor aflevering',
                'Klant ontvangen en rondleiding geven',
                'Handtekeningen en betaling afhandelen'
            ],
        ];

        foreach ($cars as $car) {
            foreach ($stages as $stage) {
                if (isset($defaultTasks[$stage->name])) {
                    foreach ($defaultTasks[$stage->name] as $task) {
                        Checklist::firstOrCreate([
                            'car_id' => $car->id,
                            'stage_id' => $stage->id,
                            'task' => $task,
                        ], [
                            'is_completed' => false,
                            'company_id' => $car->company_id,
                        ]);
                    }
                }
            }
        }
    }
}
