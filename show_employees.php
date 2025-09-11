<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Employee;
use App\Models\Company;

echo "=== EMPLOYEE SEEDER RESULT ===\n\n";

$companies = Company::with(['users' => function($query) {
    $query->whereHas('employee');
}])->get();

foreach ($companies as $company) {
    echo "🏢 {$company->name} ({$company->subdomain}):\n";
    
    $employees = Employee::where('company_id', $company->id)->get();
    
    foreach ($employees as $employee) {
        $roleIcon = match($employee->role) {
            'owner' => '👨‍💼',
            'manager' => '👨‍🔧',
            'employee' => '🔧',
            default => '👤'
        };
        
        echo "   {$roleIcon} {$employee->name} - {$employee->position} ({$employee->role})\n";
        echo "       📧 {$employee->email}\n";
        echo "       🛠️  " . implode(', ', $employee->specializations ?? []) . "\n";
    }
    echo "\n";
}

echo "📊 SUMMARY:\n";
echo "- Total Companies: " . Company::count() . "\n";
echo "- Total Employees: " . Employee::count() . "\n";
echo "- Total Users: " . \App\Models\User::count() . "\n";
