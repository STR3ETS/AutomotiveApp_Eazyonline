<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarStage;
use Illuminate\Http\Request;
use App\Models\Checklist;

class PipelineController extends Controller
{
    public function index()
    {
        $stages = CarStage::with(['cars.checklists'])->orderBy('order')->get();
        return view('pipeline.index', compact('stages'));
    }

    // Verwerk drag & drop: update stage en maak checklist-taken aan
    public function move(Request $request)
    {
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'stage_id' => 'required|exists:car_stages,id',
        ]);

        $car = Car::findOrFail($request->car_id);
        $car->stage_id = $request->stage_id;
        $car->save();

        // (Optioneel) Maak checklist-taken aan voor deze fase als ze nog niet bestaan
        $stage = CarStage::findOrFail($request->stage_id);
        $defaultTasks = [
            // Vul hier per stage de standaard taken in
            'Intake' => ['Kenteken controleren', 'Sleutels ontvangen'],
            'Technische intake' => ['Technische inspectie', 'Olie verversen'],
            'Commercieel gereed' => ['Auto poetsen', 'Foto maken'],
            'Proefrit & Verzekering' => ['Proefrit plannen', 'Verzekering checken'],
            'Verkoop & Aflevering' => ['Papieren regelen', 'Afleveren aan klant'],
        ];
        if (isset($defaultTasks[$stage->name])) {
            foreach ($defaultTasks[$stage->name] as $task) {
                Checklist::firstOrCreate([
                    'car_id' => $car->id,
                    'stage_id' => $stage->id,
                    'task' => $task,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
}
