@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold mb-6">Agenda deze week</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('agenda.store') }}" class="mb-8 bg-white rounded shadow p-4 flex flex-wrap gap-4 items-end">
        @csrf
        <div>
            <label class="block text-xs font-semibold mb-1">Auto</label>
            <select name="car_id" class="border rounded p-2" required>
                <option value="">Kies auto...</option>
                @foreach($cars as $car)
                    <option value="{{ $car->id }}">{{ $car->license_plate ?? $car->kenteken }} - {{ $car->brand ?? $car->merk }} {{ $car->model }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1">Type</label>
            <select name="type" class="border rounded p-2" required>
                <option value="proefrit">Proefrit</option>
                <option value="aflevering">Aflevering</option>
                <option value="werkplaats">Werkplaats</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1">Datum</label>
            <input type="date" name="date" class="border rounded p-2" required>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1">Tijd</label>
            <input type="time" name="time" class="border rounded p-2" required>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1">Klant</label>
            <select name="customer_id" id="customer_select" class="border rounded p-2" onchange="toggleCustomerInput()">
                <option value="">Bestaande klant...</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
                <option value="new">+ Nieuwe klant</option>
            </select>
        </div>
        <div id="new_customer_input" style="display: none;">
            <label class="block text-xs font-semibold mb-1">Nieuwe klant naam</label>
            <input type="text" name="customer_name" class="border rounded p-2">
        </div>
        <div class="flex-1">
            <label class="block text-xs font-semibold mb-1">Notities</label>
            <input type="text" name="notes" class="border rounded p-2 w-full">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded font-semibold">Toevoegen</button>
    </form>

    <div class="bg-white rounded shadow divide-y">
        @forelse($appointments as $appointment)
            <div class="flex items-center gap-4 p-4">
                <span class="px-2 py-1 rounded text-xs font-bold {{
                    $appointment->type === 'proefrit' ? 'bg-blue-100 text-blue-700' :
                    ($appointment->type === 'aflevering' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700')
                }}">
                    {{ ucfirst($appointment->type) }}
                </span>
                <span class="w-28 text-sm text-gray-700">{{ $appointment->date }} {{ \Carbon\Carbon::parse($appointment->time)->format("H:i") }}</span>
                <span class="w-40 text-sm text-gray-900 font-semibold">
                    {{ $appointment->customer ? $appointment->customer->name : $appointment->customer_name }}
                </span>
                <span class="w-56 text-sm text-gray-600">
                    @if($appointment->car)
                        {{ $appointment->car->license_plate ?? $appointment->car->kenteken }} - {{ $appointment->car->brand ?? $appointment->car->merk }} {{ $appointment->car->model }}
                    @else
                        <span class="text-red-500">Auto niet gevonden</span>
                    @endif
                </span>
                <form method="POST" action="{{ route('agenda.destroy', $appointment->id) }}" onsubmit="return confirm('Weet je zeker dat je deze afspraak wilt verwijderen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline ml-2">Verwijder</button>
                </form>
            </div>
        @empty
            <div class="p-6 text-gray-400 text-center">Geen afspraken deze week.</div>
        @endforelse
    </div>
</div>

<script>
function toggleCustomerInput() {
    const select = document.getElementById('customer_select');
    const newCustomerInput = document.getElementById('new_customer_input');
    const customerNameInput = document.querySelector('input[name="customer_name"]');
    
    if (select.value === 'new') {
        newCustomerInput.style.display = 'block';
        customerNameInput.required = true;
    } else {
        newCustomerInput.style.display = 'none';
        customerNameInput.required = false;
        customerNameInput.value = '';
    }
}
</script>
@endsection