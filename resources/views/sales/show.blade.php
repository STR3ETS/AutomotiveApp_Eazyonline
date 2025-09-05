@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-full">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('sales.index') }}" 
                       class="text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        <i class="fa-solid fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">🚗 Verkoopdossier #{{ $sale->id }}</h1>
                        <p class="text-gray-600 mt-1">{{ $sale->car->brand }} {{ $sale->car->model }} • {{ $sale->car->license_plate }}</p>
                    </div>
                </div>
                
                <!-- Status Badge -->
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-sm font-medium 
                        @if($sale->status === 'delivered') bg-green-100 text-green-800
                        @elseif($sale->status === 'cancelled') bg-red-100 text-red-800
                        @elseif($sale->status === 'ready_for_delivery') bg-blue-100 text-blue-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        @switch($sale->status)
                            @case('delivered') ✅ Afgeleverd @break
                            @case('cancelled') ❌ Geannuleerd @break
                            @case('ready_for_delivery') 🚀 Klaar voor levering @break
                            @case('contract_signed') 📋 Contract getekend @break
                            @default 📝 Optie @break
                        @endswitch
                    </span>
                    
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($sale->payment_status === 'paid') bg-green-100 text-green-800
                        @elseif($sale->payment_status === 'deposit_paid') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        @switch($sale->payment_status)
                            @case('paid') 💰 Volledig betaald @break
                            @case('deposit_paid') 💳 Aanbetaling @break
                            @default 🔍 Open @break
                        @endswitch
                    </span>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fa-solid fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <i class="fa-solid fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Auto Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-car text-blue-600"></i>
                            Voertuig Informatie
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Kenteken</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $sale->car->license_plate }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Merk & Model</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $sale->car->brand }} {{ $sale->car->model }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Bouwjaar</label>
                                        <p class="text-lg text-gray-900">{{ $sale->car->year }}</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Kilometerstand</label>
                                        <p class="text-lg text-gray-900">{{ number_format($sale->car->mileage) }} km</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Vraagprijs</label>
                                        <p class="text-lg text-gray-900">€ {{ number_format($sale->car->price, 2) }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Huidige Fase</label>
                                        <p class="text-lg text-gray-900">{{ $sale->car->stage->name ?? 'Onbekend' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Klant Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-user text-green-600"></i>
                            Klant Informatie
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Naam</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $sale->customer->name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">E-mail</label>
                                        <p class="text-lg text-gray-900">
                                            <a href="mailto:{{ $sale->customer->email }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                                                {{ $sale->customer->email }}
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Telefoon</label>
                                        <p class="text-lg text-gray-900">
                                            <a href="tel:{{ $sale->customer->phone }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                                                {{ $sale->customer->phone }}
                                            </a>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Adres</label>
                                        <p class="text-lg text-gray-900">{{ $sale->customer->address ?? 'Niet opgegeven' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Levering Informatie -->
                @if($sale->delivery_date || $sale->delivery_time)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fa-solid fa-calendar text-purple-600"></i>
                                Levering Details
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-6">
                                @if($sale->delivery_date)
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Leverdatum</label>
                                        <p class="text-lg text-gray-900">{{ \Carbon\Carbon::parse($sale->delivery_date)->format('d-m-Y') }}</p>
                                    </div>
                                @endif
                                @if($sale->delivery_time)
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Levertijd</label>
                                        <p class="text-lg text-gray-900">{{ $sale->delivery_time }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Notities -->
                @if($sale->notes)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fa-solid fa-sticky-note text-yellow-600"></i>
                                Notities
                            </h2>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-900 whitespace-pre-line">{{ $sale->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                
                <!-- Financiën -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-euro-sign text-green-600"></i>
                            Financiën
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Verkoopprijs</span>
                            <span class="text-lg font-bold text-gray-900">€ {{ number_format($sale->sale_price, 2) }}</span>
                        </div>
                        
                        @if($sale->deposit_amount)
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500">Aanbetaling</span>
                                <span class="text-lg font-semibold text-green-600">€ {{ number_format($sale->deposit_amount, 2) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center pt-2 border-t">
                                <span class="text-sm font-medium text-gray-500">Restbedrag</span>
                                <span class="text-lg font-semibold text-red-600">€ {{ number_format($sale->sale_price - $sale->deposit_amount, 2) }}</span>
                            </div>
                        @endif

                        <!-- Payment Status Update -->
                        @if($sale->status !== 'delivered' && $sale->status !== 'cancelled')
                            <form method="POST" action="{{ route('sales.update', $sale) }}" class="mt-4 pt-4 border-t">
                                @csrf
                                @method('PUT')
                                <label class="block text-sm font-medium text-gray-700 mb-2">Betalingsstatus</label>
                                <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-3">
                                    <option value="open" @selected($sale->payment_status == 'open')>🔍 Open</option>
                                    <option value="deposit_paid" @selected($sale->payment_status == 'deposit_paid')>💳 Aanbetaling</option>
                                    <option value="paid" @selected($sale->payment_status == 'paid')>💰 Volledig betaald</option>
                                </select>
                                {{-- <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                    <i class="fa-solid fa-save mr-2"></i>
                                    Bijwerken
                                </button> --}}
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Acties -->
                @if($sale->status !== 'delivered' && $sale->status !== 'cancelled')
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fa-solid fa-cogs text-gray-600"></i>
                                Acties
                            </h2>
                        </div>
                        <div class="p-6 space-y-3">
                            
                            <!-- Markeer als afgeleverd -->
                            @if($sale->payment_status === 'paid')
                                <form method="POST" action="{{ route('sales.deliver', $sale) }}">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-check-circle"></i>
                                        Markeer als Afgeleverd
                                    </button>
                                </form>
                            @else
                                <div class="w-full bg-gray-300 text-gray-500 font-semibold py-3 px-4 rounded-lg flex items-center justify-center gap-2 cursor-not-allowed">
                                    <i class="fa-solid fa-lock"></i>
                                    Betaling Vereist
                                </div>
                            @endif

                            <!-- Bewerk verkoop -->
                            <a href="{{ route('sales.edit', $sale) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-edit"></i>
                                Bewerk Verkoop
                            </a>

                            <!-- Annuleer verkoop -->
                            <form method="POST" action="{{ route('sales.cancel', $sale) }}" 
                                  onsubmit="return confirm('Weet je zeker dat je deze verkoop wilt annuleren?')">
                                @csrf
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-times-circle"></i>
                                    Annuleer Verkoop
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Timeline -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-clock text-blue-600"></i>
                            Timeline
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Verkoop aangemaakt</p>
                                    <p class="text-xs text-gray-500">{{ $sale->created_at->format('d-m-Y H:i') }}</p>
                                </div>
                            </div>
                            
                            @if($sale->sold_at)
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Auto afgeleverd</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($sale->sold_at)->format('d-m-Y H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection