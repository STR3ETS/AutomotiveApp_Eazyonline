@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-full">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">📅 Agenda deze week</h1>
            <p class="text-gray-600">Beheer je afspraken en werkplaats planning</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl flex items-center">
                <i class="fa-solid fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- New Appointment Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 hover-card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fa-solid fa-plus-circle text-blue-600 mr-2"></i>
                Nieuwe Afspraak
            </h2>
            <form method="POST" action="{{ route('agenda.store') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Auto</label>
                    <select name="car_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                        <option value="">Kies auto...</option>
                        @foreach($cars as $car)
                            <option value="{{ $car->id }}">({{ $car->status }}) {{ $car->license_plate ?? $car->kenteken }} - {{ $car->brand ?? $car->merk }} {{ $car->model }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                        <option value="proefrit">🚗 Proefrit</option>
                        <option value="aflevering">🎉 Aflevering</option>
                        <option value="werkplaats">🔧 Werkplaats</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Datum</label>
                    <input type="date" name="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tijd</label>
                    <input type="time" name="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Klant</label>
                    <select name="customer_id" id="customer_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" onchange="toggleCustomerInput()">
                        <option value="">Bestaande klant...</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                        <option value="new">+ Nieuwe klant</option>
                    </select>
                </div>
                <div id="new_customer_input" style="display: none;">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nieuwe klant naam</label>
                    <input type="text" name="customer_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Notities</label>
                    <input type="text" name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Optionele notities...">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-plus mr-2"></i>Afspraak Toevoegen
                    </button>
                </div>
            </form>
        </div>

        <!-- Appointments List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover-card">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">📋 Komende Afspraken</h2>
            </div>
            @forelse($appointments as $appointment)
                <div class="border-b border-gray-100 last:border-b-0 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200">
                    <div class="flex items-center gap-4 p-6">
                        <!-- Type Badge -->
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{
                                $appointment->type === 'proefrit' ? 'bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800' :
                                ($appointment->type === 'aflevering' ? 'bg-gradient-to-r from-green-100 to-green-200 text-green-800' : 'bg-gradient-to-r from-orange-100 to-orange-200 text-orange-800')
                            }}">
                                @if($appointment->type === 'proefrit')
                                    🚗 Proefrit
                                @elseif($appointment->type === 'aflevering')
                                    🎉 Aflevering
                                @else
                                    🔧 Werkplaats
                                @endif
                            </span>
                        </div>
                        
                        <!-- Date & Time -->
                        <div class="flex-shrink-0 bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($appointment->date)->format('d') }}</div>
                            <div class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($appointment->date)->format('M') }}</div>
                            <div class="text-xs font-medium text-blue-600">{{ \Carbon\Carbon::parse($appointment->time)->format("H:i") }}</div>
                        </div>
                        
                        <!-- Customer Info -->
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 flex items-center">
                                <i class="fa-solid fa-user text-gray-400 mr-2"></i>
                                {{ $appointment->customer ? $appointment->customer->name : $appointment->customer_name }}
                            </div>
                            <div class="text-sm text-gray-600 flex items-center mt-1">
                                <i class="fa-solid fa-car text-gray-400 mr-2"></i>
                                @if($appointment->car)
                                    {{ $appointment->car->license_plate ?? $appointment->car->kenteken }} - {{ $appointment->car->brand ?? $appointment->car->merk }} {{ $appointment->car->model }}
                                @else
                                    <span class="text-red-500">Auto niet gevonden</span>
                                @endif
                            </div>
                            @if($appointment->notes)
                                <div class="text-sm text-gray-500 flex items-center mt-1">
                                    <i class="fa-solid fa-sticky-note text-gray-400 mr-2"></i>
                                    {{ $appointment->notes }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex-shrink-0">
                            <form method="POST" action="{{ route('agenda.destroy', $appointment->id) }}" onsubmit="return confirm('Weet je zeker dat je deze afspraak wilt verwijderen?')">
                                @csrf
                                                    @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium hover:bg-red-200 transition-all duration-200">
                                    <i class="fa-solid fa-trash mr-1"></i>
                                    Verwijder
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="text-6xl mb-4">📅</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Geen afspraken gepland</h3>
                    <p class="text-gray-600">Voeg je eerste afspraak toe om te beginnen.</p>
                </div>
            @endforelse
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
    from { 
        transform: translateY(30px); 
        opacity: 0; 
    }
    to { 
        transform: translateY(0); 
        opacity: 1; 
    }
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

/* Form animations */
.hover-card form input,
.hover-card form select {
    transition: all 0.2s ease;
}

.hover-card form input:focus,
.hover-card form select:focus {
    transform: scale(1.02);
}

/* Staggered animation delay */
.hover-card:nth-child(1) { animation-delay: 0.1s; }
.hover-card:nth-child(2) { animation-delay: 0.2s; }
.hover-card:nth-child(3) { animation-delay: 0.3s; }
</style>

<script>
function toggleCustomerInput() {
    const select = document.getElementById('customer_select');
    const input = document.getElementById('new_customer_input');
    
    if (select.value === 'new') {
        input.style.display = 'block';
        input.querySelector('input').required = true;
    } else {
        input.style.display = 'none';
        input.querySelector('input').required = false;
    }
}
</script>
@endsection