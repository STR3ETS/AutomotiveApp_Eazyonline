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
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('autos.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-64">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Zoek op kenteken, merk, model..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fa-solid fa-search mr-2"></i>Zoeken
                </button>
                @if(request('search'))
                    <a href="{{ route('autos.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition duration-200">
                        <i class="fa-solid fa-times mr-2"></i>Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Cars Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($cars->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Auto
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Details
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fase
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prijs
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Toegevoegd
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acties
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cars as $car)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <i class="fa-solid fa-car text-blue-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $car->license_plate }}
                                                </div>
                                                <div class="text-sm text-gray-500">
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
                                    <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                        <a href="{{ route('autos.show', $car) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                           title="Bekijken">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('autos.edit', $car) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200"
                                           title="Bewerken">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('autos.destroy', $car) }}" 
                                              class="inline"
                                              onsubmit="return confirm('Weet je zeker dat je deze auto wilt verwijderen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                    title="Verwijderen">
                                                <i class="fa-solid fa-trash"></i>
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
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">🚗</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Nog geen auto's toegevoegd</h3>
                    <p class="text-gray-600 mb-6">Voeg je eerste auto toe om te beginnen.</p>
                    <a href="{{ route('autos.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 inline-flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        Eerste Auto Toevoegen
                    </a>
                </div>
            @endif
        </div>

        @if($cars->count() > 0)
            <!-- Stats Footer -->
            <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-2xl font-bold text-blue-600">{{ $cars->total() }}</p>
                        <p class="text-sm text-gray-600">Totaal Auto's</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-green-600">
                            €{{ number_format($cars->sum('price'), 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-gray-600">Totale Voorraadwaarde</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-purple-600">
                            €{{ $cars->count() > 0 ? number_format($cars->avg('price'), 0, ',', '.') : 0 }}
                        </p>
                        <p class="text-sm text-gray-600">Gemiddelde Prijs</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
