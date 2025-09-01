<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AgendaController extends Controller
{
    // Toon alle afspraken van de huidige week
    public function index()
    {
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();
        $appointments = Appointment::whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->orderBy('time')
            ->get();
        return view('agenda.index', compact('appointments'));
    }

    // Sla een nieuwe afspraak op
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'customer_name' => 'required',
            'type' => 'required|in:proefrit,aflevering,werkplaats',
            'date' => 'required|date',
            'time' => 'required',
            'notes' => 'nullable',
        ]);
        Appointment::create($validated);
        return redirect()->route('agenda.index')->with('success', 'Afspraak toegevoegd!');
    }

    // Verwijder een afspraak
    public function destroy($id)
    {
        Appointment::findOrFail($id)->delete();
        return redirect()->route('agenda.index')->with('success', 'Afspraak verwijderd!');
    }
}
