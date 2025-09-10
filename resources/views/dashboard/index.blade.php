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
        
        <!-- Dag Agenda -->
        <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] hover-card mb-8">
            <div class="p-6 border-b border-[var(--border-light)]">
                <h2 class="text-xl font-bold text-[var(--text-primary)] flex items-center">
                    <i class="fas fa-calendar-day text-[var(--status-success)] mr-3"></i>
                    Vandaag - {{ \Carbon\Carbon::now()->format('d M Y') }}
                    <span class="ml-3 px-3 py-1 bg-[var(--status-success-light)] text-[var(--status-success)] text-sm rounded-full">
                        {{ $todayAppointments->count() }} afspra{{ $todayAppointments->count() !== 1 ? 'ken' : 'ak' }}
                    </span>
                </h2>
            </div>
            
            <div class="p-6">
                @if($todayAppointments->count() > 0)
                    <div class="space-y-3">
                        @foreach($todayAppointments as $appointment)
                            <div class="flex items-start p-4 bg-gradient-to-r from-[var(--status-success-light)] to-green-50 rounded-lg border-l-4 border-[var(--status-success)] hover:shadow-md transition-all duration-[var(--transition-normal)]">
                                <div class="flex-shrink-0 w-3 h-3 bg-[var(--status-success)] rounded-full mt-2 mr-4 animate-pulse"></div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-[var(--text-primary)]">
                                            {{ ucfirst($appointment->type) }} 
                                            @if($appointment->car)
                                                - {{ $appointment->car->brand }} {{ $appointment->car->model }} ({{ $appointment->car->license_plate }})
                                            @endif
                                        </h3>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-[var(--status-success)] bg-white px-3 py-1 rounded-full border border-[var(--status-success-border)]">
                                                {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                            </span>
                                            <span class="px-2 py-1 bg-[var(--status-success)] text-white text-xs rounded-full font-medium">
                                                VANDAAG
                                            </span>
                                        </div>
                                    </div>
                                    @if($appointment->customer || $appointment->customer_name)
                                        <p class="text-[var(--text-secondary)] text-sm mt-2 flex items-center">
                                            <i class="fas fa-user text-[var(--status-success)] mr-2"></i>
                                            {{ $appointment->customer ? $appointment->customer->name : $appointment->customer_name }}
                                        </p>
                                    @endif
                                    @if($appointment->notes)
                                        <p class="text-[var(--text-secondary)] text-sm mt-1 flex items-center">
                                            <i class="fas fa-sticky-note text-[var(--status-success)] mr-2"></i>
                                            {{ $appointment->notes }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-[var(--status-success-light)] rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-check text-[var(--status-success)] text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-[var(--text-primary)] mb-2">Geen afspraken vandaag</h3>
                        <p class="text-[var(--text-secondary)]">Je hebt een rustige dag! Perfect om andere taken af te handelen.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Weekagenda -->
        <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] hover-card">
            <div class="p-6 border-b border-[var(--border-light)]">
                <h2 class="text-xl font-bold text-[var(--text-primary)] flex items-center">
                    <i class="fas fa-calendar-alt text-[var(--status-info)] mr-3"></i>
                    Weekoverzicht
                    <span class="ml-3 px-3 py-1 bg-[var(--status-info-light)] text-[var(--status-info)] text-sm rounded-full">
                        {{ $weekAppointments->count() }} totaal
                    </span>
                </h2>
            </div>
            
            <div class="p-6">
                @if($weekAppointments->count() > 0)
                    <div class="space-y-4">
                        @foreach($weekAppointments as $appointment)
                            @php
                                $isToday = \Carbon\Carbon::parse($appointment->date)->isToday();
                                $appointmentDate = \Carbon\Carbon::parse($appointment->date . ' ' . $appointment->time);
                            @endphp
                            <div class="flex items-start p-4 {{ $isToday ? 'bg-gradient-to-r from-[var(--status-success-light)] to-green-50 border-l-4 border-[var(--status-success)]' : 'bg-gradient-to-r from-[var(--gradient-blue-start)] to-[var(--gradient-blue-end)] border border-[var(--status-info-border)]' }} rounded-lg hover:shadow-md transition-all duration-[var(--transition-normal)]">
                                <div class="flex-shrink-0 w-4 h-4 {{ $isToday ? 'bg-[var(--status-success)] animate-pulse' : 'bg-[var(--status-info)]' }} rounded-full mt-1 mr-4"></div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-[var(--text-primary)]">
                                            {{ ucfirst($appointment->type) }} 
                                            @if($appointment->car)
                                                - {{ $appointment->car->brand }} {{ $appointment->car->model }}
                                            @endif
                                        </h3>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-[var(--text-secondary)] bg-[var(--background-card)] px-2 py-1 rounded">
                                                {{ $appointment->day_label }}
                                            </span>
                                            @if($isToday)
                                                <span class="px-2 py-1 bg-[var(--status-success)] text-white text-xs rounded-full font-medium animate-pulse">
                                                    VANDAAG
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($appointment->customer || $appointment->customer_name)
                                        <p class="text-[var(--text-secondary)] text-sm mt-1 flex items-center">
                                            <i class="fas fa-user {{ $isToday ? 'text-[var(--status-success)]' : 'text-[var(--text-tertiary)]' }} mr-1"></i>
                                            {{ $appointment->customer ? $appointment->customer->name : $appointment->customer_name }}
                                        </p>
                                    @endif
                                    @if($appointment->notes)
                                        <p class="text-[var(--text-secondary)] text-sm mt-1 flex items-center">
                                            <i class="fas fa-sticky-note {{ $isToday ? 'text-[var(--status-success)]' : 'text-[var(--text-tertiary)]' }} mr-1"></i>
                                            {{ $appointment->notes }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Voorbeeld agenda items als er geen echte data is -->
                    {{-- <div class="space-y-4">
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
                        </div> --}}
                        
                        {{-- <div class="flex items-start p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-100 hover:shadow-md transition-all duration-200">
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
                        </div> --}}
{{--                         
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
                        </div> --}}
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

@keyframes fadeInUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes shimmer {
    0% { background-position: -200px 0; }
    100% { background-position: calc(200px + 100%) 0; }
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
.hover-card:nth-child(6) { animation-delay: 0.6s; }

/* Today appointment special styling */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Day agenda card */
.hover-card:nth-child(5) {
    animation: fadeInUp 0.6s ease-out;
    animation-delay: 0.4s;
    animation-fill-mode: both;
}

/* Week agenda card */
.hover-card:nth-child(6) {
    animation: fadeInUp 0.6s ease-out;
    animation-delay: 0.5s;
    animation-fill-mode: both;
}

/* Today badge animation */
.animate-pulse {
    animation: pulse 2s ease-in-out infinite;
}

/* Gradient animation for today items */
.bg-gradient-to-r.from-\[var\(--status-success-light\)\] {
    background: linear-gradient(90deg, var(--status-success-light) 0%, #f0fdf4 50%, var(--status-success-light) 100%);
    background-size: 200% 100%;
    animation: shimmer 3s ease-in-out infinite;
}
</style>
@endsection
