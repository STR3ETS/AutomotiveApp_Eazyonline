@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-8 text-gray-900">Dashboard</h1>
    
    <!-- Statistieken Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-car text-xl"></i>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">Totaal Auto's</span>
                    <div class="text-3xl font-extrabold text-gray-900">{{ $totalCars }}</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-download text-xl"></i>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">Auto's in Intake</span>
                    <div class="text-3xl font-extrabold text-gray-900">{{ $intakeCars }}</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-bullhorn text-xl"></i>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">Advertenties Live</span>
                    <div class="text-3xl font-extrabold text-gray-900">{{ $liveAds }}</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-wrench text-xl"></i>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">Open Reparaties</span>
                    <div class="text-3xl font-extrabold text-gray-900">{{ $openRepairs }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Weekagenda -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-calendar-alt text-blue-600 mr-3"></i>
                Weekagenda
            </h2>
        </div>
        
        <div class="p-6">
            @if($weekAppointments->count() > 0)
                <div class="space-y-4">
                    @foreach($weekAppointments as $appointment)
                        <div class="flex items-start p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-shrink-0 w-4 h-4 bg-blue-500 rounded-full mt-1 mr-4"></div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-900">
                                        {{ ucfirst($appointment->type) }} 
                                        @if($appointment->car)
                                            {{ $appointment->car->brand }} {{ $appointment->car->model }}
                                        @endif
                                    </h3>
                                    <span class="text-sm text-gray-500">{{ $appointment->day_label }}</span>
                                </div>
                                @if($appointment->customer || $appointment->customer_name)
                                    <p class="text-gray-600 text-sm mt-1">
                                        Klant: {{ $appointment->customer ? $appointment->customer->name : $appointment->customer_name }}
                                    </p>
                                @endif
                                @if($appointment->notes)
                                    <p class="text-gray-500 text-sm mt-1">{{ $appointment->notes }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Voorbeeld agenda items als er geen echte data is -->
                <div class="space-y-4">
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex-shrink-0 w-4 h-4 bg-blue-500 rounded-full mt-1 mr-4"></div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-900">Proefrit BMW 320i</h3>
                                <span class="text-sm text-gray-500">Vandaag 14:00</span>
                            </div>
                            <p class="text-gray-600 text-sm mt-1">Klant: P. van der Berg</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex-shrink-0 w-4 h-4 bg-green-500 rounded-full mt-1 mr-4"></div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-900">Aflevering Audi A3</h3>
                                <span class="text-sm text-gray-500">Morgen 10:30</span>
                            </div>
                            <p class="text-gray-600 text-sm mt-1">Klant: J. Bakker</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex-shrink-0 w-4 h-4 bg-orange-500 rounded-full mt-1 mr-4"></div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-900">Werkplaats: Golf onderdelen</h3>
                                <span class="text-sm text-gray-500">Vrijdag</span>
                            </div>
                            <p class="text-gray-600 text-sm mt-1">Verwachte levering</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
