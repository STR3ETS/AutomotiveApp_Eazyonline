@extends('layouts.app') {{-- of je eigen layout --}}
@section('content')
<div class="max-w-7xl mx-auto p-6">
    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-4 text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <h1 class="text-2xl font-bold mb-6">Werkplaats — Reparaties</h1>

    {{-- Nieuw: reparatie toevoegen --}}
    <div class="bg-white rounded-xl shadow p-5 mb-8">
        <h2 class="text-lg font-semibold mb-4">Nieuwe reparatie</h2>

        <form method="POST" action="{{ route('repairs.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Auto</label>
                <select name="car_id" required class="w-full rounded-lg border-gray-300">
                    <option value="">— Kies auto —</option>
                    @foreach($cars as $car)
                        <option value="{{ $car->id }}">
                            {{-- Pas velden aan je Car-model aan --}}
                            {{ $car->license_plate ?? '—' }} — {{ $car->brand ?? '' }} {{ $car->model ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Omschrijving</label>
                <input type="text" name="description" required class="w-full rounded-lg border-gray-300" placeholder="Bijv. Remblokken vervangen, APK, olie + filter" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" required class="w-full rounded-lg border-gray-300">
                    <option value="gepland">Gepland</option>
                    <option value="bezig">Bezig</option>
                    <option value="wachten_op_onderdeel">Wachten op onderdeel</option>
                    <option value="gereed">Gereed</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 mt-2 md:mt-0">Kostenraming (€)</label>
                <input type="number" step="0.01" min="0" name="cost_estimate" class="w-full rounded-lg border-gray-300" placeholder="Bijv. 249.95" />
            </div>

            <div class="md:col-span-3"></div>

            <div class="md:col-span-1 text-right">
                <button class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Opslaan
                </button>
            </div>
        </form>
    </div>

    {{-- Overzicht alle reparaties --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Omschrijving</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Raming (€)</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Onderdelen</th>
                    <th class="px-4 py-3"></th>
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
                        <td class="px-4 py-3 align-top text-right">
                            <form method="POST" action="{{ route('repairs.destroy', $repair) }}" onsubmit="return confirm('Weet je zeker dat je deze reparatie wilt verwijderen?');">
                                @csrf @method('DELETE')
                                <button class="inline-flex items-center text-red-600 hover:text-red-700">Verwijderen</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">Er zijn nog geen reparaties.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection