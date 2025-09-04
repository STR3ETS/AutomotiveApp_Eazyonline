@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-full">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header with Add Button -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">💼 Verkoopdossiers</h1>
                <p class="text-gray-600">Overzicht van alle actieve verkopen</p>
            </div>
            <a href="{{ route('sales.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                Nieuwe Verkoop
            </a>
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

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('sales.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-64">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Zoek op kenteken, merk, model of klant..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Alle statussen</option>
                    <option value="option" {{ request('status') == 'option' ? 'selected' : '' }}>📝 Optie</option>
                    <option value="contract_signed" {{ request('status') == 'contract_signed' ? 'selected' : '' }}>📋 Contract getekend</option>
                    <option value="ready_for_delivery" {{ request('status') == 'ready_for_delivery' ? 'selected' : '' }}>🚀 Klaar voor levering</option>
                </select>
                <select name="payment_status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Alle betalingen</option>
                    <option value="open" {{ request('payment_status') == 'open' ? 'selected' : '' }}>🔍 Open</option>
                    <option value="deposit_paid" {{ request('payment_status') == 'deposit_paid' ? 'selected' : '' }}>💳 Aanbetaling</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>💰 Volledig betaald</option>
                </select>
                <button type="submit" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fa-solid fa-search mr-2"></i>Zoeken
                </button>
                @if(request('search') || request('status') || request('payment_status'))
                    <a href="{{ route('sales.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition duration-200">
                        <i class="fa-solid fa-times mr-2"></i>Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Sales Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($sales->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Auto & Klant
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Financiën
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Levering
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acties
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sales as $sale)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <i class="fa-solid fa-car text-blue-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $sale->car->license_plate }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $sale->car->brand }} {{ $sale->car->model }} ({{ $sale->car->year }})
                                                </div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    <i class="fa-solid fa-user mr-1"></i>{{ $sale->customer->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <div class="font-semibold">€ {{ number_format($sale->sale_price, 2) }}</div>
                                            @if($sale->deposit_amount)
                                                <div class="text-green-600 text-xs">
                                                    Aanbetaling: € {{ number_format($sale->deposit_amount, 2) }}
                                                </div>
                                                <div class="text-red-600 text-xs">
                                                    Restant: € {{ number_format($sale->sale_price - $sale->deposit_amount, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if($sale->status === 'option') bg-gray-100 text-gray-800
                                                @elseif($sale->status === 'contract_signed') bg-blue-100 text-blue-800
                                                @elseif($sale->status === 'ready_for_delivery') bg-purple-100 text-purple-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                @switch($sale->status)
                                                    @case('option') 📝 Optie @break
                                                    @case('contract_signed') 📋 Contract @break
                                                    @case('ready_for_delivery') 🚀 Klaar @break
                                                    @default 🔍 Onbekend @break
                                                @endswitch
                                            </span>
                                            
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if($sale->payment_status === 'paid') bg-green-100 text-green-800
                                                @elseif($sale->payment_status === 'deposit_paid') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800 @endif">
                                                @switch($sale->payment_status)
                                                    @case('paid') 💰 Betaald @break
                                                    @case('deposit_paid') 💳 Aanbetaling @break
                                                    @default 🔍 Open @break
                                                @endswitch
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        @if($sale->delivery_date)
                                            <div class="flex items-center">
                                                <i class="fa-solid fa-calendar-alt mr-1 text-gray-400"></i>
                                                {{ \Carbon\Carbon::parse($sale->delivery_date)->format('d-m-Y') }}
                                            </div>
                                        @endif
                                        @if($sale->delivery_time)
                                            <div class="flex items-center text-xs text-gray-500">
                                                <i class="fa-solid fa-clock mr-1"></i>
                                                {{ $sale->delivery_time }}
                                            </div>
                                        @endif
                                        @if(!$sale->delivery_date)
                                            <span class="text-gray-400 text-sm">Niet gepland</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                        <a href="{{ route('sales.show', $sale) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('sales.edit', $sale) }}" 
                                           class="text-green-600 hover:text-green-900 transition-colors">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Geen verkoopdossiers gevonden</h3>
                    <p class="text-gray-500 mb-6">
                        @if(request('search') || request('status') || request('payment_status'))
                            Geen verkoopdossiers voldoen aan je zoekcriteria.
                        @else
                            Je hebt nog geen verkoopdossiers aangemaakt.
                        @endif
                    </p>
                    @if(!request('search') && !request('status') && !request('payment_status'))
                        <a href="{{ route('sales.create') }}" 
                           class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 inline-flex items-center gap-2">
                            <i class="fa-solid fa-plus"></i>
                            Maak je eerste verkoop aan
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection