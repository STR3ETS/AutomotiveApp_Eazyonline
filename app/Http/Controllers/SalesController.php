<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Car;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['car','customer']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('car', function($carQuery) use ($search) {
                    $carQuery->where('license_plate', 'like', "%{$search}%")
                             ->orWhere('brand', 'like', "%{$search}%")
                             ->orWhere('model', 'like', "%{$search}%")
                             ->orWhereRaw("CONCAT(brand, ' ', model) like ?", ["%{$search}%"]);
                })->orWhereHas('customer', function($customerQuery) use ($search) {
                    $customerQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Payment status filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        $sales = $query->whereNotIn('status', ['delivered','cancelled'])
                      ->latest()
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
                'status' => 'option', // Start with option status
                'payment_status' => 'open', // Start with open payment
            ]);

            // Update car stage to a sales-related stage if needed
            if ($sale->car->stage && $sale->car->stage->name === 'Verkoop klaar') {
                // Keep it in sales ready stage, don't change anything
            } else {
                // Find and set to a sales stage
                $salesStage = \App\Models\CarStage::where('name', 'LIKE', '%verkoop%')->first();
                if ($salesStage) {
                    $sale->car->update(['stage_id' => $salesStage->id]);
                }
            }
        });

        return redirect()->route('sales.show', $sale)->with('success','Verkoopdossier aangemaakt.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['car.stage','customer']);
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $sale->load(['car', 'customer']);
        return view('sales.edit', compact('sale'));
    }

    public function update(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'sale_price' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0|lte:sale_price',
            'payment_status' => 'required|in:open,deposit_paid,paid,refunded',
            'status' => 'required|in:option,contract_signed,ready_for_delivery,delivered,cancelled',
            'delivery_date' => 'nullable|date',
            'delivery_time' => 'nullable',
            'notes' => 'nullable|string',
        ]);

        $sale->update($data);

        return redirect()->route('sales.show', $sale)->with('success','Verkoopdossier succesvol bijgewerkt.');
    }

    public function markAsDelivered(Sale $sale)
    {
        if ($sale->payment_status !== 'paid') {
            return back()->with('error','Betaling nog niet volledig voldaan.');
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

        // Move car back to "Verkoop klaar" stage if it exists
        $salesReadyStage = \App\Models\CarStage::where('name', 'LIKE', '%verkoop klaar%')->first();
        if ($salesReadyStage) {
            $sale->car->update(['stage_id' => $salesReadyStage->id]);
        }

        return redirect()->route('sales.index')->with('success','Verkoop geannuleerd. Auto is teruggezet naar "Verkoop klaar".');
    }
}
