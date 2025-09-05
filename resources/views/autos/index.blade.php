@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-full">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header with Add Button -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">🚗 Auto Beheer</h1>
                <p class="text-gray-600">Overzicht van alle auto's in je voorraad</p>
            </div>
            <a href="{{ route('autos.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                Nieuwe Auto Toevoegen
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

        <!-- Search & Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 hover-card">
            <form method="GET" action="{{ route('autos.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-64">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Zoek op kenteken, merk, model..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    </div>
                </div>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200 flex items-center gap-2 shadow-sm hover:shadow-md">
                    <i class="fa-solid fa-search"></i>Zoeken
                </button>
                @if(request('search'))
                    <a href="{{ route('autos.index') }}" 
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg transition duration-200 flex items-center gap-2">
                        <i class="fa-solid fa-times"></i>Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Cars Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover-card">
            @if($cars->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Auto
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Details
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Fase
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Prijs
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
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
                                            <div class="flex-shrink-0 h-12 w-12">
                                                <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-100 to-blue-200 flex items-center justify-center shadow-sm">
                                                    <i class="fa-solid fa-car text-blue-600 text-lg"></i>
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
                                                    'Intake' => 'bg-yellow-100 text-yellow-800',
                                                    'Technische controle' => 'bg-blue-100 text-blue-800',
                                                    'Herstel & Onderhoud' => 'bg-orange-100 text-orange-800',
                                                    'Commercieel gereed' => 'bg-purple-100 text-purple-800',
                                                    'Verkoop klaar' => 'bg-green-100 text-green-800'
                                                ];
                                                $colorClass = $stageColors[$car->stage->name] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                                {{ $car->stage->name }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Onbekend
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        €{{ number_format($car->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $car->created_at->format('d-m-Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium space-x-3">
                                        <a href="{{ route('autos.show', $car) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium hover:bg-blue-200 transition-all duration-200"
                                           title="Bekijken">
                                            <i class="fa-solid fa-eye mr-1"></i> Bekijk
                                        </a>
                                        <a href="{{ route('autos.edit', $car) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium hover:bg-yellow-200 transition-all duration-200"
                                           title="Bewerken">
                                            <i class="fa-solid fa-edit mr-1"></i> Bewerk
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('autos.destroy', $car) }}" 
                                              class="inline"
                                              onsubmit="return confirm('Weet je zeker dat je deze auto wilt verwijderen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium hover:bg-red-200 transition-all duration-200"
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
