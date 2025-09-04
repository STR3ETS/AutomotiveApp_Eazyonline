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
        
        // Zorg dat elke auto checklists heeft voor ALLE fases, niet alleen de huidige
        foreach ($stages as $stage) {
            foreach ($stage->cars as $car) {
                $this->ensureChecklistsExist($car, $stage);
            }
        }
        
        // Zorg ook dat auto's die niet in een specifieke stage staan, toch checklists hebben
        $allCars = Car::all();
        foreach ($allCars as $car) {
            foreach ($stages as $stage) {
                $this->ensureChecklistsExist($car, $stage);
            }
        }
        
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
        $targetStage = CarStage::findOrFail($request->stage_id);
        $currentStage = $car->stage;

        // Check of de auto mag worden verplaatst
        if ($currentStage && !$car->canMoveToNextStage() && $targetStage->order > $currentStage->order) {
            // Debug informatie
            $checklists = $car->checklists()->where('stage_id', $car->stage_id)->get();
            $completed = $checklists->where('is_completed', true)->count();
            $total = $checklists->count();
            
            return response()->json([
                'success' => false, 
                'message' => "Voltooi eerst alle taken in de huidige fase voordat je de auto kunt verplaatsen. ({$completed}/{$total} voltooid)",
                'debug' => [
                    'car_id' => $car->id,
                    'current_stage' => $currentStage->name,
                    'target_stage' => $targetStage->name,
                    'completed_tasks' => $completed,
                    'total_tasks' => $total,
                    'can_move' => $car->canMoveToNextStage(),
                    'completion' => $car->stage_completion
                ]
            ], 422);
        }

        // Update car stage
        $car->stage_id = $request->stage_id;
        
        // Update car status based on stage - use exact stage names
        $car->status = $targetStage->name;
        
        $car->save();

        // Maak checklist taken aan voor ALLE stages, niet alleen de nieuwe
        $allStages = CarStage::all();
        foreach ($allStages as $stage) {
            $this->ensureChecklistsExist($car, $stage);
        }

        return response()->json(['success' => true]);
    }

    // Zorg ervoor dat checklist taken bestaan voor een auto in een specifieke stage
    private function ensureChecklistsExist(Car $car, CarStage $stage)
    {
        $existingTasks = $car->checklists()
            ->where('stage_id', $stage->id)
            ->pluck('task')
            ->toArray();

        foreach ($stage->default_tasks as $task) {
            if (!in_array($task, $existingTasks)) {
                Checklist::create([
                    'car_id' => $car->id,
                    'stage_id' => $stage->id,
                    'task' => $task,
                    'is_completed' => false,
                ]);
            }
        }
    }

    // Toon checklist voor een specifieke auto
    public function showChecklist(Car $car)
    {
        $car->load(['stage', 'checklists' => function($query) use ($car) {
            $query->where('stage_id', $car->stage_id)->orderBy('id');
        }]);
        
        // Zorg dat alle checklists bestaan voor de huidige stage
        $this->ensureChecklistsExist($car, $car->stage);
        
        // Herlaad de checklists na het aanmaken
        $car->load(['checklists' => function($query) use ($car) {
            $query->where('stage_id', $car->stage_id)->orderBy('id');
        }]);
        
        return view('pipeline.checklist', compact('car'));
    }

    // Update checklist item
    public function updateChecklistItem(Request $request, Checklist $checklist)
    {
        $request->validate([
            'is_completed' => 'required|boolean'
        ]);

        $checklist->update([
            'is_completed' => $request->is_completed
        ]);
        
        // Als deze checklist gekoppeld is aan een reparatie en wordt afgevinkt, 
        // zet de reparatie op "gereed"
        if ($checklist->repair_id && $request->is_completed) {
            $repair = $checklist->repair;
            if ($repair && $repair->status !== 'gereed') {
                $repair->update(['status' => 'gereed']);
            }
        }

        $car = $checklist->car;
        $completion = $car->stage_completion;

        return response()->json([
            'success' => true,
            'completion' => $completion,
            'can_move' => $car->canMoveToNextStage()
        ]);
    }
}
