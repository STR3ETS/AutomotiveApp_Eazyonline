<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Part;
use App\Models\Repair;
use App\Models\CarStage;
use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class RepairController extends Controller
{
    /**
     * GET /repairs
     * Toon overzicht + formulieren.
     */
    public function index()
    {
        // Laad auto's voor dropdown (pas velden aan je Car-model aan)
$cars = Car::orderBy('id', 'desc')->get(['id', 'license_plate', 'brand', 'model']);

        // Haal alle reparaties met auto + onderdelen op
        $repairs = Repair::with(['car', 'parts'])->latest()->get();

        return view('repairs.index', compact('repairs', 'cars'));
        
    }

    /**
     * POST /repairs
     * Nieuwe reparatie toevoegen.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'car_id'       => ['required', 'exists:cars,id'],
            'description'  => ['required', 'string', 'max:255'],
            'status'       => ['required', Rule::in(['gepland','bezig','wachten_op_onderdeel','gereed'])],
            'cost_estimate'=> ['nullable', 'numeric', 'min:0'],
        ]);

        $repair = Repair::create($data);
        
        // Handel auto stage logica af
        $this->handleCarStageForRepair($repair);

        return redirect()->route('repairs.index')->with('success', 'Reparatie aangemaakt.');
    }
    
    /**
     * Handel de auto stage logica af wanneer een reparatie wordt toegevoegd
     */
    private function handleCarStageForRepair(Repair $repair)
    {
        $car = $repair->car;
        $repairStage = CarStage::where('name', 'Herstel & Onderhoud')->first();
        
        if (!$repairStage) {
            Log::warning('Herstel & Onderhoud stage niet gevonden');
            return;
        }
        
        // Als de auto in een hogere stage staat dan "Herstel & Onderhoud", zet hem terug
        if ($car->stage && $car->stage->order > $repairStage->order) {
            $car->update(['stage_id' => $repairStage->id]);
            Log::info("Auto {$car->license_plate} teruggeplaatst naar Herstel & Onderhoud vanwege nieuwe reparatie");
        }
        
        // Controleer of er al een checklist item bestaat voor deze reparatie
        $existingChecklist = Checklist::where([
            'car_id' => $car->id,
            'stage_id' => $repairStage->id,
            'repair_id' => $repair->id
        ])->first();
        
        if (!$existingChecklist) {
            // Voeg checklist item toe aan "Herstel & Onderhoud" stage
            Checklist::create([
                'car_id' => $car->id,
                'stage_id' => $repairStage->id,
                'task' => 'Reparatie: ' . $repair->description,
                'is_completed' => false,
                'repair_id' => $repair->id
            ]);
        }
    }

    /**
     * PUT /repairs/{id}
     * Status/kostenraming bijwerken.
     */
    public function update(Request $request, Repair $repair)
    {
        $data = $request->validate([
            'status'        => ['required', Rule::in(['gepland','bezig','wachten_op_onderdeel','gereed'])],
            'cost_estimate' => ['nullable', 'numeric', 'min:0'],
            'description'   => ['sometimes', 'string', 'max:255'],
        ]);

        $repair->update($data);

        return redirect()->route('repairs.index')->with('success', 'Reparatie bijgewerkt.');
    }

    /**
     * DELETE /repairs/{id}
     * Reparatie verwijderen (onderdelen gaan mee weg).
     */
    public function destroy(Repair $repair)
    {
        $repair->delete();

        return redirect()->route('repairs.index')->with('success', 'Reparatie verwijderd.');
    }

    /**
     * POST /repairs/{id}/parts
     * Nieuw onderdeel toevoegen aan reparatie.
     */
    public function addPart(Request $request, Repair $repair)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'status'=> ['required', Rule::in(['besteld','geleverd','gemonteerd'])],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $repair->parts()->create($data);

        return redirect()->route('repairs.index')->with('success', 'Onderdeel toegevoegd.');
    }

    /**
     * PUT /parts/{id}
     * Onderdeelstatus/prijs bijwerken.
     */
    public function updatePart(Request $request, Part $part)
    {
        $data = $request->validate([
            'status'=> ['required', Rule::in(['besteld','geleverd','gemonteerd'])],
            'price' => ['nullable', 'numeric', 'min:0'],
            'name'  => ['sometimes', 'string', 'max:255'],
        ]);

        $part->update($data);

        return redirect()->route('repairs.index')->with('success', 'Onderdeel bijgewerkt.');
    }
}