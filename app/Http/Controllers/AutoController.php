<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarStage;
use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
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
        Log::info('Store method called');
        Log::info('Request method:', [$request->method()]);
        Log::info('Content type:', [$request->header('Content-Type')]);
        Log::info('Request all:', $request->all());
        Log::info('Request files:', $request->allFiles());
        Log::info('Has file images:', [$request->hasFile('images')]);
        
        // Check what's in the images field specifically
        if ($request->has('images')) {
            Log::info('Images field exists');
            $images = $request->input('images');
            Log::info('Images field content type:', [gettype($images)]);
            if (is_array($images)) {
                Log::info('Images array count:', [count($images)]);
                foreach ($images as $key => $img) {
                    Log::info("Image $key type:", [gettype($img)]);
                    if (is_object($img)) {
                        Log::info("Image $key class:", [get_class($img)]);
                    }
                }
            }
        }
        
        // Let's try a more permissive validation first
        try {
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
            Log::info('Validation passed with correct field names');
        } catch (\Exception $e) {
            Log::error('Validation failed:', [$e->getMessage()]);
            throw $e;
        }

        // Check if user is authenticated using session
        if (!session('authenticated')) {
            Log::error('User not authenticated via session');
            return redirect()->route('auth.login')->with('error', 'Je moet ingelogd zijn om auto\'s toe te voegen.');
        }

        $companyId = session('current_company_id');
        if (!$companyId) {
            Log::error('No company_id in session');
            return redirect()->route('dashboard')->with('error', 'Je account is niet gekoppeld aan een bedrijf.');
        }

        // Create the car
        $auto = Car::create([
            'license_plate' => $validated['license_plate'],
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'year' => $validated['year'],
            'mileage' => $validated['mileage'],
            'price' => $validated['price'],
            'stage_id' => $validated['stage_id'] ?? 1, // Default to first stage
            'company_id' => $companyId,
        ]);

        Log::info('Car created with ID: ' . $auto->id);

        // Handle images if any
        if ($request->hasFile('images')) {
            Log::info('Images found, processing...');
            foreach ($request->file('images') as $index => $image) {
                Log::info('Processing image ' . $index . ': ' . $image->getClientOriginalName());
                Log::info('Image mime type: ' . $image->getMimeType());
                Log::info('Image size: ' . $image->getSize());
                
                // Create a new request for each image
                $imageRequest = new Request();
                $imageRequest->files->set('images', [$image]);
                $imageRequest->merge(['category' => 'exterior']);
                
                $carImageController = new CarImageController();
                $carImageController->store($imageRequest, $auto);
            }
        } else {
            Log::info('No images found in request');
        }

        return redirect()->route('autos.index')->with('success', 'Auto succesvol toegevoegd.');
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
