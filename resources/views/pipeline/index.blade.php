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
                
                <div class="flex flex-wrap gap-3 flex-1">
                    @forelse($stage->cars as $car)
                        @php
                            $completion = $car->stage_completion;
                            $canMove = $car->canMoveToNextStage();
                        @endphp
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg shadow p-3 cursor-move border border-blue-200 hover:shadow-md transition-all duration-200 w-48 flex-shrink-0" 
                             draggable="true" 
                             @dragstart="onDragStart($event, {{ $car->id }})"
                             @dragend="onDragEnd()"
                             :class="{ 'opacity-50': draggedCarId === {{ $car->id }} }">
                            
                            <div class="font-bold text-blue-900 mb-1 text-sm truncate">
                                {{ $car->license_plate }} - {{ $car->brand }} {{ $car->model }}
                            </div>
                            
                            <div class="text-xs text-blue-700 mb-2">
                                € {{ number_format($car->price, 2, ',', '.') }}
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="mb-2">
                                <div class="flex justify-between text-xs text-blue-800 mb-1">
                                    <span class="text-xs">Checklist voortgang</span>
                                    <span class="text-xs">{{ $completion }}%</span>
                                </div>
                                <div class="w-full bg-blue-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full transition-all duration-300 {{ $completion === 100 ? 'bg-green-500' : 'bg-blue-500' }}" 
                                         style="width: {{ $completion }}%"></div>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            @if($canMove)
                                <div class="inline-flex items-center px-1.5 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full mb-2">
                                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></span>
                                    <span class="text-xs">Klaar</span>
                                </div>
                            @else
                                <div class="inline-flex items-center px-1.5 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full mb-2">
                                    <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1"></span>
                                    <span class="text-xs">Bezig</span>
                                </div>
                            @endif
                            
                            <div class="flex flex-col gap-1">
                                <div class="text-xs text-blue-800">
                                    {{ $car->checklists->where('stage_id', $stage->id)->where('is_completed', true)->count() }} / 
                                    {{ $car->checklists->where('stage_id', $stage->id)->count() }} taken voltooid
                                </div>
                                <a href="{{ route('pipeline.checklist', $car) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-xs font-medium hover:underline text-center py-1"
                                   @click.stop>
                                    Checklist →
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-400 text-center py-8 border-2 border-dashed border-gray-200 rounded-lg w-full">
                            <div class="text-lg mb-2">📋</div>
                            <div class="text-sm">Geen auto's in deze fase</div>
                            <div class="text-xs">Sleep een auto hiernaartoe</div>
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
                    // Toon success bericht
                    if (data.message) {
                        // Voeg een tijdelijke success melding toe
                        const successDiv = document.createElement('div');
                        successDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
                        successDiv.innerHTML = `<i class="fa-solid fa-check-circle mr-2"></i>${data.message}`;
                        document.body.appendChild(successDiv);
                        
                        // Verwijder melding na 3 seconden
                        setTimeout(() => {
                            successDiv.remove();
                        }, 3000);
                    }
                    
                    // Herlaad de pagina om de nieuwe positie te tonen
                    window.location.reload();
                } else {
                    // Toon error bericht in een mooie modal
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50 max-w-md';
                    errorDiv.innerHTML = `<i class="fa-solid fa-exclamation-circle mr-2"></i>${data.message}`;
                    document.body.appendChild(errorDiv);
                    
                    // Verwijder melding na 5 seconden
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 5000);
                }
            } catch (error) {
                console.error('Error moving car:', error);
                
                // Toon error bericht
                const errorDiv = document.createElement('div');
                errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50';
                errorDiv.innerHTML = '<i class="fa-solid fa-exclamation-circle mr-2"></i>Er is een fout opgetreden bij het verplaatsen van de auto.';
                document.body.appendChild(errorDiv);
                
                setTimeout(() => {
                    errorDiv.remove();
                }, 5000);
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
