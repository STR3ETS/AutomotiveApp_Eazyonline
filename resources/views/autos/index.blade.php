@extends('layouts.app')

@section('content')
<div class="min-h-full" style="background-color: var(--third-color);">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header with Add Button -->
        <div class="flex justify-between items-center mb-8 p-6 rounded-xl shadow-sm" style="background: linear-gradient(135deg, var(--primary-color)10, var(--secondary-color)20); border: 1px solid var(--secondary-color);">
            <div>
                <h1 class="text-3xl font-bold mb-2" style="color: var(--primary-color);">🚗 Auto Beheer</h1>
                <p style="color: var(--secondary-color);">Overzicht van alle auto's in je voorraad</p>
            </div>
            <a href="{{ route('autos.create') }}" 
               class="font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2 shadow-sm hover:shadow-md"
               style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: var(--text-kleur-white);"
               onmouseover="this.style.transform='scale(1.05)'"
               onmouseout="this.style.transform='scale(1)'">
                <i class="fa-solid fa-plus"></i>
                Nieuwe Auto Toevoegen
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="px-4 py-3 rounded mb-6 shadow-sm" style="background: linear-gradient(135deg, var(--primary-color)20, var(--secondary-color)10); border: 1px solid var(--primary-color); color: var(--text-kleur-black);">
                <i class="fa-solid fa-check-circle mr-2" style="color: var(--primary-color);"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="px-4 py-3 rounded mb-6 shadow-sm" style="background: linear-gradient(135deg, var(--third-color), var(--secondary-color)20); border: 1px solid var(--primary-color); color: var(--text-kleur-black);">
                <i class="fa-solid fa-exclamation-circle mr-2" style="color: var(--primary-color);"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Search & Filters -->
        <div class="rounded-xl shadow-sm p-6 mb-6 transition-all duration-300 hover:shadow-md" style="background-color: var(--text-kleur-white); border: 1px solid var(--third-color);">
            <form method="GET" action="{{ route('autos.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-64">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search" style="color: var(--secondary-color);"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Zoek op kenteken, merk, model..." 
                               class="w-full pl-10 pr-4 py-2 border rounded-lg transition-all duration-200"
                               style="border-color: var(--third-color); color: var(--text-kleur-black);"
                               onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 2px var(--primary-color)30'"
                               onblur="this.style.borderColor='var(--third-color)'; this.style.boxShadow='none'">
                    </div>
                </div>
                <button type="submit" 
                        class="px-6 py-2 rounded-lg transition duration-200 flex items-center gap-2 shadow-sm hover:shadow-md"
                        style="background-color: var(--primary-color); color: var(--text-kleur-white);"
                        onmouseover="this.style.backgroundColor='var(--secondary-color)'"
                        onmouseout="this.style.backgroundColor='var(--primary-color)'">
                    <i class="fa-solid fa-search"></i>Zoeken
                </button>
                @if(request('search'))
                    <a href="{{ route('autos.index') }}" 
                       class="px-6 py-2 rounded-lg transition duration-200 flex items-center gap-2"
                       style="background-color: var(--third-color); color: var(--text-kleur-black);"
                       onmouseover="this.style.backgroundColor='var(--secondary-color)'; this.style.color='var(--text-kleur-white)'"
                       onmouseout="this.style.backgroundColor='var(--third-color)'; this.style.color='var(--text-kleur-black)'">
                        <i class="fa-solid fa-times"></i>Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Cars Table -->
        <div class="rounded-xl shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md" style="background-color: var(--text-kleur-white); border: 1px solid var(--third-color);">
            @if($cars->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead style="background: linear-gradient(135deg, var(--third-color), var(--secondary-color)20);">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-kleur-black);">
                                    Auto
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-kleur-black);">
                                    Details
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-kleur-black);">
                                    Fase
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-kleur-black);">
                                    Toegewezen aan
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Prijs
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Toegevoegd
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Acties
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cars as $car)
                                <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-16">
                                                <div class="h-12 w-16 rounded-lg overflow-hidden bg-gray-100">
                                                    @if($car->primaryImage)
                                                        <img src="{{ $car->primaryImage->thumbnail_url }}" 
                                                             alt="{{ $car->primaryImage->alt_text }}"
                                                             class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                            <i class="fa-solid fa-car text-lg"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">
                                                    {{ $car->license_plate }}
                                                </div>
                                                <div class="text-sm text-gray-600">
                                                    {{ $car->brand }} {{ $car->model }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $car->year }}</div>
                                        <div class="text-sm text-gray-500">{{ number_format($car->mileage) }} km</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($car->stage)
                                            @php
                                                $stageColors = [
                                                    'Intake' => 'background: linear-gradient(135deg, #fbbf24, #f59e0b); color: var(--text-kleur-white);',
                                                    'Technische controle' => 'background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: var(--text-kleur-white);',
                                                    'Herstel & Onderhoud' => 'background: linear-gradient(135deg, #f97316, #ea580c); color: var(--text-kleur-white);',
                                                    'Commercieel gereed' => 'background: linear-gradient(135deg, #a855f7, #9333ea); color: var(--text-kleur-white);',
                                                    'Verkoop klaar' => 'background: linear-gradient(135deg, #22c55e, #16a34a); color: var(--text-kleur-white);'
                                                ];
                                                $colorStyle = $stageColors[$car->stage->name] ?? 'background-color: var(--third-color); color: var(--text-kleur-black);';
                                            @endphp
                                            <span style="display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; {{ $colorStyle }}">
                                                {{ $car->stage->name }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Onbekend
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($car->currentAssignment && $car->currentAssignment->employee)
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                                    {{ strtoupper(substr($car->currentAssignment->employee->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $car->currentAssignment->employee->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $car->currentAssignment->employee->position }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Niet toegewezen</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        €{{ number_format($car->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $car->created_at ? $car->created_at->format('d-m-Y') : 'Onbekend' }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium space-x-3">
                                        <a href="{{ route('autos.show', $car) }}" 
                                           style="display: inline-flex; align-items: center; padding: 4px 12px; background: linear-gradient(135deg, var(--third-color), var(--text-kleur-white)); color: var(--primary-color); border-radius: 20px; font-size: 12px; font-weight: 500; text-decoration: none; transition: all 0.2s ease;"
                                           onmouseover="this.style.backgroundColor='var(--primary-color)'; this.style.color='var(--text-kleur-white)'"
                                           onmouseout="this.style.background='linear-gradient(135deg, var(--third-color), var(--text-kleur-white))'; this.style.color='var(--primary-color)'"
                                           title="Bekijken">
                                            <i class="fa-solid fa-eye mr-1"></i> Bekijk
                                        </a>
                                        <a href="{{ route('autos.edit', $car) }}" 
                                           style="display: inline-flex; align-items: center; padding: 4px 12px; background: linear-gradient(135deg, #fbbf24, #f59e0b); color: var(--text-kleur-white); border-radius: 20px; font-size: 12px; font-weight: 500; text-decoration: none; transition: all 0.2s ease;"
                                           onmouseover="this.style.background='linear-gradient(135deg, #f59e0b, #d97706)'"
                                           onmouseout="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'"
                                           title="Bewerken">
                                            <i class="fa-solid fa-edit mr-1"></i> Bewerk
                                        </a>
                                        @if($car->images->count() > 0)
                                            <span style="display: inline-flex; align-items: center; padding: 4px 8px; background: linear-gradient(135deg, #22c55e, #16a34a); color: var(--text-kleur-white); border-radius: 20px; font-size: 12px; font-weight: 500;" title="{{ $car->images->count() }} foto's">
                                                <i class="fa-solid fa-images mr-1"></i> {{ $car->images->count() }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-medium" title="Geen foto's">
                                                <i class="fa-solid fa-image mr-1"></i> 0
                                            </span>
                                        @endif
                                        <form method="POST" 
                                              action="{{ route('autos.destroy', $car) }}" 
                                              class="inline"
                                              onsubmit="return confirm('Weet je zeker dat je deze auto wilt verwijderen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    style="display: inline-flex; align-items: center; padding: 4px 12px; background: linear-gradient(135deg, #ef4444, #dc2626); color: var(--text-kleur-white); border-radius: 20px; font-size: 12px; font-weight: 500; border: none; cursor: pointer; transition: all 0.2s ease;"
                                                    onmouseover="this.style.background='linear-gradient(135deg, #dc2626, #b91c1c)'"
                                                    onmouseout="this.style.background='linear-gradient(135deg, #ef4444, #dc2626)'"
                                                    title="Verwijderen">
                                                <i class="fa-solid fa-trash mr-1"></i> Verwijder
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200">
                    {{ $cars->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-20">
                    <div class="text-8xl mb-6 animate-bounce">🚗</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Nog geen auto's toegevoegd</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">Voeg je eerste auto toe om te beginnen met het beheren van je voorraad.</p>
                    <a href="{{ route('autos.create') }}" 
                       class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 inline-flex items-center gap-2 shadow-lg hover:shadow-xl">
                        <i class="fa-solid fa-plus"></i>
                        Eerste Auto Toevoegen
                    </a>
                </div>
            @endif
        </div>

        @if($cars->count() > 0)
            <!-- Stats Footer -->
            <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover-card">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">📊 Voorraad Statistieken</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                        <p class="text-3xl font-bold text-blue-600 mb-1">{{ $cars->total() }}</p>
                        <p class="text-sm font-medium text-blue-800">Totaal Auto's</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200">
                        <p class="text-3xl font-bold text-green-600 mb-1">
                            €{{ number_format($cars->sum('price'), 0, ',', '.') }}
                        </p>
                        <p class="text-sm font-medium text-green-800">Totale Voorraadwaarde</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                        <p class="text-3xl font-bold text-purple-600 mb-1">
                            €{{ $cars->count() > 0 ? number_format($cars->avg('price'), 0, ',', '.') : 0 }}
                        </p>
                        <p class="text-sm font-medium text-purple-800">Gemiddelde Prijs</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
/* Custom animations */
@keyframes slideIn {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes fadeInUp {
    from { 
        transform: translateY(30px); 
        opacity: 0; 
    }
    to { 
        transform: translateY(0); 
        opacity: 1; 
    }
}

.hover-card {
    animation: slideIn 0.5s ease-out;
}

/* Hover effects */
.hover-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

/* Table row animations */
tbody tr {
    animation: fadeInUp 0.3s ease-out;
}

tbody tr:nth-child(odd) { animation-delay: 0.05s; }
tbody tr:nth-child(even) { animation-delay: 0.1s; }

/* Staggered animation delay */
.hover-card:nth-child(1) { animation-delay: 0.1s; }
.hover-card:nth-child(2) { animation-delay: 0.2s; }
.hover-card:nth-child(3) { animation-delay: 0.3s; }
.hover-card:nth-child(4) { animation-delay: 0.4s; }
</style>
@endsection
