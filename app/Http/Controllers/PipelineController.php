<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarStage;
use Illuminate\Http\Request;
use App\Models\Checklist;
use Illuminate\Support\Facades\Log;

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
        try {
            $request->validate([
                'car_id' => 'required|exists:cars,id',
                'stage_id' => 'required|exists:car_stages,id',
            ]);

            $car = Car::findOrFail($request->car_id);
            $newStage = CarStage::findOrFail($request->stage_id);
            
            // Check if the car is already in this stage
            if ($car->stage_id == $newStage->id) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Auto staat al in deze fase.'
                ]);
            }
            
            // Check if all tasks in current stage are completed
            $currentStageTasks = $car->checklists->where('stage_id', $car->stage_id);
            $completedTasks = $currentStageTasks->where('is_completed', true);
            
            if ($currentStageTasks->count() > 0 && $completedTasks->count() !== $currentStageTasks->count()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Alle taken moeten voltooid zijn voordat de auto verplaatst kan worden.'
                ]);
            }
            
            $car->stage_id = $request->stage_id;
            $car->save();

            // (Optioneel) Maak checklist-taken aan voor deze fase als ze nog niet bestaan
            $stage = CarStage::findOrFail($request->stage_id);
            $defaultTasks = [
                // Vul hier per stage de standaard taken in
                'Intake & Juridisch' => [
                    'Kenteken controleren',
                    'Eigendomspapieren verifiëren', 
                    'APK-status checken',
                    'Sleutels ontvangen en tellen',
                    'Schade-inspectie uitvoeren'
                ],
                'Technische Intake' => [
                    'Volledige technische inspectie',
                    'Olie en vloeistoffen controleren',
                    'Remmen en banden checken',
                    'Motor- en transmissie test',
                    'Elektronica en verlichting testen'
                ],
                'Commercieel Gereed' => [
                    'Auto volledig schoonmaken',
                    'Professionele foto\'s maken',
                    'Advertentie tekst opstellen',
                    'Prijs bepalen en valideren',
                    'Online plaatsen op verkoopplatforms'
                ],
                'Proefrit & Verzekering' => [
                    'Proefrit regelen en begeleiden',
                    'Verzekeringspapieren voorbereiden',
                    'Financiering opties bespreken',
                    'Aankoopcontract opstellen',
                    'Garantievoorwaarden uitleggen'
                ],
                'Verkoop Klaar' => [
                    'Finale administratie afhandelen',
                    'Overdracht plannen met klant',
                    'Auto voorbereiden voor aflevering',
                    'Klant ontvangen en rondleiding geven',
                    'Handtekeningen en betaling afhandelen'
                ],
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

            Log::info('Car moved successfully', [
                'car_id' => $car->id,
                'to_stage' => $stage->name
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Auto succesvol verplaatst naar ' . $stage->name
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error moving car: ' . $e->getMessage(), [
                'car_id' => $request->car_id ?? null,
                'stage_id' => $request->stage_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Er is een fout opgetreden bij het verplaatsen van de auto.'
            ], 500);
        }
    }

    public function checklist($carId)
    {
        $car = Car::with(['stage', 'checklists' => function($query) {
            $query->orderBy('created_at');
        }])->findOrFail($carId);
        
        $stages = CarStage::orderBy('order')->get();
        $currentStageChecklists = $car->checklists->where('stage_id', $car->stage_id);
        
        return view('pipeline.checklist', compact('car', 'stages', 'currentStageChecklists'));
    }

    public function toggleChecklistItem(Request $request, $carId)
    {
        $request->validate([
            'checklist_id' => 'required|exists:checklists,id',
        ]);

        $checklist = Checklist::findOrFail($request->checklist_id);
        $checklist->is_completed = !$checklist->is_completed;
        $checklist->save();

        return response()->json(['success' => true, 'is_completed' => $checklist->is_completed]);
    }

    public function addTask(Request $request, $carId)
    {
        $request->validate([
            'task' => 'required|string|max:255',
        ]);

        $car = Car::findOrFail($carId);
        
        Checklist::create([
            'car_id' => $car->id,
            'stage_id' => $car->stage_id,
            'task' => $request->task,
            'is_completed' => false,
        ]);

        return redirect()->back()->with('success', 'Taak toegevoegd!');
    }
}
