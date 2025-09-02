@extends('layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Verkoopdossier</h1>
        <div class="space-x-2">
            <form method="POST" action="{{ route('sales.deliver',$sale) }}" class="inline">
                @csrf
                <button class="bg-green-600 text-white px-3 py-1 rounded"
                    @disabled($sale->payment_status!=='paid' || $sale->checklistItems->where('is_completed',false)->count()>0 || $sale->status==='delivered')>
                    Markeer als afgeleverd
                </button>
            </form>
            <form method="POST" action="{{ route('sales.cancel',$sale) }}" class="inline">
                @csrf
                <button class="bg-red-600 text-white px-3 py-1 rounded">Annuleer</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <div>
            <h2 class="font-semibold mb-2">Auto</h2>
            <p>{{ $sale->car->license_plate }} - {{ $sale->car->brand }} {{ $sale->car->model }}</p>
            <p>Status auto: {{ $sale->car->status }}</p>
        </div>
        <div>
            <h2 class="font-semibold mb-2">Klant</h2>
            <p>{{ $sale->customer->name }}</p>
            <p>{{ $sale->customer->email }}</p>
            <p>{{ $sale->customer->phone }}</p>
        </div>
    </div>

    <div>
        <h2 class="font-semibold mb-2">Checklist</h2>
        <div x-data>
            @foreach($sale->checklistItems as $item)
                <label class="flex items-center gap-2 mb-1">
                    <input type="checkbox" 
                           @change="fetch('{{ route('sales.checklist.toggle',$item) }}',{method:'PUT',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>location.reload())"
                           @checked($item->is_completed)>
                    <span>{{ $item->task }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div>
        <h2 class="font-semibold mb-2">Betaling</h2>
        <p>Status: <strong>{{ ucfirst($sale->payment_status) }}</strong></p>
        <form method="POST" action="{{ route('sales.update',$sale) }}">
            @csrf
            @method('PUT')
            <select name="payment_status" class="border rounded px-2 py-1">
                <option value="open" @selected($sale->payment_status=='open')>Open</option>
                <option value="deposit_paid" @selected($sale->payment_status=='deposit_paid')>Aanbetaling</option>
                <option value="paid" @selected($sale->payment_status=='paid')>Betaald</option>
            </select>
            <button class="bg-blue-600 text-white px-2 py-1 rounded text-sm">Opslaan</button>
        </form>
    </div>
</div>
@endsection