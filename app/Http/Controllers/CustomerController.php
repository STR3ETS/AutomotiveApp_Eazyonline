<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * GET /customers
     * Toon overzicht van klanten + formulier om nieuwe toe te voegen
     */
    public function index()
    {
        $customers = Customer::latest()->paginate(20);
        
        return view('customers.index', compact('customers'));
    }

    /**
     * POST /customers
     * Nieuwe klant toevoegen
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Klant succesvol toegevoegd!');
    }

    /**
     * PUT /customers/{id}
     * Klant bijwerken
     */
    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $customer->update($data);

        return redirect()->route('customers.index')->with('success', 'Klant succesvol bijgewerkt!');
    }

    /**
     * DELETE /customers/{id}
     * Klant verwijderen
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Klant verwijderd.');
    }
}
