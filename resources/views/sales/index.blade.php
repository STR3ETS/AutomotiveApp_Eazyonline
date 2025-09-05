@extends('layouts.app')

@section('content')
<div class="bg-[var(--background-main)] min-h-full">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header with Add Button -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[var(--text-primary)] mb-2">💼 Verkoopdossiers</h1>
                <p class="text-[var(--text-secondary)]">Overzicht van alle actieve verkopen</p>
            </div>
            <a href="{{ route('sales.create') }}" 
               class="btn-success flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                Nieuwe Verkoop
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-[var(--status-success-bg)] border border-[var(--status-success-border)] text-[var(--status-success-text)] px-4 py-3 rounded mb-6">
                <i class="fa-solid fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-[var(--status-danger-bg)] border border-[var(--status-danger-light)] text-[var(--status-danger-text)] px-4 py-3 rounded mb-6">
                <i class="fa-solid fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6 mb-6 card-hover">
            <form method="GET" action="{{ route('sales.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-64">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-[var(--text-tertiary)]"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Zoek op kenteken, merk, model of klant..." 
                               class="w-full pl-10 pr-4 py-2 border border-[var(--border-medium)] rounded-lg focus:ring-2 focus:ring-[var(--status-info)] focus:border-[var(--status-info)] transition-all duration-[var(--transition-normal)]">
                    </div>
                </div>
                <select name="status" class="px-4 py-2 border border-[var(--border-medium)] rounded-lg focus:ring-2 focus:ring-[var(--status-info)] focus:border-[var(--status-info)] transition-all duration-[var(--transition-normal)]">
                    <option value="">Alle statussen</option>
                    <option value="option" {{ request('status') == 'option' ? 'selected' : '' }}>📝 Optie</option>
                    <option value="contract_signed" {{ request('status') == 'contract_signed' ? 'selected' : '' }}>📋 Contract getekend</option>
                    <option value="ready_for_delivery" {{ request('status') == 'ready_for_delivery' ? 'selected' : '' }}>🚀 Klaar voor levering</option>
                </select>
                <select name="payment_status" class="px-4 py-2 border border-[var(--border-medium)] rounded-lg focus:ring-2 focus:ring-[var(--status-info)] focus:border-[var(--status-info)] transition-all duration-[var(--transition-normal)]">
                    <option value="">Alle betalingen</option>
                    <option value="open" {{ request('payment_status') == 'open' ? 'selected' : '' }}>🔍 Open</option>
                    <option value="deposit_paid" {{ request('payment_status') == 'deposit_paid' ? 'selected' : '' }}>💳 Aanbetaling</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>💰 Volledig betaald</option>
                </select>
                <button type="submit" 
                        class="bg-[var(--status-info)] hover:bg-[var(--status-info-dark)] text-[var(--text-white)] px-6 py-2 rounded-lg transition duration-[var(--transition-normal)] flex items-center gap-2 shadow-sm hover:shadow-md">
                    <i class="fa-solid fa-search"></i>Zoeken
                </button>
                @if(request('search') || request('status') || request('payment_status'))
                    <a href="{{ route('sales.index') }}" 
                       class="bg-[var(--background-secondary)] hover:bg-[var(--border-medium)] text-[var(--text-secondary)] px-6 py-2 rounded-lg transition duration-[var(--transition-normal)] flex items-center gap-2">
                        <i class="fa-solid fa-times"></i>Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Sales Table -->
        <div class="bg-[var(--background-card)] rounded-lg shadow-sm border border-[var(--border-light)] overflow-hidden">
            @if($sales->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[var(--border-light)]">
                        <thead class="bg-[var(--background-secondary)]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                    Auto & Klant
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                    Financiën
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                    Levering
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-[var(--text-secondary)] uppercase tracking-wider">
                                    Acties
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-[var(--background-card)] divide-y divide-[var(--border-light)]">
                            @foreach($sales as $sale)
                                <tr class="hover:bg-[var(--background-secondary)] transition-colors duration-[var(--transition-normal)]">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                <div class="h-12 w-12 rounded-full bg-[var(--status-info-light)] flex items-center justify-center">
                                                    <i class="fa-solid fa-car text-[var(--status-info)]"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-[var(--text-primary)]">
                                                    {{ $sale->car->license_plate }}
                                                </div>
                                                <div class="text-sm text-[var(--text-secondary)]">
                                                    {{ $sale->car->brand }} {{ $sale->car->model }} ({{ $sale->car->year }})
                                                </div>
                                                <div class="text-sm text-[var(--text-secondary)] mt-1">
                                                    <i class="fa-solid fa-user mr-1"></i>{{ $sale->customer->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-[var(--text-primary)]">
                                            <div class="font-semibold">€ {{ number_format($sale->sale_price, 2) }}</div>
                                            @if($sale->deposit_amount)
                                                <div class="text-[var(--status-success)] text-xs">
                                                    Aanbetaling: € {{ number_format($sale->deposit_amount, 2) }}
                                                </div>
                                                <div class="text-[var(--status-danger)] text-xs">
                                                    Restant: € {{ number_format($sale->sale_price - $sale->deposit_amount, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if($sale->status === 'option') bg-[var(--background-secondary)] text-[var(--text-secondary)]
                                                @elseif($sale->status === 'contract_signed') bg-[var(--status-info-light)] text-[var(--status-info-text)]
                                                @elseif($sale->status === 'ready_for_delivery') bg-[var(--status-special-light)] text-[var(--status-special-text)]
                                                @else bg-[var(--status-warning-light)] text-[var(--status-warning-text)] @endif">
                                                @switch($sale->status)
                                                    @case('option') 📝 Optie @break
                                                    @case('contract_signed') 📋 Contract @break
                                                    @case('ready_for_delivery') 🚀 Klaar @break
                                                    @default 🔍 Onbekend @break
                                                @endswitch
                                            </span>
                                            
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if($sale->payment_status === 'paid') bg-[var(--status-success-light)] text-[var(--status-success-text)]
                                                @elseif($sale->payment_status === 'deposit_paid') bg-[var(--status-warning-light)] text-[var(--status-warning-text)]
                                                @else bg-[var(--status-danger-light)] text-[var(--status-danger-text)] @endif">
                                                @switch($sale->payment_status)
                                                    @case('paid') 💰 Betaald @break
                                                    @case('deposit_paid') 💳 Aanbetaling @break
                                                    @default 🔍 Open @break
                                                @endswitch
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-[var(--text-primary)]">
                                        @if($sale->delivery_date)
                                            <div class="flex items-center">
                                                <i class="fa-solid fa-calendar-alt mr-1 text-[var(--text-tertiary)]"></i>
                                                {{ \Carbon\Carbon::parse($sale->delivery_date)->format('d-m-Y') }}
                                            </div>
                                        @endif
                                        @if($sale->delivery_time)
                                            <div class="flex items-center text-xs text-[var(--text-secondary)]">
                                                <i class="fa-solid fa-clock mr-1"></i>
                                                {{ $sale->delivery_time }}
                                            </div>
                                        @endif
                                        @if(!$sale->delivery_date)
                                            <span class="text-[var(--text-tertiary)] text-sm">Niet gepland</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                        <a href="{{ route('sales.show', $sale) }}" 
                                           class="text-[var(--status-info)] hover:text-[var(--status-info-dark)] transition-colors">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('sales.edit', $sale) }}" 
                                           class="text-[var(--status-success)] hover:text-[var(--status-success-dark)] transition-colors">
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
                    <div class="text-[var(--text-tertiary)] text-6xl mb-4">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <h3 class="text-lg font-medium text-[var(--text-primary)] mb-2">Geen verkoopdossiers gevonden</h3>
                    <p class="text-[var(--text-secondary)] mb-6">
                        @if(request('search') || request('status') || request('payment_status'))
                            Geen verkoopdossiers voldoen aan je zoekcriteria.
                        @else
                            Je hebt nog geen verkoopdossiers aangemaakt.
                        @endif
                    </p>
                    @if(!request('search') && !request('status') && !request('payment_status'))
                        <a href="{{ route('sales.create') }}" 
                           class="btn-success inline-flex items-center gap-2">
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