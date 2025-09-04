@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-full">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('autos.show', $auto) }}" 
                   class="text-gray-600 hover:text-gray-900 transition-colors duration-200">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">🚗 Auto Bewerken</h1>
            </div>
            <p class="text-gray-600">Bewerk de gegevens van {{ $auto->license_plate }}</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Auto Informatie</h2>
                <p class="text-sm text-gray-600">Wijzig de gewenste gegevens</p>
            </div>

            <form method="POST" action="{{ route('autos.update', $auto) }}" class="p-6">
                @csrf
                @method('PUT')

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
                               value="{{ old('license_plate', $auto->license_plate) }}"
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
                               value="{{ old('brand', $auto->brand) }}"
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
                               value="{{ old('model', $auto->model) }}"
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
                               value="{{ old('year', $auto->year) }}"
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
                                   value="{{ old('mileage', $auto->mileage) }}"
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
                                   value="{{ old('price', $auto->price) }}"
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

                    <!-- Huidige Fase -->
                    <div>
                        <label for="stage_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Huidige Fase
                        </label>
                        <select id="stage_id" 
                                name="stage_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stage_id') border-red-500 @enderror">
                            <option value="">Geen fase</option>
                            @foreach($stages as $stage)
                                <option value="{{ $stage->id }}" 
                                        {{ old('stage_id', $auto->stage_id) == $stage->id ? 'selected' : '' }}>
                                    {{ $stage->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('stage_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Warning Box -->
                @if($auto->sales->whereNotIn('status', ['delivered', 'cancelled'])->count() > 0)
                    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fa-solid fa-exclamation-triangle text-yellow-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">⚠️ Let op!</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    Deze auto heeft actieve verkopen. Wijzigingen kunnen invloed hebben op lopende verkoopdossiers.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        <i class="fa-solid fa-info-circle mr-1"></i>
                        Velden met <span class="text-red-500">*</span> zijn verplicht
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('autos.show', $auto) }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-times"></i>
                            Annuleren
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-save"></i>
                            Wijzigingen Opslaan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Additional Actions -->
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🔧 Aanvullende Acties</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('pipeline.checklist', $auto) }}" 
                   class="flex items-center justify-center gap-2 p-4 border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors duration-200">
                    <i class="fa-solid fa-list-check text-blue-600"></i>
                    <span class="text-blue-600 font-medium">Checklist Beheren</span>
                </a>
                <a href="{{ route('repairs.index', ['car_id' => $auto->id]) }}" 
                   class="flex items-center justify-center gap-2 p-4 border border-orange-200 rounded-lg hover:bg-orange-50 transition-colors duration-200">
                    <i class="fa-solid fa-wrench text-orange-600"></i>
                    <span class="text-orange-600 font-medium">Reparatie Toevoegen</span>
                </a>
                <a href="{{ route('agenda.index', ['car_id' => $auto->id]) }}" 
                   class="flex items-center justify-center gap-2 p-4 border border-purple-200 rounded-lg hover:bg-purple-50 transition-colors duration-200">
                    <i class="fa-solid fa-calendar text-purple-600"></i>
                    <span class="text-purple-600 font-medium">Afspraak Plannen</span>
                </a>
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
