@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-full">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('autos.index') }}" 
                   class="text-gray-600 hover:text-gray-900 transition-colors duration-200">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">🚗 Nieuwe Auto Toevoegen</h1>
            </div>
            <p class="text-gray-600">Voeg een nieuwe auto toe aan je voorraad pipeline</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Auto Informatie</h2>
                <p class="text-sm text-gray-600">Vul alle benodigde gegevens in</p>
            </div>

            <form method="POST" action="{{ route('autos.store') }}" class="p-6">
                @csrf

                <!-- Error Summary -->
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <div class="flex items-center mb-2">
                            <i class="fa-solid fa-exclamation-circle mr-2"></i>
                            <strong>Er zijn fouten gevonden:</strong>
                        </div>
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kenteken -->
                    <div class="md:col-span-2">
                        <label for="license_plate" class="block text-sm font-medium text-gray-700 mb-2">
                            Kenteken <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="license_plate" 
                               name="license_plate" 
                               value="{{ old('license_plate') }}"
                               placeholder="XX-XXX-X"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('license_plate') border-red-500 @enderror"
                               required>
                        @error('license_plate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Merk -->
                    <div>
                        <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">
                            Merk <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="brand" 
                               name="brand" 
                               value="{{ old('brand') }}"
                               placeholder="Volkswagen, BMW, Audi..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('brand') border-red-500 @enderror"
                               required>
                        @error('brand')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Model -->
                    <div>
                        <label for="model" class="block text-sm font-medium text-gray-700 mb-2">
                            Model <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="model" 
                               name="model" 
                               value="{{ old('model') }}"
                               placeholder="Golf, 3 Serie, A4..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('model') border-red-500 @enderror"
                               required>
                        @error('model')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bouwjaar -->
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                            Bouwjaar <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="year" 
                               name="year" 
                               value="{{ old('year') }}"
                               min="1950" 
                               max="{{ date('Y') + 1 }}"
                               placeholder="{{ date('Y') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('year') border-red-500 @enderror"
                               required>
                        @error('year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kilometerstand -->
                    <div>
                        <label for="mileage" class="block text-sm font-medium text-gray-700 mb-2">
                            Kilometerstand <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   id="mileage" 
                                   name="mileage" 
                                   value="{{ old('mileage') }}"
                                   min="0"
                                   placeholder="75000"
                                   class="w-full px-4 py-2 pr-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('mileage') border-red-500 @enderror"
                                   required>
                            <span class="absolute right-3 top-2 text-gray-500 text-sm">km</span>
                        </div>
                        @error('mileage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prijs -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Vraagprijs <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500 text-sm">€</span>
                            <input type="number" 
                                   id="price" 
                                   name="price" 
                                   value="{{ old('price') }}"
                                   min="0"
                                   step="0.01"
                                   placeholder="15000"
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-500 @enderror"
                                   required>
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Start Fase -->
                    <div>
                        <label for="stage_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Fase
                        </label>
                        <select id="stage_id" 
                                name="stage_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stage_id') border-red-500 @enderror">
                            <option value="">Automatisch (Intake)</option>
                            @foreach($stages as $stage)
                                <option value="{{ $stage->id }}" {{ old('stage_id') == $stage->id ? 'selected' : '' }}>
                                    {{ $stage->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Laat leeg om automatisch in "Intake" te beginnen</p>
                        @error('stage_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notities -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notities (optioneel)
                        </label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="3"
                                  placeholder="Eventuele bijzonderheden, schade, onderhoud geschiedenis, etc..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        <i class="fa-solid fa-info-circle mr-1"></i>
                        Velden met <span class="text-red-500">*</span> zijn verplicht
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('autos.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-times"></i>
                            Annuleren
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-save"></i>
                            Auto Toevoegen
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-lightbulb text-blue-400 text-lg"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">💡 Handig om te weten</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Na toevoegen worden automatisch checklist taken aangemaakt voor alle fases</li>
                            <li>De auto start standaard in de "Intake" fase</li>
                            <li>Je kunt de auto later altijd bewerken of verplaatsen naar andere fases</li>
                            <li>Kenteken moet uniek zijn in het systeem</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Improved license plate formatting
document.getElementById('license_plate').addEventListener('input', function(e) {
    let cursorPosition = e.target.selectionStart;
    let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    let originalLength = e.target.value.length;
    
    // Format license plate based on length
    let formatted = '';
    if (value.length > 0) {
        if (value.length <= 2) {
            formatted = value;
        } else if (value.length <= 5) {
            formatted = value.substring(0, 2) + '-' + value.substring(2);
        } else if (value.length <= 6) {
            formatted = value.substring(0, 2) + '-' + value.substring(2, 5) + '-' + value.substring(5);
        } else {
            // Limit to 6 characters max
            formatted = value.substring(0, 2) + '-' + value.substring(2, 5) + '-' + value.substring(5, 6);
        }
    }
    
    e.target.value = formatted;
    
    // Adjust cursor position after formatting
    let newLength = formatted.length;
    let diff = newLength - originalLength;
    let newCursorPosition = cursorPosition + diff;
    
    // Make sure cursor doesn't land on a dash
    if (formatted[newCursorPosition] === '-') {
        newCursorPosition++;
    }
    
    // Set cursor position after a small delay to ensure formatting is complete
    setTimeout(() => {
        e.target.setSelectionRange(newCursorPosition, newCursorPosition);
    }, 0);
});

// Handle backspace/delete for license plate
document.getElementById('license_plate').addEventListener('keydown', function(e) {
    if (e.key === 'Backspace' || e.key === 'Delete') {
        let cursorPosition = e.target.selectionStart;
        let value = e.target.value;
        
        // If cursor is right after a dash, move it back one more position
        if (e.key === 'Backspace' && cursorPosition > 0 && value[cursorPosition - 1] === '-') {
            setTimeout(() => {
                e.target.setSelectionRange(cursorPosition - 1, cursorPosition - 1);
            }, 0);
        }
    }
});

// Auto-capitalize brand and model
document.getElementById('brand').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\b\w/g, l => l.toUpperCase());
});

document.getElementById('model').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\b\w/g, l => l.toUpperCase());
});
</script>
@endsection
