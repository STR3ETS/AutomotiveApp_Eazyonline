@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-8 text-gray-900">Actieve Verkoop & Oplevering</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Proefritten deze week -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-car text-blue-600 mr-3"></i>
                    Geplande Proefritten
                    <span class="ml-2 bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded-full">{{ $testDrives->count() }}</span>
                </h2>
            </div>
            <div class="p-6">
                @if($testDrives->count() > 0)
                    <div class="space-y-4">
                        @foreach($testDrives as $appointment)
                            <div class="flex items-center p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex-shrink-0 w-3 h-3 bg-blue-500 rounded-full mr-4"></div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">
                                                @if($appointment->car)
                                                    {{ $appointment->car->brand }} {{ $appointment->car->model }}
                                                @endif
                                            </h3>
                                            <p class="text-gray-600 text-sm">
                                                Klant: {{ $appointment->customer ? $appointment->customer->name : $appointment->customer_name }}
                                            </p>
                                            @if($appointment->notes)
                                                <p class="text-gray-500 text-sm">{{ $appointment->notes }}</p>
                                            @endif
                                        </div>
                                        <span class="text-sm font-medium text-blue-700">{{ $appointment->day_label }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-calendar-times text-4xl mb-4 text-gray-300"></i>
                        <p>Geen proefritten gepland deze week</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Verkochte auto's (nog niet opgeleverd) -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-handshake text-green-600 mr-3"></i>
                    Verkochte Auto's
                    <span class="ml-2 bg-green-100 text-green-800 text-sm px-2 py-1 rounded-full">{{ $soldCars->count() }}</span>
                </h2>
                <p class="text-sm text-gray-500 mt-1">Wachten op oplevering</p>
            </div>
            <div class="p-6">
                @if($soldCars->count() > 0)
                    <div class="space-y-4">
                        @foreach($soldCars as $sale)
                            <div class="flex items-center p-4 bg-green-50 rounded-lg border border-green-200">
                                <div class="flex-shrink-0 w-3 h-3 bg-green-500 rounded-full mr-4"></div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">
                                                @if($sale->car)
                                                    {{ $sale->car->brand }} {{ $sale->car->model }}
                                                @endif
                                            </h3>
                                            <p class="text-gray-600 text-sm">
                                                Verkocht aan: {{ $sale->customer ? $sale->customer->name : 'Onbekende klant' }}
                                            </p>
                                            <p class="text-gray-500 text-sm">
                                                Verkoopdatum: {{ \Carbon\Carbon::parse($sale->sold_at)->format('d-m-Y') }}
                                            </p>
                                        </div>
                                        <span class="text-lg font-bold text-green-700">€{{ number_format($sale->sale_price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-shopping-cart text-4xl mb-4 text-gray-300"></i>
                        <p>Geen verkochte auto's wachtend op oplevering</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Geplande ophalingen/afleveringen -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-truck text-orange-600 mr-3"></i>
                    Geplande Afleveringen
                    <span class="ml-2 bg-orange-100 text-orange-800 text-sm px-2 py-1 rounded-full">{{ $deliveries->count() }}</span>
                </h2>
            </div>
            <div class="p-6">
                @if($deliveries->count() > 0)
                    <div class="space-y-4">
                        @foreach($deliveries as $appointment)
                            <div class="flex items-center p-4 bg-orange-50 rounded-lg border border-orange-200">
                                <div class="flex-shrink-0 w-3 h-3 bg-orange-500 rounded-full mr-4"></div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">
                                                @if($appointment->car)
                                                    {{ $appointment->car->brand }} {{ $appointment->car->model }}
                                                @endif
                                            </h3>
                                            <p class="text-gray-600 text-sm">
                                                Aflevering aan: {{ $appointment->customer ? $appointment->customer->name : $appointment->customer_name }}
                                            </p>
                                            @if($appointment->notes)
                                                <p class="text-gray-500 text-sm">{{ $appointment->notes }}</p>
                                            @endif
                                        </div>
                                        <span class="text-sm font-medium text-orange-700">{{ $appointment->day_label }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-truck-loading text-4xl mb-4 text-gray-300"></i>
                        <p>Geen afleveringen gepland deze week</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Auto's klaar voor verkoop -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-tag text-purple-600 mr-3"></i>
                    Klaar voor Verkoop
                    <span class="ml-2 bg-purple-100 text-purple-800 text-sm px-2 py-1 rounded-full">{{ $readyForSale->count() }}</span>
                </h2>
                <p class="text-sm text-gray-500 mt-1">Auto's in "Verkoop klaar" fase</p>
            </div>
            <div class="p-6">
                @if($readyForSale->count() > 0)
                    <div class="space-y-4">
                        @foreach($readyForSale as $car)
                            <div class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-200">
                                <div class="flex-shrink-0 w-3 h-3 bg-purple-500 rounded-full mr-4"></div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">
                                                {{ $car->brand }} {{ $car->model }}
                                            </h3>
                                            <p class="text-gray-600 text-sm">
                                                {{ $car->license_plate ?? $car->kenteken }} • {{ $car->year ?? 'Onbekend jaar' }}
                                            </p>
                                            <p class="text-gray-500 text-sm">
                                                Vraagprijs: €{{ number_format($car->price ?? 0, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <span class="text-sm px-3 py-1 bg-purple-100 text-purple-700 rounded-full font-medium">
                                            Verkoop klaar
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-car-side text-4xl mb-4 text-gray-300"></i>
                        <p>Geen auto's klaar voor verkoop</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
