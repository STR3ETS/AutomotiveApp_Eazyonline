<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarStage;
use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CarImageController;

class AutoController extends Controller
{
    /**
     * Display a listing of the cars.
     */
    public function index(Request $request)
    {
        $query = Car::with(['stage', 'currentAssignment.employee']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('license_plate', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(brand, ' ', model) like ?", ["%{$search}%"]);
            });
        }
        
        $cars = $query->latest()->paginate(20);
        return view('autos.index', compact('cars'));
    }

    /**
     * Show the form for creating a new car.
     */
    public function create()
    {
        $stages = CarStage::orderBy('order')->get();
        return view('autos.create', compact('stages'));
    }

    /**
     * Store a newly created car in storage.
     */
public function store(Request $request)
{
    if (!Auth::check()) {
        return redirect()->route('auth.login')
            ->with('error', 'Je moet ingelogd zijn om auto\'s toe te voegen.');
    }

    $user = Auth::user();
    $companyId = $user->company_id ?? session('current_company_id');

    if (!$companyId) {
        return redirect()->route('dashboard')
            ->with('error', 'Je account is niet gekoppeld aan een bedrijf.');
    }

    $validated = $request->validate([
        'license_plate' => 'required|string|max:255|unique:cars,license_plate',
        'brand' => 'required|string|max:255',
        'model' => 'required|string|max:255',
        'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
        'mileage' => 'required|integer|min:0',
        'price' => 'required|numeric|min:0',
        'stage_id' => 'nullable|exists:car_stages,id',
        'notes' => 'nullable|string',
        'images' => 'nullable|array',
        'images.*' => 'nullable|file',
    ]);

    $auto = Car::create([
        'license_plate' => $validated['license_plate'],
        'brand' => $validated['brand'],
        'model' => $validated['model'],
        'year' => $validated['year'],
        'mileage' => $validated['mileage'],
        'price' => $validated['price'],
        'stage_id' => $validated['stage_id'] ?? 1,
        'company_id' => $companyId,
    ]);

if ($request->hasFile('images')) {
    $imageRequest = new \Illuminate\Http\Request();
    $imageRequest->files->set('images', $request->file('images'));
    $imageRequest->merge([
        'category' => 'exterior',
        'alt_text' => '' // leeg laten als niet ingevuld
    ]);

    app(CarImageController::class)->store($imageRequest, $auto);
}

return redirect()->route('autos.show', $auto)
    ->with('success', 'Auto succesvol toegevoegd.');
}


    /**
     * Display the specified car.
     */
    public function show(Car $auto)
    {
        $auto->load(['stage', 'checklists.stage', 'repairs.parts', 'appointments', 'sales.customer', 'currentAssignment.employee']);
        return view('autos.show', compact('auto'));
    }

    /**
     * Show the form for editing the specified car.
     */
    public function edit(Car $auto)
    {
        $stages = CarStage::orderBy('order')->get();
        return view('autos.edit', compact('auto', 'stages'));
    }

    /**
     * Update the specified car in storage.
     */
    public function update(Request $request, Car $auto)
    {
        $validated = $request->validate([
            'license_plate' => 'required|string|max:255|unique:cars,license_plate,' . $auto->id,
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1950|max:' . (date('Y') + 1),
            'mileage' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'stage_id' => 'nullable|exists:car_stages,id',
        ]);

        // Update status based on stage
        if ($validated['stage_id']) {
            $stage = CarStage::find($validated['stage_id']);
            $validated['status'] = $stage ? $stage->name : $auto->status;
        }

        $auto->update($validated);

        return redirect()->route('autos.index')
            ->with('success', 'Auto succesvol bijgewerkt!');
    }

    /**
     * Remove the specified car from storage.
     */
    public function destroy(Car $auto)
    {
        // Check if car has active sales
        if ($auto->sales()->whereNotIn('status', ['delivered', 'cancelled'])->exists()) {
            return redirect()->route('autos.index')
                ->with('error', 'Kan auto niet verwijderen: er zijn actieve verkopen gekoppeld.');
        }

        $licensePlate = $auto->license_plate;
        $auto->delete();

        return redirect()->route('autos.index')
            ->with('success', 'Auto ' . $licensePlate . ' succesvol verwijderd.');
    }

    /**
     * Create checklists for all stages for a new car
     */
    private function createChecklistsForCar(Car $car)
    {
        $stages = CarStage::all();
        
        foreach ($stages as $stage) {
            $tasks = $stage->default_tasks;
            
            foreach ($tasks as $task) {
                Checklist::create([
                    'car_id' => $car->id,
                    'stage_id' => $stage->id,
                    'task' => $task,
                    'is_completed' => false,
                ]);
            }
        }
    }

    /**
     * Quick search for cars (AJAX endpoint)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $cars = Car::where('license_plate', 'LIKE', "%{$query}%")
            ->orWhere('brand', 'LIKE', "%{$query}%")
            ->orWhere('model', 'LIKE', "%{$query}%")
            ->with('stage')
            ->limit(10)
            ->get();

        return response()->json($cars);
    }
}
