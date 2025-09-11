<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarStage;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesReadyController extends Controller
{
    public function index()
    {
        // Haal de "Verkoop klaar" stage op
        $salesReadyStage = CarStage::where('name', 'Verkoop klaar')->first();
        
        if (!$salesReadyStage) {
            return view('sales-ready.index', ['cars' => collect()]);
        }
        
        // Haal alle auto's op die in de "Verkoop klaar" fase staan
        $cars = Car::where('stage_id', $salesReadyStage->id)
            ->with([
                'checklists' => function($query) {
                    $query->where('is_completed', true)
                          ->with(['stage', 'repair'])
                          ->orderBy('created_at');
                },
                'stage'
            ])
            ->get();
        
        // Groepeer de checklist items per auto en per stage voor een overzichtelijke weergave
        foreach ($cars as $car) {
            $car->completed_tasks_by_stage = $car->checklists->groupBy('stage.name');
        }
        
        return view('sales-ready.index', compact('cars'));
    }

    public function exportPdf(Car $car)
    {
        // Laad de auto met alle benodigde relaties
        $car->load([
            'checklists' => function($query) {
                $query->where('is_completed', true)
                      ->with(['stage', 'repair'])
                      ->orderBy('created_at');
            },
            'stage',
            'company'
        ]);

        // Groepeer taken per stage
        $car->completed_tasks_by_stage = $car->checklists->groupBy('stage.name');

        // Genereer PDF
        $pdf = Pdf::loadView('sales-ready.pdf-report', compact('car'));
        
        $filename = 'werkzaamheden_' . str_replace('-', '_', $car->license_plate) . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}
