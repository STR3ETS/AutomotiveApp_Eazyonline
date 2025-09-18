@extends('layouts.app')

@section('content')
<div class="bg-[var(--background-main)] min-h-full">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header with Add Button -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[var(--text-primary)]">Verkochte Auto's</h1>
                <p class="text-[var(--text-secondary)] mt-1">Overzicht van alle verkochte voertuigen</p>
            </div>
            
            <!-- Stats Cards -->
            <div class="flex gap-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center min-w-[120px]">
                    <div class="text-2xl font-bold text-[var(--status-success)]">{{ $stats['total_cars'] }}</div>
                    <div class="text-sm text-[var(--text-secondary)]">Verkocht</div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center min-w-[120px]">
                    <div class="text-2xl font-bold text-[var(--status-info)]">€{{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
                    <div class="text-sm text-[var(--text-secondary)]">Omzet</div>
                </div>
                
                @if($stats['total_profit'] > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center min-w-[120px]">
                    <div class="text-2xl font-bold text-[var(--status-success)]">€{{ number_format($stats['total_profit'], 0, ',', '.') }}</div>
                    <div class="text-sm text-[var(--text-secondary)]">Winst</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('sold-cars.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Zoeken</label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Kenteken, merk, model, klant..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Van datum</label>
                    <input type="date" 
                           name="date_from" 
                           value="{{ request('date_from') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tot datum</label>
                    <input type="date" 
                           name="date_to" 
                           value="{{ request('date_to') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- Brand Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Merk</label>
                    <select name="brand" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Alle merken</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand }}" @selected(request('brand') == $brand)>{{ $brand }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Submit -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-[var(--primary)] hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-search"></i>
                        Filteren
                    </button>
                </div>
            </form>
        </div>

        <!-- Sold Cars Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($soldCars->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verkoop Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verkocht Op</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Winst</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($soldCars as $soldCar)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($soldCar->primary_image_url)
                                                <img src="{{ $soldCar->primary_image_url }}" alt="{{ $soldCar->brand }} {{ $soldCar->model }}" class="w-12 h-12 rounded-lg object-cover mr-4">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center mr-4">
                                                    <i class="fa-solid fa-car text-gray-400"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-[var(--text-primary)]">
                                                    {{ $soldCar->license_plate }}
                                                </div>
                                                <div class="text-sm text-[var(--text-secondary)]">
                                                    {{ $soldCar->brand }} {{ $soldCar->model }} ({{ $soldCar->year }})
                                                </div>
                                                <div class="text-sm text-[var(--text-tertiary)]">
                                                    {{ number_format($soldCar->mileage) }} km
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-[var(--text-primary)]">{{ $soldCar->customer_name }}</div>
                                        @if($soldCar->customer_email)
                                            <div class="text-sm text-[var(--text-secondary)]">{{ $soldCar->customer_email }}</div>
                                        @endif
                                        @if($soldCar->customer_phone)
                                            <div class="text-sm text-[var(--text-tertiary)]">{{ $soldCar->customer_phone }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">
                                            <div class="font-semibold text-[var(--text-primary)]">€ {{ number_format($soldCar->sale_price, 2) }}</div>
                                            @if($soldCar->deposit_amount)
                                                <div class="text-[var(--status-success)] text-xs">
                                                    Aanbetaling: € {{ number_format($soldCar->deposit_amount, 2) }}
                                                </div>
                                            @endif
                                            <div class="text-[var(--text-tertiary)] text-xs mt-1">
                                                Vraagprijs: € {{ number_format($soldCar->original_price, 2) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-[var(--text-primary)]">
                                        <div>{{ $soldCar->sold_at->format('d-m-Y') }}</div>
                                        <div class="text-xs text-[var(--text-tertiary)]">{{ $soldCar->sold_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($soldCar->purchase_price)
                                            @php
                                                $profit = $soldCar->profit;
                                                $profitMargin = $soldCar->profit_margin;
                                            @endphp
                                            <div class="text-sm">
                                                <div class="font-semibold @if($profit >= 0) text-[var(--status-success)] @else text-[var(--status-danger)] @endif">
                                                    € {{ number_format($profit, 2) }}
                                                </div>
                                                <div class="text-xs text-[var(--text-tertiary)]">
                                                    {{ number_format($profitMargin, 1) }}% marge
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-[var(--text-tertiary)] text-sm">Geen inkoopprijs</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('sold-cars.show', $soldCar) }}" 
                                               class="inline-flex items-center px-3 py-1 text-xs font-medium text-[var(--primary)] bg-blue-50 hover:bg-blue-100 rounded-full transition-colors duration-200">
                                                <i class="fa-solid fa-eye mr-1"></i>
                                                Details
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $soldCars->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fa-solid fa-car text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Geen verkochte auto's gevonden</h3>
                    <p class="text-gray-500">
                        @if(request()->hasAny(['search', 'date_from', 'date_to', 'brand']))
                            Probeer je filters aan te passen om meer resultaten te zien.
                        @else
                            Er zijn nog geen auto's verkocht.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
