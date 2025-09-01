<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Part;
use App\Models\Repair;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        Repair::create($data);

        return redirect()->route('repairs.index')->with('success', 'Reparatie aangemaakt.');
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