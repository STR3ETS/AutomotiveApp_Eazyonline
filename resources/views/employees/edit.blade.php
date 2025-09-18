@extends('layouts.app')

@section('content')
<div class="bg-[var(--background-main)] min-h-full">
    <div class="max-w-2xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('employees.show', $employee) }}" 
                   class="text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors duration-200">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-3xl font-bold text-[var(--text-primary)]">👷 Medewerker Bewerken</h1>
            </div>
            <p class="text-[var(--text-secondary)]">Bewerk de gegevens van {{ $employee->name }}</p>
        </div>

        <!-- Form Card -->
        <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] overflow-hidden">
            <div class="px-6 py-4 bg-[var(--background-main)] border-b border-[var(--border-light)]">
                <h2 class="text-lg font-semibold text-[var(--text-primary)]">Medewerker Gegevens</h2>
            </div>

            <form method="POST" action="{{ route('employees.update', $employee) }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Naam -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                            Naam <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $employee->name) }}"
                               placeholder="Volledige naam"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                            Email
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $employee->email) }}"
                               placeholder="naam@bedrijf.nl"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Telefoon -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                            Telefoon
                        </label>
                        <input type="text" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $employee->phone) }}"
                               placeholder="06-12345678"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Functie -->
                    <div class="md:col-span-2">
                        <label for="position" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                            Functie <span class="text-red-500">*</span>
                        </label>
                        <select id="position" 
                                name="position" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('position') border-red-500 @enderror"
                                required>
                            <option value="">Selecteer een functie</option>
                            @foreach($positions as $position)
                                <option value="{{ $position }}" {{ old('position', $employee->position) == $position ? 'selected' : '' }}>
                                    {{ $position }}
                                </option>
                            @endforeach
                        </select>
                        @error('position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actief Status -->
                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="active" 
                                   value="1"
                                   {{ old('active', $employee->active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-[var(--text-primary)]">Medewerker is actief</span>
                        </label>
                        <p class="mt-1 text-xs text-[var(--text-secondary)]">Inactieve medewerkers kunnen geen nieuwe auto's toegewezen krijgen</p>
                    </div>

                    <!-- Specialisaties -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                            Specialisaties
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($specializations as $specialization)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="specializations[]" 
                                           value="{{ $specialization }}"
                                           {{ in_array($specialization, old('specializations', $employee->specializations ?? [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-[var(--text-primary)]">{{ $specialization }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('specializations')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-[var(--text-secondary)]">Selecteer de specialisaties van deze medewerker</p>
                    </div>
                </div>

                <!-- Warning Box for Active Assignments -->
                @if($employee->currentAssignments->count() > 0)
                    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fa-solid fa-exclamation-triangle text-yellow-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">⚠️ Let op!</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Deze medewerker heeft {{ $employee->currentAssignments->count() }} actieve auto-toewijzing(en). Als je de medewerker inactief maakt, blijven de huidige toewijzingen actief.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 mt-6 border-t border-[var(--border-light)]">
                    <div class="text-sm text-[var(--text-secondary)]">
                        <i class="fa-solid fa-info-circle mr-1"></i>
                        Velden met <span class="text-red-500">*</span> zijn verplicht
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('employees.show', $employee) }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-times"></i>
                            Annuleren
                        </a>
                        <button type="submit" 
                                class="bg-[var(--status-info)] hover:bg-[var(--status-info-dark)] text-[var(--text-white)] font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-save"></i>
                            Bijwerken
                        </button>
                    </div>
                </div>
            </form>

            <!-- Delete Section -->
            @if($employee->currentAssignments->count() === 0)
                <div class="px-6 py-4 bg-red-50 border-t border-red-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Medewerker Verwijderen</h3>
                            <p class="text-sm text-red-600">Deze actie kan niet ongedaan worden gemaakt.</p>
                        </div>
                        <form method="POST" action="{{ route('employees.destroy', $employee) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                                    onclick="return confirm('Weet je zeker dat je {{ $employee->name }} wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.')">
                                <i class="fa-solid fa-trash mr-2"></i>
                                Verwijderen
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
