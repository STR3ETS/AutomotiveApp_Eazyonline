@extends('layouts.app')

@section('content')
<div class="bg-[var(--background-main)] min-h-full">
    <div class="max-w-2xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('employees.index') }}" 
                   class="text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors duration-200">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-3xl font-bold text-[var(--text-primary)]">👷 Nieuwe Medewerker</h1>
            </div>
            <p class="text-[var(--text-secondary)]">Voeg een nieuwe medewerker toe aan je team</p>
        </div>

        <!-- Form Card -->
        <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] overflow-hidden">
            <div class="px-6 py-4 bg-[var(--background-main)] border-b border-[var(--border-light)]">
                <h2 class="text-lg font-semibold text-[var(--text-primary)]">Medewerker Gegevens</h2>
            </div>

            <form method="POST" action="{{ route('employees.store') }}" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Naam -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                            Naam <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
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
                               value="{{ old('email') }}"
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
                               value="{{ old('phone') }}"
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
                                <option value="{{ $position }}" {{ old('position') == $position ? 'selected' : '' }}>
                                    {{ $position }}
                                </option>
                            @endforeach
                        </select>
                        @error('position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                                           {{ in_array($specialization, old('specializations', [])) ? 'checked' : '' }}
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

                    <!-- Login Toegang -->
                    <div class="md:col-span-2">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-900 mb-3">🔐 Login Toegang</h4>
                            
                            <label class="flex items-center mb-4">
                                <input type="checkbox" 
                                       id="create_user_account"
                                       name="create_user_account" 
                                       value="1"
                                       {{ old('create_user_account') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-blue-900">Maak een login account aan voor deze medewerker</span>
                            </label>
                            
                            <div id="user_role_section" style="display: none;">
                                <label for="user_role" class="block text-sm font-medium text-blue-900 mb-2">
                                    Rol <span class="text-red-500">*</span>
                                </label>
                                <select id="user_role" 
                                        name="user_role" 
                                        class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Selecteer een rol</option>
                                    <option value="employee" {{ old('user_role') == 'employee' ? 'selected' : '' }}>Medewerker (Beperkte toegang)</option>
                                    <option value="manager" {{ old('user_role') == 'manager' ? 'selected' : '' }}>Voorman (Bijna alle rechten)</option>
                                </select>
                                @error('user_role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <div class="mt-2 text-xs text-blue-700">
                                    <div><strong>Medewerker:</strong> Kan alleen eigen werk bekijken</div>
                                    <div><strong>Voorman:</strong> Kan alles behalve bedrijfsinstellingen wijzigen</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 mt-6 border-t border-[var(--border-light)]">
                    <div class="text-sm text-[var(--text-secondary)]">
                        <i class="fa-solid fa-info-circle mr-1"></i>
                        Velden met <span class="text-red-500">*</span> zijn verplicht
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('employees.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-times"></i>
                            Annuleren
                        </a>
                        <button type="submit" 
                                class="bg-[var(--status-info)] hover:bg-[var(--status-info-dark)] text-[var(--text-white)] font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-user-plus"></i>
                            Medewerker Toevoegen
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const createAccountCheckbox = document.getElementById('create_user_account');
    const roleSection = document.getElementById('user_role_section');
    const roleSelect = document.getElementById('user_role');
    
    function toggleRoleSection() {
        if (createAccountCheckbox.checked) {
            roleSection.style.display = 'block';
            roleSelect.required = true;
        } else {
            roleSection.style.display = 'none';
            roleSelect.required = false;
            roleSelect.value = '';
        }
    }
    
    createAccountCheckbox.addEventListener('change', toggleRoleSection);
    toggleRoleSection(); // Initialize state
});
</script>
@endsection
