@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-8 text-gray-900">Voorraad Pipeline</h1>
    <div class="grid grid-cols-1 md:grid-cols-{{ count($stages) }} gap-6" x-data="pipelineDrag()">
        @foreach($stages as $stage)
            <div class="bg-white rounded-xl shadow-lg p-4 min-h-[400px] border border-gray-100 flex flex-col" 
                 x-data="{ stageId: {{ $stage->id }} }" 
                 @dragover.prevent 
                 @drop="onDrop($event, stageId)"
                 :class="{ 'ring-2 ring-blue-300 bg-blue-50': draggedCarId && hoveredStage === stageId }"
                 @dragenter="hoveredStage = stageId"
                 @dragleave="if ($event.target === $el) hoveredStage = null">
                
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-lg text-blue-700">{{ $stage->name }}</h2>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        {{ $stage->cars->count() }}
                    </span>
                </div>
                
                <div class="space-y-4 flex-1">
                    @forelse($stage->cars as $car)
                        @php
                            $completion = $car->stage_completion;
                            $canMove = $car->canMoveToNextStage();
                        @endphp
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg shadow p-4 cursor-move border border-blue-200 hover:shadow-md transition-all duration-200" 
                             draggable="true" 
                             @dragstart="onDragStart($event, {{ $car->id }})"
                             @dragend="onDragEnd()"
                             :class="{ 'opacity-50': draggedCarId === {{ $car->id }} }">
                            
                            <div class="font-bold text-blue-900 mb-1">
                                {{ $car->license_plate }} - {{ $car->brand }} {{ $car->model }}
                            </div>
                            
                            <div class="text-sm text-blue-700 mb-2">
                                € {{ number_format($car->price, 2, ',', '.') }}
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-blue-800 mb-1">
                                    <span>Checklist voortgang</span>
                                    <span>{{ $completion }}%</span>
                                </div>
                                <div class="w-full bg-blue-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-300 {{ $completion === 100 ? 'bg-green-500' : 'bg-blue-500' }}" 
                                         style="width: {{ $completion }}%"></div>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            @if($canMove)
                                <div class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full mb-2">
                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                                    Klaar voor volgende fase
                                </div>
                            @else
                                <div class="inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full mb-2">
                                    <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1"></span>
                                    In bewerking
                                </div>
                            @endif
                            
                            <div class="flex justify-between items-center">
                                <div class="text-xs text-blue-800">
                                    {{ $car->checklists->where('stage_id', $stage->id)->where('is_completed', true)->count() }} / 
                                    {{ $car->checklists->where('stage_id', $stage->id)->count() }} taken voltooid
                                </div>
                                <a href="{{ route('pipeline.checklist', $car) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-xs font-medium hover:underline"
                                   @click.stop>
                                    Checklist →
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-400 text-center py-12 border-2 border-dashed border-gray-200 rounded-lg">
                            <div class="text-lg mb-2">📋</div>
                            <div>Geen auto's in deze fase</div>
                            <div class="text-sm">Sleep een auto hiernaartoe</div>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
function pipelineDrag() {
    return {
        draggedCarId: null,
        hoveredStage: null,
        scrollInterval: null,
        
        onDragStart(e, carId) {
            this.draggedCarId = carId;
            e.dataTransfer.effectAllowed = 'move';
            this.startAutoScroll();
        },
        
        onDragEnd() {
            this.draggedCarId = null;
            this.hoveredStage = null;
            this.stopAutoScroll();
        },
        
        startAutoScroll() {
            this.scrollInterval = setInterval(() => {
                if (!this.draggedCarId) return;
                
                const mouseX = event.clientX;
                const scrollContainer = document.documentElement;
                const scrollSpeed = 10;
                const scrollZone = 100; // pixels from edge
                
                // Scroll left
                if (mouseX < scrollZone) {
                    scrollContainer.scrollLeft -= scrollSpeed;
                }
                // Scroll right
                else if (mouseX > window.innerWidth - scrollZone) {
                    scrollContainer.scrollLeft += scrollSpeed;
                }
            }, 16); // ~60fps
        },
        
        stopAutoScroll() {
            if (this.scrollInterval) {
                clearInterval(this.scrollInterval);
                this.scrollInterval = null;
            }
        },
        
        async onDrop(e, stageId) {
            e.preventDefault();
            this.hoveredStage = null;
            this.stopAutoScroll();
            
            if (!this.draggedCarId) return;
            
            try {
                const response = await fetch('/pipeline/move', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ 
                        car_id: this.draggedCarId, 
                        stage_id: stageId 
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Er is een fout opgetreden bij het verplaatsen van de auto.');
                }
            } catch (error) {
                console.error('Error moving car:', error);
                alert('Er is een fout opgetreden bij het verplaatsen van de auto.');
            }
            
            this.draggedCarId = null;
        }
    }
}

// Global mouse tracking for auto-scroll
document.addEventListener('dragover', (e) => {
    window.dragX = e.clientX;
});
</script>
@endsection
