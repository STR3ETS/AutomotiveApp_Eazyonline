@extends('layouts.app')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Nieuwe verkoop</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong>Er zijn fouten opgetreden:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('sales.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block font-semibold">Auto</label>
            <select name="car_id" class="border rounded w-full px-2 py-1" required>
                <option value="">Selecteer een auto</option>
                @forelse($cars as $car)
                    <option value="{{ $car->id }}">{{ $car->license_plate }} - {{ $car->brand }} {{ $car->model }} (Status: {{ $car->status }})</option>
                @empty
                    <option value="">Geen auto's beschikbaar</option>
                @endforelse
            </select>
            <small class="text-gray-500">{{ count($cars) }} auto(s) beschikbaar</small>
        </div>

        <div x-data="{ newCustomer:false }">
            <label class="block font-semibold">Klant</label>
            <select x-show="!newCustomer" name="customer_id" class="border rounded w-full px-2 py-1 mb-2">
                <option value="">Selecteer een bestaande klant of maak een nieuwe aan</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->email }}</option>
                @endforeach
            </select>

            <button type="button" class="text-blue-600 text-sm underline mb-2" @click="newCustomer = !newCustomer">
                <span x-text="newCustomer ? 'Bestaande klant kiezen' : 'Nieuwe klant invoeren'"></span>
            </button>

            <div x-show="newCustomer" class="mt-2 space-y-2">
                <input type="text" name="customer_name" placeholder="Naam *" class="border rounded w-full px-2 py-1" x-bind:required="newCustomer" x-bind:disabled="!newCustomer">
                <input type="email" name="customer_email" placeholder="E-mail *" class="border rounded w-full px-2 py-1" x-bind:required="newCustomer" x-bind:disabled="!newCustomer">
                <input type="text" name="customer_phone" placeholder="Telefoon" class="border rounded w-full px-2 py-1" x-bind:disabled="!newCustomer">
                <textarea name="customer_address" placeholder="Adres" class="border rounded w-full px-2 py-1" x-bind:disabled="!newCustomer"></textarea>
            </div>
        </div>

        <div>
            <label class="block font-semibold">Prijs</label>
            <input type="number" name="sale_price" step="0.01" class="border rounded w-full px-2 py-1">
        </div>

        <div>
            <label class="block font-semibold">Aanbetaling</label>
            <input type="number" name="deposit_amount" step="0.01" class="border rounded w-full px-2 py-1">
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block font-semibold">Afleverdatum</label>
                <input type="date" name="delivery_date" class="border rounded w-full px-2 py-1">
            </div>
            <div>
                <label class="block font-semibold">Aflevertijd</label>
                <input type="time" name="delivery_time" class="border rounded w-full px-2 py-1">
            </div>
        </div>

        <div>
            <label class="block font-semibold">Notities</label>
            <textarea name="notes" class="border rounded w-full px-2 py-1"></textarea>
        </div>

        <button class="bg-green-600 text-white px-4 py-2 rounded">Opslaan</button>
    </form>
</div>
@endsection