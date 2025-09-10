<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Car;
use App\Models\CarAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index()
    {
        $employees = Employee::with(['currentAssignments.car'])
            ->orderBy('name')
            ->get();

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $positions = [
            'Hoofdmonteur',
            'Monteur', 
            'APK-keurder',
            'Leerling',
            'Voorman',
            'Specialist',
        ];

        $specializations = [
            'APK',
            'Diagnose',
            'Motor',
            'Transmissie',
            'Remmen',
            'Uitlaat',
            'Banden',
            'Elektronica',
            'Airco',
            'Carrosserie',
            'Lakwerk',
            'Onderhoud',
            'Controle',
        ];

        return view('employees.create', compact('positions', 'specializations'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:100',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string|max:100',
            'create_user_account' => 'boolean',
            'user_role' => 'required_if:create_user_account,1|in:employee,manager',
        ]);

        // Create employee first
        $employee = Employee::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'position' => $validated['position'],
            'specializations' => $validated['specializations'] ?? [],
            'role' => $validated['user_role'] ?? 'employee',
        ]);

        // Create user account if requested
        if ($request->boolean('create_user_account')) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'] ?: $this->generateEmployeeEmail($employee),
                'password' => Hash::make('password123'), // Default password
                'company_id' => tenant_id(),
                'role' => $validated['user_role'],
                'active' => true,
            ]);

            $employee->update(['user_id' => $user->id]);

            $message = "Medewerker succesvol toegevoegd met login account! (Wachtwoord: password123)";
        } else {
            $message = "Medewerker succesvol toegevoegd!";
        }

        return redirect()->route('employees.index')->with('success', $message);
    }

    private function generateEmployeeEmail(Employee $employee): string
    {
        $cleanName = strtolower(str_replace(' ', '.', $employee->name));
        return $cleanName . '@' . $employee->company->subdomain . '.nl';
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        // Check if current user can view this employee
        $user = Auth::user();
        if ($user->canOnlyViewOwnWork() && $user->employee && $user->employee->id !== $employee->id) {
            abort(403, 'Je kunt alleen je eigen gegevens bekijken.');
        }

        $employee->load([
            'carAssignments.car',
            'currentAssignments.car.stage'
        ]);

        // Get available cars (not assigned to anyone)
        $availableCars = Car::whereDoesntHave('currentAssignment')
            ->with('stage')
            ->orderBy('license_plate')
            ->get();

        return view('employees.show', compact('employee', 'availableCars'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $positions = [
            'Hoofdmonteur',
            'Monteur', 
            'APK-keurder',
            'Leerling',
            'Voorman',
            'Specialist',
        ];

        $specializations = [
            'APK',
            'Diagnose',
            'Motor',
            'Transmissie',
            'Remmen',
            'Uitlaat',
            'Banden',
            'Elektronica',
            'Airco',
            'Carrosserie',
            'Lakwerk',
            'Onderhoud',
            'Controle',
        ];

        return view('employees.edit', compact('employee', 'positions', 'specializations'));
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:100',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string|max:100',
            'active' => 'boolean',
        ]);

        $employee->update($validated);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Medewerker succesvol bijgewerkt!');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee)
    {
        // Check if employee has active assignments
        if ($employee->currentAssignments()->exists()) {
            return redirect()->route('employees.index')
                ->with('error', 'Kan medewerker niet verwijderen: er zijn actieve auto-toewijzingen.');
        }

        $name = $employee->name;
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', "Medewerker {$name} succesvol verwijderd.");
    }

    /**
     * Assign a car to an employee.
     */
    public function assignCar(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'notes' => 'nullable|string|max:500',
            'estimated_completion' => 'nullable|date|after:today',
        ]);

        $car = Car::findOrFail($validated['car_id']);

        try {
            $assignment = $car->assignTo(
                $employee, 
                $validated['notes'] ?? null,
                $validated['estimated_completion'] ?? null
            );

            return redirect()->route('employees.show', $employee)
                ->with('success', "Auto {$car->license_plate} toegewezen aan {$employee->name}!");

        } catch (\Exception $e) {
            return redirect()->route('employees.show', $employee)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Complete a car assignment.
     */
    public function completeAssignment(Request $request, Employee $employee, CarAssignment $assignment)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $assignment->complete($validated['notes'] ?? null);

        return redirect()->route('employees.show', $employee)
            ->with('success', "Werk aan {$assignment->car->license_plate} afgerond!");
    }

    /**
     * Cancel a car assignment.
     */
    public function cancelAssignment(Request $request, Employee $employee, CarAssignment $assignment)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $assignment->cancel($validated['reason']);

        return redirect()->route('employees.show', $employee)
            ->with('success', "Toewijzing van {$assignment->car->license_plate} geannuleerd!");
    }
}
