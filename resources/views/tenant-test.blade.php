@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-3xl font-bold mb-6">Multi-Tenant Test Dashboard</h1>
    
    @if($currentCompany ?? null)
        <div class="bg-green-50 border border-green-200 p-4 rounded-lg mb-6">
            <h2 class="text-lg font-semibold text-green-800">Current Company</h2>
            <p><strong>Name:</strong> {{ $currentCompany->name }}</p>
            <p><strong>Subdomain:</strong> {{ $currentCompany->subdomain }}</p>
            <p><strong>Primary Color:</strong> <span style="color: {{ $currentCompany->primary_color }}">{{ $currentCompany->primary_color }}</span></p>
        </div>
    @else
        <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-6">
            <p><strong>No tenant detected</strong> (localhost development mode)</p>
            <p>Session company_id: {{ session('current_company_id') ?? 'none' }}</p>
        </div>
    @endif

    <!-- Company Switcher -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">Available Companies - Click to Switch</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach(\App\Models\Company::withCount('cars')->get() as $company)
                <div class="border rounded-lg p-4 {{ session('current_company_id') == $company->id ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                    <div class="flex items-center mb-2">
                        <div class="w-4 h-4 rounded mr-2" style="background-color: {{ $company->primary_color }}"></div>
                        <h4 class="font-medium">{{ $company->name }}</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ $company->subdomain }}.localhost</p>
                    <p class="text-sm text-gray-600 mb-3">{{ $company->cars_count }} cars</p>
                    @if(session('current_company_id') == $company->id)
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Active</span>
                    @else
                        <a href="{{ route('tenant.set', $company->id) }}" 
                           class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">Switch To</a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold">Companies</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $stats['companies'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold">Cars (Current Tenant)</h3>
            <p class="text-3xl font-bold text-green-600">{{ $stats['cars'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold">Customers</h3>
            <p class="text-3xl font-bold text-purple-600">{{ $stats['customers'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold">Appointments</h3>
            <p class="text-3xl font-bold text-orange-600">{{ $stats['appointments'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Cars for Current Tenant (With Global Scope)</h3>
            <p class="text-sm text-gray-600">This should show only cars for the current company</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">License Plate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Brand</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Model</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($cars as $car)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium">{{ $car->license_plate }}</td>
                        <td class="px-6 py-4 text-sm">{{ $car->brand }}</td>
                        <td class="px-6 py-4 text-sm">{{ $car->model }}</td>
                        <td class="px-6 py-4 text-sm">{{ $car->year }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ $car->status }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $car->company->name ?? 'No Company' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No cars found for current tenant</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">All Cars (Without Global Scope)</h3>
            <p class="text-sm text-gray-600">This shows ALL cars from ALL companies for comparison</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">License Plate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Brand</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Model</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($allCars as $car)
                    <tr class="{{ session('current_company_id') == $car->company_id ? 'bg-green-50' : '' }}">
                        <td class="px-6 py-4 text-sm font-medium">{{ $car->license_plate }}</td>
                        <td class="px-6 py-4 text-sm">{{ $car->brand }}</td>
                        <td class="px-6 py-4 text-sm">{{ $car->model }}</td>
                        <td class="px-6 py-4 text-sm">{{ $car->year }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ $car->status }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="font-semibold">{{ $car->company->name ?? 'No Company' }}</span>
                            <span class="text-xs text-gray-500">(ID: {{ $car->company_id }})</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">All Companies (Admin View)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subdomain</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Primary Color</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cars Count</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($companies as $company)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium">{{ $company->name }}</td>
                        <td class="px-6 py-4 text-sm font-mono">{{ $company->subdomain }}</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 rounded mr-2" style="background-color: {{ $company->primary_color }}"></div>
                                {{ $company->primary_color }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $company->cars_count }}</td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="/css/company/{{ $company->subdomain }}.css" target="_blank" 
                               class="text-blue-600 hover:text-blue-800">View CSS</a>
                            <a href="/set-tenant/{{ $company->id }}" 
                               class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">Switch To</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
