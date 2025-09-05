@extends('layouts.app')

@section('content')
<div class="bg-[var(--background-main)] min-h-full">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[var(--text-primary)] mb-2">🏠 Dashboard</h1>
            <p class="text-[var(--text-secondary)]">Welkom terug! Hier is je overzicht van vandaag</p>
        </div>
        
        <!-- Statistieken Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6 hover-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-[var(--text-secondary)]">Totaal Auto's</p>
                        <p class="text-3xl font-bold text-[var(--text-primary)]">{{ $totalCars }}</p>
                    </div>
                    <div class="p-3 bg-[var(--status-info-light)] rounded-full">
                        <i class="fas fa-car text-[var(--status-info)] text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6 hover-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-[var(--text-secondary)]">Auto's in Intake</p>
                        <p class="text-3xl font-bold text-[var(--text-primary)]">{{ $intakeCars }}</p>
                    </div>
                    <div class="p-3 bg-[var(--status-success-light)] rounded-full">
                        <i class="fas fa-download text-[var(--status-success)] text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6 hover-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-[var(--text-secondary)]">Advertenties Live</p>
                        <p class="text-3xl font-bold text-[var(--text-primary)]">{{ $liveAds }}</p>
                    </div>
                    <div class="p-3 bg-[var(--status-special-light)] rounded-full">
                        <i class="fas fa-bullhorn text-[var(--status-special)] text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6 hover-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-[var(--text-secondary)]">Open Reparaties</p>
                        <p class="text-3xl font-bold text-[var(--text-primary)]">{{ $openRepairs }}</p>
                    </div>
                    <div class="p-3 bg-[var(--status-danger-light)] rounded-full">
                        <i class="fas fa-wrench text-[var(--status-danger)] text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Weekagenda -->
        <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] hover-card">
            <div class="p-6 border-b border-[var(--border-light)]">
                <h2 class="text-xl font-bold text-[var(--text-primary)] flex items-center">
                    <i class="fas fa-calendar-alt text-[var(--status-info)] mr-3"></i>
                    Weekagenda
                </h2>
            </div>
            
            <div class="p-6">
                @if($weekAppointments->count() > 0)
                    <div class="space-y-4">
                        @foreach($weekAppointments as $appointment)
                            <div class="flex items-start p-4 bg-gradient-to-r from-[var(--gradient-blue-start)] to-[var(--gradient-blue-end)] rounded-lg border border-[var(--status-info-border)] hover:shadow-md transition-all duration-[var(--transition-normal)]">
                                <div class="flex-shrink-0 w-4 h-4 bg-[var(--status-info)] rounded-full mt-1 mr-4"></div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-[var(--text-primary)]">
                                            {{ ucfirst($appointment->type) }} 
                                            @if($appointment->car)
                                                {{ $appointment->car->brand }} {{ $appointment->car->model }}
                                            @endif
                                        </h3>
                                        <span class="text-sm text-[var(--text-secondary)] bg-[var(--background-card)] px-2 py-1 rounded">{{ $appointment->day_label }}</span>
                                    </div>
                                    @if($appointment->customer || $appointment->customer_name)
                                        <p class="text-[var(--text-secondary)] text-sm mt-1 flex items-center">
                                            <i class="fas fa-user text-[var(--text-tertiary)] mr-1"></i>
                                            {{ $appointment->customer ? $appointment->customer->name : $appointment->customer_name }}
                                        </p>
                                    @endif
                                    @if($appointment->notes)
                                        <p class="text-[var(--text-secondary)] text-sm mt-1 flex items-center">
                                            <i class="fas fa-sticky-note text-[var(--text-tertiary)] mr-1"></i>
                                            {{ $appointment->notes }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Voorbeeld agenda items als er geen echte data is -->
                    <div class="space-y-4">
                        <div class="flex items-start p-4 bg-gradient-to-r from-[var(--gradient-blue-start)] to-[var(--gradient-blue-end)] rounded-lg border border-[var(--status-info-border)] hover:shadow-md transition-all duration-[var(--transition-normal)]">
                            <div class="flex-shrink-0 w-4 h-4 bg-[var(--status-info)] rounded-full mt-1 mr-4"></div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-[var(--text-primary)]">Proefrit BMW 320i</h3>
                                    <span class="text-sm text-[var(--text-secondary)] bg-[var(--background-card)] px-2 py-1 rounded">Vandaag 14:00</span>
                                </div>
                                <p class="text-[var(--text-secondary)] text-sm mt-1 flex items-center">
                                    <i class="fas fa-user text-[var(--text-tertiary)] mr-1"></i>
                                    P. van der Berg
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-100 hover:shadow-md transition-all duration-200">
                            <div class="flex-shrink-0 w-4 h-4 bg-green-500 rounded-full mt-1 mr-4"></div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-900">Aflevering Audi A3</h3>
                                    <span class="text-sm text-gray-500 bg-white px-2 py-1 rounded">Morgen 10:30</span>
                                </div>
                                <p class="text-gray-600 text-sm mt-1 flex items-center">
                                    <i class="fas fa-user text-gray-400 mr-1"></i>
                                    J. Bakker
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg border border-orange-100 hover:shadow-md transition-all duration-200">
                            <div class="flex-shrink-0 w-4 h-4 bg-orange-500 rounded-full mt-1 mr-4"></div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-900">Werkplaats: Golf onderdelen</h3>
                                    <span class="text-sm text-gray-500 bg-white px-2 py-1 rounded">Vrijdag</span>
                                </div>
                                <p class="text-gray-600 text-sm mt-1 flex items-center">
                                    <i class="fas fa-truck text-gray-400 mr-1"></i>
                                    Verwachte levering
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Custom animations */
@keyframes slideIn {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.hover-card {
    animation: slideIn 0.5s ease-out;
}

/* Hover effects */
.hover-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

/* Staggered animation delay */
.hover-card:nth-child(1) { animation-delay: 0.1s; }
.hover-card:nth-child(2) { animation-delay: 0.2s; }
.hover-card:nth-child(3) { animation-delay: 0.3s; }
.hover-card:nth-child(4) { animation-delay: 0.4s; }
.hover-card:nth-child(5) { animation-delay: 0.5s; }
</style>
@endsection
