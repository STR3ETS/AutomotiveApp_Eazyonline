@extends('layouts.app')
@section('content')
<div class="bg-gray-50 min-h-full">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🔧 Werkplaats — Reparaties</h1>
            <p class="text-gray-600">Beheer alle reparaties en onderhoudswerkzaamheden</p>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl flex items-center">
                <i class="fa-solid fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl">
                <div class="flex items-center mb-2">
                    <i class="fa-solid fa-exclamation-circle mr-2"></i>
                    <span class="font-semibold">Er zijn fouten opgetreden:</span>
                </div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- Nieuw: reparatie toevoegen --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 hover-card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fa-solid fa-plus-circle text-blue-600 mr-2"></i>
                Nieuwe reparatie
            </h2>

            <form method="POST" action="{{ route('repairs.store') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Auto</label>
                    <select name="car_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="">— Kies auto —</option>
                        @foreach($cars as $car)
                            <option value="{{ $car->id }}">
                                {{ $car->license_plate ?? '—' }} — {{ $car->brand ?? '' }} {{ $car->model ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Omschrijving</label>
                    <input type="text" name="description" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Bijv. Remblokken vervangen, APK, olie + filter" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="gepland">📅 Gepland</option>
                        <option value="bezig">🔧 Bezig</option>
                        <option value="wachten_op_onderdeel">⏳ Wachten op onderdeel</option>
                        <option value="gereed">✅ Gereed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kostenraming (€)</label>
                    <input type="number" step="0.01" min="0" name="cost_estimate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Bijv. 249.95" />
                </div>

                <div class="md:col-span-3"></div>

                <div class="text-right">
                    <button class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                        <i class="fa-solid fa-save"></i>
                        Reparatie Opslaan
                    </button>
                </div>
            </form>
        </div>

        {{-- Overzicht alle reparaties --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover-card">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">📋 Alle Reparaties</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Auto</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Omschrijving</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Raming (€)</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Onderdelen</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acties</th>
                        </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($repairs as $repair)
                    @php
                        $statusClasses = [
                            'gepland' => 'bg-gray-100 text-gray-800',
                            'bezig' => 'bg-blue-100 text-blue-800',
                            'wachten_op_onderdeel' => 'bg-orange-100 text-orange-800',
                            'gereed' => 'bg-green-100 text-green-800',
                        ];
                    @endphp
                    <tr>
                        <td class="px-4 py-3 align-top">
                            <div class="font-medium text-gray-900">{{ $repair->car->license_plate ?? '—' }}</div>
                            <div class="text-sm text-gray-500">{{ ($repair->car->make ?? '') . ' ' . ($repair->car->model ?? '') }}</div>
                            <div class="text-xs text-gray-400">#{{ $repair->id }} • {{ $repair->created_at->format('d-m-Y H:i') }}</div>
                        </td>
                        <td class="px-4 py-3 align-top">
                            <div class="text-gray-900">{{ $repair->description }}</div>
                            {{-- Optioneel: inline bewerken van omschrijving --}}
                            <form method="POST" action="{{ route('repairs.update', $repair) }}" class="mt-2 flex items-center gap-2">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="{{ $repair->status }}">
                                <input type="text" name="description" value="{{ $repair->description }}" class="rounded-md border-gray-300 w-64">
                                <button class="text-xs px-3 py-1 rounded-md bg-gray-200 hover:bg-gray-300">Opslaan</button>
                            </form>
                        </td>
                        <td class="px-4 py-3 align-top">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$repair->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ str_replace('_', ' ', ucfirst($repair->status)) }}
                            </span>

                            {{-- Status bijwerken --}}
                            <form method="POST" action="{{ route('repairs.update', $repair) }}" class="mt-2">
                                @csrf @method('PUT')
                                <label class="text-xs text-gray-500">Wijzig status</label>
                                <div class="flex items-center gap-2 mt-1">
                                    <select name="status" class="rounded-md border-gray-300 text-sm">
                                        <option value="gepland" @selected($repair->status==='gepland')>Gepland</option>
                                        <option value="bezig" @selected($repair->status==='bezig')>Bezig</option>
                                        <option value="wachten_op_onderdeel" @selected($repair->status==='wachten_op_onderdeel')>Wachten op onderdeel</option>
                                        <option value="gereed" @selected($repair->status==='gereed')>Gereed</option>
                                    </select>
                                    <input type="hidden" name="cost_estimate" value="{{ $repair->cost_estimate }}">
                                    <button class="text-xs px-3 py-1 rounded-md bg-blue-600 text-white hover:bg-blue-700">Update</button>
                                </div>
                            </form>
                        </td>
                        <td class="px-4 py-3 align-top text-right">
                            <div class="text-gray-900 font-medium">
                                {{ $repair->cost_estimate ? number_format($repair->cost_estimate, 2, ',', '.') : '—' }}
                            </div>
                            <form method="POST" action="{{ route('repairs.update', $repair) }}" class="mt-2 flex items-center gap-2 justify-end">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="{{ $repair->status }}">
                                <input type="number" step="0.01" min="0" name="cost_estimate" value="{{ $repair->cost_estimate }}" class="rounded-md border-gray-300 w-28 text-right">
                                <button class="text-xs px-3 py-1 rounded-md bg-blue-600 text-white hover:bg-blue-700">Opslaan</button>
                            </form>
                            <div class="text-xs text-gray-500 mt-1">Onderdelen: € {{ number_format($repair->parts_total, 2, ',', '.') }}</div>
                        </td>
                        <td class="px-4 py-3 align-top">
                            {{-- Onderdelenlijst --}}
                            <div class="space-y-2">
                                @forelse($repair->parts as $part)
                                    @php
                                        $pClasses = [
                                            'besteld' => 'bg-yellow-50 text-yellow-800 border border-yellow-200',
                                            'geleverd' => 'bg-blue-50 text-blue-800 border border-blue-200',
                                            'gemonteerd' => 'bg-green-50 text-green-800 border border-green-200',
                                        ];
                                    @endphp
                                    <div class="p-3 rounded-lg {{ $pClasses[$part->status] ?? 'bg-gray-50 text-gray-800 border border-gray-200' }}">
                                        <div class="flex items-center justify-between">
                                            <div class="font-medium">{{ $part->name }}</div>
                                            <div class="text-sm">€ {{ $part->price ? number_format($part->price, 2, ',', '.') : '—' }}</div>
                                        </div>
                                        <form method="POST" action="{{ route('parts.update', $part) }}" class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-2">
                                            @csrf @method('PUT')
                                            <input type="text" name="name" value="{{ $part->name }}" class="rounded-md border-gray-300 w-full" />
                                            <select name="status" class="rounded-md border-gray-300">
                                                <option value="besteld" @selected($part->status==='besteld')>Besteld</option>
                                                <option value="geleverd" @selected($part->status==='geleverd')>Geleverd</option>
                                                <option value="gemonteerd" @selected($part->status==='gemonteerd')>Gemonteerd</option>
                                            </select>
                                            <div class="flex gap-2">
                                                <input type="number" step="0.01" min="0" name="price" value="{{ $part->price }}" class="rounded-md border-gray-300 w-28" placeholder="Prijs" />
                                                <button class="text-xs px-3 py-1 rounded-md bg-gray-200 hover:bg-gray-300">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                @empty
                                    <div class="text-sm text-gray-500">Nog geen onderdelen.</div>
                                @endforelse
                            </div>

                            {{-- Onderdeel toevoegen --}}
                            <div class="mt-3">
                                <form method="POST" action="{{ route('repairs.parts.store', $repair) }}" class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                    @csrf
                                    <input type="text" name="name" required class="rounded-md border-gray-300" placeholder="Onderdeelnaam (bijv. Remblok set)" />
                                    <select name="status" class="rounded-md border-gray-300">
                                        <option value="besteld">Besteld</option>
                                        <option value="geleverd">Geleverd</option>
                                        <option value="gemonteerd">Gemonteerd</option>
                                    </select>
                                    <div class="flex gap-2">
                                        <input type="number" step="0.01" min="0" name="price" class="rounded-md border-gray-300 w-28" placeholder="Prijs" />
                                        <button class="text-xs px-3 py-1 rounded-md bg-blue-600 text-white hover:bg-blue-700">Toevoegen</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top text-center">
                            <form method="POST" action="{{ route('repairs.destroy', $repair) }}" onsubmit="return confirm('Weet je zeker dat je deze reparatie wilt verwijderen?');">
                                @csrf @method('DELETE')
                                <button class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium hover:bg-red-200 transition-all duration-200">
                                    <i class="fa-solid fa-trash mr-1"></i>
                                    Verwijderen
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-6xl mb-4">🔧</div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Nog geen reparaties</h3>
                            <p class="text-gray-600">Voeg je eerste reparatie toe om te beginnen.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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

/* Hover effects for table rows */
tbody tr:hover {
    background: linear-gradient(to right, rgb(239 246 255), rgb(238 242 255));
    transform: scale(1.01);
    transition: all 0.2s ease;
}

/* Form elements animations */
.hover-card form input,
.hover-card form select {
    transition: all 0.2s ease;
}

.hover-card form input:focus,
.hover-card form select:focus {
    transform: scale(1.02);
}

/* Staggered animation delay */
.hover-card:nth-child(1) { animation-delay: 0.1s; }
.hover-card:nth-child(2) { animation-delay: 0.2s; }
.hover-card:nth-child(3) { animation-delay: 0.3s; }
</style>
@endsection