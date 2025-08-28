@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-8 text-gray-900">Pipeline</h1>
    <div class="grid grid-cols-1 md:grid-cols-{{ count($stages) }} gap-8" x-data="pipelineDrag()">
        @foreach($stages as $stage)
            <div class="bg-white rounded-xl shadow-lg p-4 min-h-[350px] border border-gray-100 flex flex-col" x-data="{ stageId: {{ $stage->id }} }" @dragover.prevent @drop="onDrop($event, stageId)">
                <h2 class="font-semibold text-lg mb-4 text-blue-700">{{ $stage->name }}</h2>
                <div class="space-y-4 flex-1">
                    @forelse($stage->cars as $car)
                        <div class="bg-blue-50 rounded-lg shadow p-4 cursor-move border border-blue-100 hover:bg-blue-100 transition" draggable="true" @dragstart="onDragStart($event, {{ $car->id }})">
                            <div class="font-bold text-blue-900">{{ $car->license_plate }} - {{ $car->brand }} {{ $car->model }}</div>
                            <div class="text-sm text-blue-700">€ {{ number_format($car->price, 2, ',', '.') }}</div>
                            <div class="mt-2 text-xs text-blue-800">
                                Checklist: {{ $car->checklists->where('stage_id', $stage->id)->where('is_completed', true)->count() }} / {{ $car->checklists->where('stage_id', $stage->id)->count() }}
                            </div>
                            <a href="#" class="mt-2 inline-block text-blue-600 hover:underline text-xs">Checklist bekijken</a>
                        </div>
                    @empty
                        <div class="text-gray-400 text-center py-8">Geen auto's in deze fase</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
function pipelineDrag() {
    return {
        draggedCarId: null,
        onDragStart(e, carId) {
            this.draggedCarId = carId;
        },
        async onDrop(e, stageId) {
            if (!this.draggedCarId) return;
            // Stuur AJAX request om auto te verplaatsen
            await fetch('/pipeline/move', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                },
                body: JSON.stringify({ car_id: this.draggedCarId, stage_id: stageId })
            });
            window.location.reload();
        }
    }
}
</script>
@endsection
