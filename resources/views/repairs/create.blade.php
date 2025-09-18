@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-full">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header with Navigation -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-4">
                    <a href="{{ route('repairs.index') }}" class="hover:text-blue-600 transition-colors">
                        <i class="fa-solid fa-wrench mr-1"></i>
                        Reparaties
                    </a>
                    <i class="fa-solid fa-chevron-right text-gray-400"></i>
                    <span class="text-gray-900 font-medium">Nieuwe Reparatie</span>
                </nav>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    ✨ Nieuwe Reparatie Aanmaken
                </h1>
                <p class="text-gray-600 mt-2">Voeg een nieuwe reparatie toe aan het systeem</p>
            </div>
            <a href="{{ route('repairs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Terug naar Overzicht
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-8 p-4 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 rounded-2xl shadow-sm">
                <div class="flex items-center mb-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-red-500 rounded-full flex items-center justify-center mr-3">
                        <i class="fa-solid fa-exclamation text-white text-sm"></i>
                    </div>
                    <span class="font-semibold">Er zijn fouten opgetreden:</span>
                </div>
                <ul class="list-disc pl-11 space-y-1">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Form Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fa-solid fa-plus-circle mr-3"></i>
                    Reparatie Details
                </h2>
                <p class="text-blue-100 mt-1">Vul alle benodigde informatie in voor de nieuwe reparatie</p>
            </div>

            <form method="POST" action="{{ route('repairs.store') }}" class="p-8">
                @csrf
                
                <!-- Auto Selection -->
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fa-solid fa-car text-blue-600 mr-2"></i>
                        Auto Selecteren
                    </label>
                    <select name="car_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                        <option value="">— Selecteer een auto —</option>
                        @foreach($cars as $car)
                            <option value="{{ $car->id }}" {{ old('car_id') == $car->id ? 'selected' : '' }}>
                                {{ $car->license_plate ?? '—' }} — {{ $car->brand ?? '' }} {{ $car->model ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 mt-2">Kies de auto waarvoor deze reparatie bedoeld is</p>
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fa-solid fa-file-text text-green-600 mr-2"></i>
                        Reparatie Omschrijving
                    </label>
                    <textarea name="description" required rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white resize-none" placeholder="Bijvoorbeeld: Remblokken vervangen voorwielen, APK keuring, olie + filter vervangen...">{{ old('description') }}</textarea>
                    <p class="text-sm text-gray-500 mt-2">Geef een duidelijke omschrijving van wat er gerepareerd moet worden</p>
                </div>

                <!-- Status and Estimate Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            <i class="fa-solid fa-tasks text-purple-600 mr-2"></i>
                            Status
                        </label>
                        <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                            <option value="gepland" {{ old('status') == 'gepland' ? 'selected' : '' }}>📅 Gepland</option>
                            <option value="bezig" {{ old('status') == 'bezig' ? 'selected' : '' }}>🔧 Bezig</option>
                            <option value="wachten_op_onderdeel" {{ old('status') == 'wachten_op_onderdeel' ? 'selected' : '' }}>⏳ Wachten op onderdeel</option>
                            <option value="gereed" {{ old('status') == 'gereed' ? 'selected' : '' }}>✅ Gereed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            <i class="fa-solid fa-euro-sign text-yellow-600 mr-2"></i>
                            Kostenraming (€)
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">€</span>
                            <input type="number" step="0.01" min="0" name="cost_estimate" value="{{ old('cost_estimate') }}" class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white" placeholder="249.95">
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Geschatte kosten voor de reparatie (optioneel)</p>
                    </div>
                </div>

                <!-- Planned Date (Optional) -->
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fa-solid fa-calendar text-indigo-600 mr-2"></i>
                        Geplande Datum (Optioneel)
                    </label>
                    <input type="date" name="planned_at" value="{{ old('planned_at') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                    <p class="text-sm text-gray-500 mt-2">Wanneer is deze reparatie gepland?</p>
                </div>

                <!-- Additional Notes -->
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fa-solid fa-sticky-note text-orange-600 mr-2"></i>
                        Extra Notities (Optioneel)
                    </label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 hover:bg-white resize-none" placeholder="Extra informatie, bijzonderheden, klant wensen...">{{ old('notes') }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-4 rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center">
                        <i class="fa-solid fa-save mr-3"></i>
                        Reparatie Aanmaken
                    </button>
                    <a href="{{ route('repairs.index') }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-8 py-4 rounded-xl font-bold transition-all duration-200 flex items-center justify-center">
                        <i class="fa-solid fa-times mr-3"></i>
                        Annuleren
                    </a>
                </div>
            </form>
        </div>

        <!-- Help Card -->
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-3 flex items-center">
                <i class="fa-solid fa-lightbulb text-yellow-500 mr-2"></i>
                Tips voor het aanmaken van reparaties
            </h3>
            <ul class="space-y-2 text-blue-800">
                <li class="flex items-start">
                    <i class="fa-solid fa-check text-green-500 mr-2 mt-1 text-sm"></i>
                    <span>Zorg voor een duidelijke omschrijving van wat er gerepareerd moet worden</span>
                </li>
                <li class="flex items-start">
                    <i class="fa-solid fa-check text-green-500 mr-2 mt-1 text-sm"></i>
                    <span>Geef een realistische kostenraming op basis van onderdelen en arbeid</span>
                </li>
                <li class="flex items-start">
                    <i class="fa-solid fa-check text-green-500 mr-2 mt-1 text-sm"></i>
                    <span>Na het aanmaken kun je onderdelen toevoegen en de status bijwerken</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
@keyframes slideInUp {
    from { 
        transform: translateY(30px); 
        opacity: 0; 
    }
    to { 
        transform: translateY(0); 
        opacity: 1; 
    }
}

.bg-white {
    animation: slideInUp 0.6s ease-out;
}

.form-section {
    transition: all 0.3s ease;
}

.form-section:hover {
    transform: translateY(-1px);
}

input:focus, select:focus, textarea:focus {
    transform: scale(1.01);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

button[type="submit"]:hover {
    animation: pulse 0.3s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
</style>
@endsection
