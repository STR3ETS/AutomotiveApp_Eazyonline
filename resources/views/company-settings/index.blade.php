@extends('layouts.app')

@section('content')
<div class="bg-[var(--background-main)] min-h-full">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[var(--text-primary)] mb-2">⚙️ Bedrijfsinstellingen</h1>
            <p class="text-[var(--text-secondary)]">Beheer de instellingen van {{ $company->name }}</p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-[var(--status-success-light)] border border-[var(--status-success-border)] text-[var(--status-success-text)] px-4 py-3 rounded-lg">
                <i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('company-settings.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Bedrijfsgegevens -->
            <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-[var(--border-light)]">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center">
                        <i class="fa-solid fa-building text-[var(--status-info)] mr-3"></i>
                        Bedrijfsgegevens
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Bedrijfsnaam -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                Bedrijfsnaam <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $company->name) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subdomain -->
                        <div>
                            <label for="subdomain" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                Subdomain <span class="text-red-500">*</span>
                            </label>
                            <div class="flex">
                                <input type="text" id="subdomain" name="subdomain" value="{{ old('subdomain', $company->subdomain) }}"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('subdomain') border-red-500 @enderror"
                                       required>
                                <span class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-gray-500">.localhost</span>
                            </div>
                            @error('subdomain')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                E-mailadres
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email', $company->email) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Telefoon -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                Telefoonnummer
                            </label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $company->phone) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                                   placeholder="+31 6 12345678">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Adres -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                Adres
                            </label>
                            <textarea id="address" name="address" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror"
                                      placeholder="Straatnaam 123, 1234 AB Plaatsnaam">{{ old('address', $company->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Website -->
                        <div>
                            <label for="website" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                Website
                            </label>
                            <input type="url" id="website" name="website" value="{{ old('website', $company->website) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('website') border-red-500 @enderror"
                                   placeholder="https://www.uwbedrijf.nl">
                            @error('website')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- KvK Nummer -->
                        <div>
                            <label for="kvk_number" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                KvK Nummer
                            </label>
                            <input type="text" id="kvk_number" name="kvk_number" value="{{ old('kvk_number', $company->kvk_number) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kvk_number') border-red-500 @enderror"
                                   placeholder="12345678">
                            @error('kvk_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- BTW Nummer -->
                        <div>
                            <label for="btw_number" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                BTW Nummer
                            </label>
                            <input type="text" id="btw_number" name="btw_number" value="{{ old('btw_number', $company->btw_number) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('btw_number') border-red-500 @enderror"
                                   placeholder="NL123456789B01">
                            @error('btw_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Werkplaats Instellingen -->
            <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-[var(--border-light)]">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center">
                        <i class="fa-solid fa-wrench text-[var(--status-success)] mr-3"></i>
                        Werkplaats Instellingen
                    </h2>
                </div>
                <div class="p-6">
                    {{-- <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"> --}}
                        <!-- Uurtarief -->
                        {{-- <div>
                            <label for="repair_hourly_rate" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                Uurtarief Reparaties (€)
                            </label>
                            <input type="number" id="repair_hourly_rate" name="repair_hourly_rate" 
                                   value="{{ old('repair_hourly_rate', $company->getRepairHourlyRate()) }}"
                                   step="0.01" min="0" max="500"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div> --}}

                        <!-- Max afspraken per dag -->
                        {{-- <div>
                            <label for="max_appointments_per_day" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                Max. afspraken per dag
                            </label>
                            <input type="number" id="max_appointments_per_day" name="max_appointments_per_day" 
                                   value="{{ old('max_appointments_per_day', $company->getMaxAppointmentsPerDay()) }}"
                                   min="1" max="50"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div> --}}

                        <!-- Standaard afspraak duur -->
                        {{-- <div>
                            <label for="default_appointment_duration" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                Standaard afspraak duur (min)
                            </label>
                            <select id="default_appointment_duration" name="default_appointment_duration"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach([15, 30, 45, 60, 90, 120, 180, 240] as $duration)
                                    <option value="{{ $duration }}" 
                                        {{ old('default_appointment_duration', $company->getDefaultAppointmentDuration()) == $duration ? 'selected' : '' }}>
                                        {{ $duration }} minuten
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}
                    {{-- </div> --}}

                    <!-- Openingstijden -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-[var(--text-primary)] mb-4">Openingstijden</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @php
                                $days = [
                                    'monday' => 'Maandag',
                                    'tuesday' => 'Dinsdag',
                                    'wednesday' => 'Woensdag',
                                    'thursday' => 'Donderdag',
                                    'friday' => 'Vrijdag',
                                    'saturday' => 'Zaterdag',
                                    'sunday' => 'Zondag'
                                ];
                                $openingHours = $company->getOpeningHours();
                            @endphp
                            @foreach($days as $day => $dayName)
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <h4 class="font-medium text-[var(--text-primary)] mb-2">{{ $dayName }}</h4>
                                    <div class="flex items-center gap-2">
                                        <input type="time" name="opening_hours[{{ $day }}][open]" 
                                               value="{{ old("opening_hours.{$day}.open", $openingHours[$day]['open'] ?? '') }}"
                                               class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                        <span class="text-gray-500">-</span>
                                        <input type="time" name="opening_hours[{{ $day }}][close]" 
                                               value="{{ old("opening_hours.{$day}.close", $openingHours[$day]['close'] ?? '') }}"
                                               class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Laat leeg voor gesloten</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pipeline & Automatisering -->
            {{-- <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-[var(--border-light)]">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center">
                        <i class="fa-solid fa-robot text-purple-600 mr-3"></i>
                        Pipeline & Automatisering
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Automatische voortgang -->
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" id="auto_progress_enabled" name="auto_progress_enabled" value="1"
                                   {{ old('auto_progress_enabled', $company->isAutoProgressEnabled()) ? 'checked' : '' }}
                                   class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            <div>
                                <label for="auto_progress_enabled" class="font-medium text-[var(--text-primary)]">
                                    Automatische voortgang
                                </label>
                                <p class="text-sm text-[var(--text-secondary)]">
                                    Auto's automatisch naar volgende fase verplaatsen wanneer alle taken voltooid zijn
                                </p>
                            </div>
                        </div>

                        <!-- Checklist auto-complete -->
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" id="checklist_auto_complete" name="checklist_auto_complete" value="1"
                                   {{ old('checklist_auto_complete', $company->getSetting('checklist_auto_complete')) ? 'checked' : '' }}
                                   class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            <div>
                                <label for="checklist_auto_complete" class="font-medium text-[var(--text-primary)]">
                                    Auto-complete checklists
                                </label>
                                <p class="text-sm text-[var(--text-secondary)]">
                                    Automatisch checklist items afvinken bij fase overgang
                                </p>
                            </div>
                        </div>

                        <!-- Marktplaats auto-publish -->
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" id="marketplace_auto_publish" name="marketplace_auto_publish" value="1"
                                   {{ old('marketplace_auto_publish', $company->isMarketplaceAutoPublishEnabled()) ? 'checked' : '' }}
                                   class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            <div>
                                <label for="marketplace_auto_publish" class="font-medium text-[var(--text-primary)]">
                                    Auto-publiceren op marktplaatsen
                                </label>
                                <p class="text-sm text-[var(--text-secondary)]">
                                    Automatisch advertenties plaatsen wanneer auto's verkoop-klaar zijn
                                </p>
                            </div>
                        </div>

                        <!-- Standaard garantie -->
                        <div>
                            <label for="default_warranty_months" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                Standaard garantie (maanden)
                            </label>
                            <select id="default_warranty_months" name="default_warranty_months"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach([0, 1, 3, 6, 12, 18, 24, 36] as $months)
                                    <option value="{{ $months }}" 
                                        {{ old('default_warranty_months', $company->getSetting('default_warranty_months', 6)) == $months ? 'selected' : '' }}>
                                        {{ $months == 0 ? 'Geen garantie' : $months . ' maand' . ($months == 1 ? '' : 'en') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- Financiële Instellingen -->
            {{-- <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-amber-50 border-b border-[var(--border-light)]">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center">
                        <i class="fa-solid fa-euro-sign text-yellow-600 mr-3"></i>
                        Financiële Instellingen
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Valuta -->
                        <div>
                            <label for="currency" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                Valuta
                            </label>
                            <select id="currency" name="currency"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach(['EUR' => '€ Euro', 'USD' => '$ Dollar', 'GBP' => '£ Pond'] as $code => $label)
                                    <option value="{{ $code }}" 
                                        {{ old('currency', $company->getCurrency()) == $code ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- BTW Tarief -->
                        <div>
                            <label for="tax_rate" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                BTW Tarief (%)
                            </label>
                            <input type="number" id="tax_rate" name="tax_rate" 
                                   value="{{ old('tax_rate', $company->getTaxRate()) }}"
                                   step="0.1" min="0" max="100"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Factuur Prefix -->
                        <div>
                            <label for="invoice_prefix" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                Factuur Prefix
                            </label>
                            <input type="text" id="invoice_prefix" name="invoice_prefix" 
                                   value="{{ old('invoice_prefix', $company->getInvoicePrefix()) }}"
                                   maxlength="10"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="INV">
                        </div>

                        <!-- Factuur Teller -->
                        <div>
                            <label for="invoice_counter" class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                Factuur Teller
                            </label>
                            <input type="number" id="invoice_counter" name="invoice_counter" 
                                   value="{{ old('invoice_counter', $company->getSetting('invoice_counter', 1)) }}"
                                   min="1"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- Branding & Uiterlijk -->
            <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-pink-50 to-rose-50 border-b border-[var(--border-light)]">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center">
                        <i class="fa-solid fa-palette text-pink-600 mr-3"></i>
                        Branding & Uiterlijk
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Logo Upload -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-primary)] mb-2">
                                Bedrijfslogo
                            </label>
                            <div class="flex items-center space-x-4">
                                @if($company->getLogoUrl())
                                    <img src="{{ $company->getLogoUrl() }}" alt="Huidig logo" class="w-16 h-16 object-contain border border-gray-200 rounded-lg">
                                @else
                                    <div class="w-16 h-16 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-image text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <input type="file" id="logo" name="logo" accept="image/*"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF (max 2MB)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Kleurinstellingen -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-primary)] mb-4">
                                Bedrijfskleuren
                            </label>
                            <div class="space-y-4">
                                <!-- Primaire kleur -->
                                <div class="flex items-center space-x-3">
                                    <input type="color" id="primary_color" name="primary_color" 
                                           value="{{ old('primary_color', $company->primary_color ?? '#2a2f37') }}"
                                           class="w-12 h-10 border border-gray-300 rounded-lg">
                                    <div>
                                        <label for="primary_color" class="font-medium text-[var(--text-primary)]">Primaire kleur</label>
                                        <p class="text-sm text-[var(--text-secondary)]">Hoofdkleur voor knoppen en accenten</p>
                                    </div>
                                </div>

                                <!-- Secundaire kleur -->
                                <div class="flex items-center space-x-3">
                                    <input type="color" id="secondary_color" name="secondary_color" 
                                           value="{{ old('secondary_color', $company->secondary_color ?? '#6b7280') }}"
                                           class="w-12 h-10 border border-gray-300 rounded-lg">
                                    <div>
                                        <label for="secondary_color" class="font-medium text-[var(--text-primary)]">Secundaire kleur</label>
                                        <p class="text-sm text-[var(--text-secondary)]">Voor tekstkleuren en subtiele elementen</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logo opties -->
                    <div class="mt-6">
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" id="show_company_logo" name="show_company_logo" value="1"
                                   {{ old('show_company_logo', $company->getSetting('show_company_logo', true)) ? 'checked' : '' }}
                                   class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            <div>
                                <label for="show_company_logo" class="font-medium text-[var(--text-primary)]">
                                    Logo tonen op advertenties
                                </label>
                                <p class="text-sm text-[var(--text-secondary)]">
                                    Bedrijfslogo automatisch toevoegen aan marktplaats advertenties
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex items-center justify-between pt-6">
                <div class="text-sm text-[var(--text-secondary)]">
                    <i class="fa-solid fa-info-circle mr-1"></i>
                    Instellingen worden direct toegepast na opslaan
                </div>
                <button type="submit" 
                        class="bg-[var(--status-info)] hover:bg-[var(--status-info-dark)] text-[var(--text-white)] font-semibold py-3 px-8 rounded-lg transition duration-200 flex items-center gap-2 shadow-sm hover:shadow-md">
                    <i class="fa-solid fa-save"></i>
                    Instellingen Opslaan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Custom animations */
@keyframes slideInUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.bg-\[var\(--background-card\)\] {
    animation: slideInUp 0.6s ease-out;
}

/* Staggered animation for cards */
.bg-\[var\(--background-card\)\]:nth-child(3) { animation-delay: 0.1s; }
.bg-\[var\(--background-card\)\]:nth-child(4) { animation-delay: 0.2s; }
.bg-\[var\(--background-card\)\]:nth-child(5) { animation-delay: 0.3s; }
.bg-\[var\(--background-card\)\]:nth-child(6) { animation-delay: 0.4s; }
.bg-\[var\(--background-card\)\]:nth-child(7) { animation-delay: 0.5s; }

/* Color picker styling */
input[type="color"] {
    cursor: pointer;
}

input[type="color"]::-webkit-color-swatch-wrapper {
    padding: 0;
}

input[type="color"]::-webkit-color-swatch {
    border: none;
    border-radius: 6px;
}

/* File input styling */
input[type="file"] {
    cursor: pointer;
}

/* Time input styling */
input[type="time"] {
    cursor: pointer;
}
</style>
@endsection
