<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Car;
use App\Models\Customer;
use App\Models\DeliveryChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['car','customer','checklistItems'])
            ->whereNotIn('status', ['delivered','cancelled'])
            ->get();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $cars = Car::all(); // Tijdelijk alle auto's tonen om te debuggen
        $customers = Customer::all();
        return view('sales.create', compact('cars','customers'));
    }

    public function store(Request $request)
    {
        // Eerst kijken of we een bestaande klant hebben of een nieuwe moeten aanmaken
        $hasCustomerId = !empty($request->input('customer_id'));
        
        $validationRules = [
            'car_id' => 'required|exists:cars,id',
            'sale_price' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'delivery_date' => 'nullable|date',
            'delivery_time' => 'nullable',
            'notes' => 'nullable|string',
        ];

        if ($hasCustomerId) {
            // Bestaande klant
            $validationRules['customer_id'] = 'required|exists:customers,id';
        } else {
            // Nieuwe klant
            $validationRules['customer_name'] = 'required|string|max:255';
            $validationRules['customer_email'] = 'required|email|max:255';
            $validationRules['customer_phone'] = 'nullable|string|max:255';
            $validationRules['customer_address'] = 'nullable|string';
        }

        $data = $request->validate($validationRules);

        DB::transaction(function() use ($data, $request, &$sale) {
            if (empty($data['customer_id'])) {
                $customer = Customer::create([
                    'name' => $request->input('customer_name'),
                    'email' => $request->input('customer_email'),
                    'phone' => $request->input('customer_phone'),
                    'address' => $request->input('customer_address'),
                ]);
                $customerId = $customer->id;
            } else {
                $customerId = $data['customer_id'];
            }

            $sale = Sale::create([
                'car_id' => $data['car_id'],
                'customer_id' => $customerId,
                'sale_price' => $data['sale_price'],
                'deposit_amount' => $data['deposit_amount'],
                'delivery_date' => $data['delivery_date'],
                'delivery_time' => $data['delivery_time'],
                'notes' => $data['notes'],
            ]);

            $tasks = [
                'APK/Technische check afgerond',
                'Interieur + exterieur poetsen',
                'Kentekenplaten gemonteerd',
                'Sleutels + reservesleutel aanwezig',
                'RDW vrijwaring geregeld',
                'Verzekering & groene kaart bevestigd',
                'Contract ondertekend en gedocumenteerd',
            ];

            foreach ($tasks as $task) {
                $sale->checklistItems()->create(['task' => $task]);
            }

            Car::where('id', $data['car_id'])->update(['status' => 'Technische controle']);
        });

        return redirect()->route('sales.show', $sale)->with('success','Verkoopdossier aangemaakt.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['car','customer','checklistItems']);
        return view('sales.show', compact('sale'));
    }

    public function update(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'sale_price' => 'nullable|numeric',
            'deposit_amount' => 'nullable|numeric',
            'payment_status' => 'nullable|in:open,deposit_paid,paid,refunded',
            'status' => 'nullable|in:option,contract_signed,ready_for_delivery,delivered,cancelled',
            'delivery_date' => 'nullable|date',
            'delivery_time' => 'nullable',
            'notes' => 'nullable|string',
        ]);

        $sale->update($data);

        return back()->with('success','Verkoopdossier bijgewerkt.');
    }

    public function toggleChecklistItem(DeliveryChecklistItem $item)
    {
        $item->update(['is_completed' => !$item->is_completed]);

        $progress = [
            'completed' => $item->sale->checklistItems()->where('is_completed', true)->count(),
            'total' => $item->sale->checklistItems()->count(),
        ];

        return response()->json($progress);
    }

    public function markAsDelivered(Sale $sale)
    {
        if ($sale->payment_status !== 'paid') {
            return back()->with('error','Betaling nog niet volledig voldaan.');
        }

        if ($sale->checklistItems()->where('is_completed', false)->exists()) {
            return back()->with('error','Niet alle checklist-items zijn voltooid.');
        }

        $sale->update([
            'status' => 'delivered',
            'sold_at' => now(),
        ]);

        $sale->car->update([
            'status' => 'sold',
            'sold_at' => now(),
        ]);

        return back()->with('success','Auto succesvol afgeleverd.');
    }

    public function cancel(Sale $sale)
    {
        $sale->update(['status' => 'cancelled']);

        $sale->car->update([
            'status' => 'Verkoop klaar'
        ]);

        return back()->with('success','Verkoop geannuleerd.');
    }
}
