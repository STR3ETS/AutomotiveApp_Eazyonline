@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 min-h-full">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header with Navigation -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-4">
                    <a href="{{ route('repairs.index') }}" class="hover:text-emerald-600 transition-colors">
                        <i class="fa-solid fa-wrench mr-1"></i>
                        Reparaties
                    </a>
                    <i class="fa-solid fa-chevron-right text-gray-400"></i>
                    <a href="{{ route('repairs.show', $repair->id) }}" class="hover:text-emerald-600 transition-colors">
                        Reparatie #{{ $repair->id }}
                    </a>
                    <i class="fa-solid fa-chevron-right text-gray-400"></i>
                    <span class="text-gray-900 font-medium">Bewerken</span>
                </nav>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                    ✏️ Reparatie Bewerken
                </h1>
                <p class="text-gray-600 mt-2">Bewerk de details van reparatie #{{ $repair->id }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('repairs.show', $repair->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium rounded-xl transition-all duration-200">
                    <i class="fa-solid fa-eye mr-2"></i>
                    Bekijken
                </a>
                <a href="{{ route('repairs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Terug
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-8 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 rounded-2xl flex items-center shadow-sm">
                <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fa-solid fa-check text-white text-sm"></i>
                </div>
                <div class="font-medium">{{ session('success') }}</div>
            </div>
        @endif

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

        <!-- Status Overview Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Huidige Status</h3>
                    @php
                        $statusConfig = [
                            'gepland' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-calendar'],
                            'bezig' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-cog'],
                            'wachten_op_onderdeel' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'icon' => 'fa-clock'],
                            'gereed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle']
                        ];
                        $config = $statusConfig[$repair->status] ?? $statusConfig['gepland'];
                    @endphp
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                        <i class="fa-solid {{ $config['icon'] }} mr-2"></i>
                        {{ ucfirst(str_replace('_', ' ', $repair->status)) }}
                    </span>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Aangemaakt op</div>
                    <div class="font-medium text-gray-900">{{ $repair->created_at->format('d-m-Y H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Main Edit Form -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fa-solid fa-edit mr-3"></i>
                    Reparatie Bewerken
                </h2>
                <p class="text-emerald-100 mt-1">Pas de details van de reparatie aan</p>
            </div>

            <form method="POST" action="{{ route('repairs.update', $repair) }}" class="p-8">
                @csrf
                @method('PUT')
                
                <!-- Auto Selection -->
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fa-solid fa-car text-blue-600 mr-2"></i>
                        Auto
                    </label>
                    <select name="car_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                        <option value="">— Selecteer een auto —</option>
                        @foreach($cars as $car)
                            <option value="{{ $car->id }}" {{ (old('car_id', $repair->car_id) == $car->id) ? 'selected' : '' }}>
                                {{ $car->license_plate ?? '—' }} — {{ $car->brand ?? '' }} {{ $car->model ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fa-solid fa-file-text text-green-600 mr-2"></i>
                        Reparatie Omschrijving
                    </label>
                    <textarea name="description" required rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-gray-50 hover:bg-white resize-none" placeholder="Bijvoorbeeld: Remblokken vervangen voorwielen, APK keuring...">{{ old('description', $repair->description) }}</textarea>
                </div>

                <!-- Status and Estimate Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            <i class="fa-solid fa-tasks text-purple-600 mr-2"></i>
                            Status
                        </label>
                        <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                            <option value="gepland" {{ old('status', $repair->status) == 'gepland' ? 'selected' : '' }}>📅 Gepland</option>
                            <option value="bezig" {{ old('status', $repair->status) == 'bezig' ? 'selected' : '' }}>🔧 Bezig</option>
                            <option value="wachten_op_onderdeel" {{ old('status', $repair->status) == 'wachten_op_onderdeel' ? 'selected' : '' }}>⏳ Wachten op onderdeel</option>
                            <option value="gereed" {{ old('status', $repair->status) == 'gereed' ? 'selected' : '' }}>✅ Gereed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            <i class="fa-solid fa-euro-sign text-yellow-600 mr-2"></i>
                            Kostenraming (€)
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">€</span>
                            <input type="number" step="0.01" min="0" name="cost_estimate" value="{{ old('cost_estimate', $repair->cost_estimate) }}" class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-gray-50 hover:bg-white" placeholder="249.95">
                        </div>
                    </div>
                </div>

                <!-- Planned Date -->
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fa-solid fa-calendar text-indigo-600 mr-2"></i>
                        Geplande Datum (Optioneel)
                    </label>
                    <input type="date" name="planned_at" value="{{ old('planned_at', $repair->planned_at ? $repair->planned_at->format('Y-m-d') : '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-gray-50 hover:bg-white">
                </div>

                <!-- Notes -->
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-3">
                        <i class="fa-solid fa-sticky-note text-orange-600 mr-2"></i>
                        Extra Notities (Optioneel)
                    </label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-gray-50 hover:bg-white resize-none" placeholder="Extra informatie, bijzonderheden...">{{ old('notes', $repair->notes) }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-8 py-4 rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center">
                        <i class="fa-solid fa-save mr-3"></i>
                        Wijzigingen Opslaan
                    </button>
                    <a href="{{ route('repairs.show', $repair->id) }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-8 py-4 rounded-xl font-bold transition-all duration-200 flex items-center justify-center">
                        <i class="fa-solid fa-times mr-3"></i>
                        Annuleren
                    </a>
                </div>
            </form>
        </div>

        <!-- Quick Actions Card -->
        <div class="mt-8 bg-gradient-to-r from-gray-50 to-blue-50 border border-gray-200 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="fa-solid fa-bolt text-yellow-500 mr-2"></i>
                Snelle Acties
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('repairs.parts.index', $repair->id) }}" class="block p-4 bg-white rounded-xl border border-gray-200 hover:border-purple-300 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fa-solid fa-puzzle-piece text-purple-600"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Onderdelen</div>
                            <div class="text-sm text-gray-600">{{ $repair->parts->count() }} onderdelen</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('repairs.show', $repair->id) }}" class="block p-4 bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fa-solid fa-eye text-blue-600"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Bekijken</div>
                            <div class="text-sm text-gray-600">Volledig overzicht</div>
                        </div>
                    </div>
                </a>

                <button onclick="showDeleteModal()" class="block w-full p-4 bg-white rounded-xl border border-gray-200 hover:border-red-300 hover:shadow-md transition-all duration-200 text-left">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fa-solid fa-trash text-red-600"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Verwijderen</div>
                            <div class="text-sm text-gray-600">Permanent verwijderen</div>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50" style="display: none;">
    <div class="flex items-center justify-center min-h-full p-4">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Reparatie Verwijderen</h3>
                <p class="text-gray-600 mb-6">Weet je zeker dat je deze reparatie permanent wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.</p>
                <div class="flex space-x-4">
                    <button onclick="hideDeleteModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-all duration-200">
                        Annuleren
                    </button>
                    <form method="POST" action="{{ route('repairs.destroy', $repair) }}" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200">
                            Verwijderen
                        </button>
                    </form>
                </div>
            </div>
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

input:focus, select:focus, textarea:focus {
    transform: scale(1.01);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
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

<script>
function showDeleteModal() {
    document.getElementById('deleteModal').style.display = 'block';
}

function hideDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDeleteModal();
    }
});
</script>
@endsection
