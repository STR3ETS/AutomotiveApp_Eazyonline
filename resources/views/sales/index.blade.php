@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Verkoopdossiers</h1>

    <div class="mb-4 flex justify-between items-center">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" placeholder="Zoek op kenteken of klant"
                   class="border rounded px-3 py-1 text-sm" value="{{ request('q') }}">
            <select name="status" class="border rounded px-2 py-1 text-sm">
                <option value="">Alle statussen</option>
                <option value="option">Optie</option>
                <option value="contract_signed">Contract</option>
                <option value="ready_for_delivery">Klaar voor levering</option>
            </select>
            <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Filter</button>
        </form>
        <a href="{{ route('sales.create') }}" class="bg-green-600 text-white px-3 py-1 rounded text-sm">
            + Nieuwe verkoop
        </a>
    </div>

    <table class="w-full border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-2 py-1 text-left">Auto</th>
                <th class="px-2 py-1 text-left">Klant</th>
                <th class="px-2 py-1">Status</th>
                <th class="px-2 py-1">Betaling</th>
                <th class="px-2 py-1">Aflevering</th>
                <th class="px-2 py-1">Checklist</th>
                <th class="px-2 py-1">Acties</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
            <tr class="border-t">
                <td class="px-2 py-1">
                    {{ $sale->car->license_plate }} - {{ $sale->car->brand }} {{ $sale->car->model }}
                </td>
                <td class="px-2 py-1">
                    {{ $sale->customer->name }}
                </td>
                <td class="px-2 py-1">
                    <span class="px-2 py-1 rounded text-xs 
                        @if($sale->status=='option') bg-gray-200 text-gray-700
                        @elseif($sale->status=='contract_signed') bg-blue-200 text-blue-700
                        @elseif($sale->status=='ready_for_delivery') bg-orange-200 text-orange-700
                        @endif">
                        {{ ucfirst(str_replace('_',' ',$sale->status)) }}
                    </span>
                </td>
                <td class="px-2 py-1">
                    <span class="px-2 py-1 rounded text-xs
                        @if($sale->payment_status=='open') bg-red-200 text-red-700
                        @elseif($sale->payment_status=='deposit_paid') bg-yellow-200 text-yellow-700
                        @elseif($sale->payment_status=='paid') bg-green-200 text-green-700
                        @endif">
                        {{ ucfirst(str_replace('_',' ',$sale->payment_status)) }}
                    </span>
                </td>
                <td class="px-2 py-1">
                    {{ $sale->delivery_date ? $sale->delivery_date->format('d-m-Y') : '-' }}
                </td>
                <td class="px-2 py-1">
                    {{ $sale->checklistItems->where('is_completed',true)->count() }}
                    / {{ $sale->checklistItems->count() }}
                </td>
                <td class="px-2 py-1">
                    <a href="{{ route('sales.show',$sale) }}" class="text-blue-600 underline">Bekijken</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-2 py-4 text-center text-gray-500">Geen actieve verkopen</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection