@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Selecteer je bedrijf</h1>
        <p class="text-lg text-gray-600">Kies het bedrijf waarmee je wilt werken</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
        @foreach($companies as $company)
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-4" style="background-color: {{ $company->primary_color }}"></div>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 rounded-full mr-3" style="background-color: {{ $company->primary_color }}"></div>
                        <h3 class="text-xl font-semibold text-gray-800">{{ $company->name }}</h3>
                    </div>
                    
                    <p class="text-gray-600 mb-4">{{ $company->subdomain }}.localhost</p>
                    
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-sm text-gray-500">{{ $company->cars_count ?? 0 }} auto's</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Actief</span>
                    </div>
                    
                    <a href="{{ route('company.dashboard', $company->id) }}" 
                       class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                        Ga naar {{ $company->name }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Test panel -->
    <div class="mt-12 max-w-4xl mx-auto">
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Test & Debug</h3>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('tenant.test') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                    Tenant Test Dashboard
                </a>
                @foreach($companies as $company)
                    <a href="{{ route('company.autos.index', $company->id) }}" 
                       class="px-4 py-2 rounded text-white"
                       style="background-color: {{ $company->primary_color }}">
                        {{ $company->name }} - Auto's
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
