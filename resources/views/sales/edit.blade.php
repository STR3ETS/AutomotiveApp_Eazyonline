@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-full">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('sales.show', $sale) }}" 
                   class="text-gray-600 hover:text-gray-900 transition-colors duration-200">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">📝 Verkoop Bewerken</h1>
            </div>
            <p class="text-gray-600">Bewerk verkoopdossier #{{ $sale->id }} voor {{ $sale->car->license_plate }}</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Verkoop Informatie</h2>
                <p class="text-sm text-gray-600">Wijzig de gewenste gegevens</p>
            </div>

            <form method="POST" action="{{ route('sales.update', $sale) }}" class="p-6">
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
                    
                    <!-- Auto (Read-only) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Auto
                        </label>
                        <div class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                            {{ $sale->car->license_plate }} - {{ $sale->car->brand }} {{ $sale->car->model }} ({{ $sale->car->year }})
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Auto kan niet gewijzigd worden na aanmaak verkoop</p>
                    </div>

                    <!-- Klant (Read-only) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Klant
                        </label>
                        <div class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                            {{ $sale->customer->name }} - {{ $sale->customer->email }}
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Klant kan niet gewijzigd worden, bewerk klant apart indien nodig</p>
                    </div>

                    <!-- Verkoopprijs -->
                    <div>
                        <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Verkoopprijs <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500 text-sm">€</span>
                            <input type="number" 
                                   id="sale_price" 
                                   name="sale_price" 
                                   value="{{ old('sale_price', $sale->sale_price) }}"
                                   min="0"
                                   step="0.01"
                                   placeholder="15000"
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('sale_price') border-red-500 @enderror"
                                   required>
                        </div>
                        @error('sale_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Aanbetaling -->
                    <div>
                        <label for="deposit_amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Aanbetaling (optioneel)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500 text-sm">€</span>
                            <input type="number" 
                                   id="deposit_amount" 
                                   name="deposit_amount" 
                                   value="{{ old('deposit_amount', $sale->deposit_amount) }}"
                                   min="0"
                                   step="0.01"
                                   placeholder="2500"
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('deposit_amount') border-red-500 @enderror">
                        </div>
                        @error('deposit_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select id="status" 
                                name="status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                            <option value="option" {{ old('status', $sale->status) == 'option' ? 'selected' : '' }}>📝 Optie</option>
                            <option value="contract_signed" {{ old('status', $sale->status) == 'contract_signed' ? 'selected' : '' }}>📋 Contract getekend</option>
                            <option value="ready_for_delivery" {{ old('status', $sale->status) == 'ready_for_delivery' ? 'selected' : '' }}>🚀 Klaar voor levering</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Betalingsstatus -->
                    <div>
                        <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">
                            Betalingsstatus
                        </label>
                        <select id="payment_status" 
                                name="payment_status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('payment_status') border-red-500 @enderror">
                            <option value="open" {{ old('payment_status', $sale->payment_status) == 'open' ? 'selected' : '' }}>🔍 Open</option>
                            <option value="deposit_paid" {{ old('payment_status', $sale->payment_status) == 'deposit_paid' ? 'selected' : '' }}>💳 Aanbetaling ontvangen</option>
                            <option value="paid" {{ old('payment_status', $sale->payment_status) == 'paid' ? 'selected' : '' }}>💰 Volledig betaald</option>
                        </select>
                        @error('payment_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Leverdatum -->
                    <div>
                        <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Leverdatum (optioneel)
                        </label>
                        <input type="date" 
                               id="delivery_date" 
                               name="delivery_date" 
                               value="{{ old('delivery_date', $sale->delivery_date) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('delivery_date') border-red-500 @enderror">
                        @error('delivery_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Levertijd -->
                    <div>
                        <label for="delivery_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Levertijd (optioneel)
                        </label>
                        <input type="time" 
                               id="delivery_time" 
                               name="delivery_time" 
                               value="{{ old('delivery_time', $sale->delivery_time) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('delivery_time') border-red-500 @enderror">
                        @error('delivery_time')
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
                                  rows="4"
                                  placeholder="Eventuele bijzonderheden over de verkoop, afspraken, bijzondere wensen van de klant..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes', $sale->notes) }}</textarea>
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
                        <a href="{{ route('sales.show', $sale) }}" 
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

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-lightbulb text-blue-400 text-lg"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">💡 Tips voor verkoop beheer</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Zet status op "Contract getekend" wanneer papierwerk compleet is</li>
                            <li>Update betalingsstatus wanneer betalingen binnenkomen</li>
                            <li>Plan leverdatum en tijd in overleg met klant</li>
                            <li>Gebruik notities voor belangrijke afspraken of bijzonderheden</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Calculate and show remaining amount when deposit changes
document.getElementById('deposit_amount').addEventListener('input', function() {
    const salePrice = parseFloat(document.getElementById('sale_price').value) || 0;
    const depositAmount = parseFloat(this.value) || 0;
    const remaining = salePrice - depositAmount;
    
    // You could add a visual indicator here if needed
    if (depositAmount > salePrice) {
        this.setCustomValidity('Aanbetaling kan niet hoger zijn dan verkoopprijs');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('sale_price').addEventListener('input', function() {
    const depositInput = document.getElementById('deposit_amount');
    const depositAmount = parseFloat(depositInput.value) || 0;
    const salePrice = parseFloat(this.value) || 0;
    
    if (depositAmount > salePrice) {
        depositInput.setCustomValidity('Aanbetaling kan niet hoger zijn dan verkoopprijs');
    } else {
        depositInput.setCustomValidity('');
    }
});
</script>
@endsection
