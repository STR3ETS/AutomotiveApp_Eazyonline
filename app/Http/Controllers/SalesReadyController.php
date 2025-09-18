<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User;
use App\Models\CarStage;
use App\Models\Employee;
use App\Mail\WorkReportMail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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

    public function sendEmailReport(Car $car)
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

        // Genereer PDF en sla tijdelijk op
        $pdf = Pdf::loadView('sales-ready.pdf-report', compact('car'));
        $filename = 'werkzaamheden_' . str_replace('-', '_', $car->license_plate) . '_' . date('Y-m-d') . '.pdf';
        $tempPath = storage_path('app/temp/' . $filename);
        
        // Zorg dat de temp directory bestaat
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        
        $pdf->save($tempPath);

        // Verzamel e-mail adressen
        $recipients = [];
        
        // Huidige ingelogde gebruiker (moet een employee zijn)
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->email) {
            $recipients[] = $currentUser->email;
        }
        
        // Eigenaar van het bedrijf
        $owner = Employee::where('company_id', $car->company_id)
                         ->where('position', 'eigenaar')
                         ->first();
        if ($owner && $owner->email && !in_array($owner->email, $recipients)) {
            $recipients[] = $owner->email;
        }
        
        // Klant via de laatste verkoop
        $sale = $car->sales()->with('customer')->latest()->first();
        if ($sale && $sale->customer && $sale->customer->email && !in_array($sale->customer->email, $recipients)) {
            $recipients[] = $sale->customer->email;
        }

        // Verstuur e-mails
        $sentCount = 0;
        foreach ($recipients as $email) {
            try {
                Mail::to($email)->send(new WorkReportMail($car, $tempPath));
                $sentCount++;
            } catch (\Exception $e) {
                // Log de fout maar ga door met de andere e-mails
                Log::error('Failed to send work report email to ' . $email . ': ' . $e->getMessage());
            }
        }

        // Verwijder tijdelijk bestand
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        // Geef feedback
        if ($sentCount > 0) {
            return back()->with('success', "Werkzaamheden rapport succesvol verzonden naar {$sentCount} ontvanger(s).");
        } else {
            return back()->with('error', 'Er konden geen e-mails worden verzonden. Controleer de e-mailadressen en configuratie.');
        }
    }
}
