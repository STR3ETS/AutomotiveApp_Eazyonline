@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-full">
    <div class="max-w-full mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🏭 Voorraad Pipeline</h1>
            <p class="text-gray-600">Sleep auto's tussen de verschillende fases om je voorraad te beheren</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-{{ count($stages) }} gap-6" x-data="pipelineDrag()">
            @foreach($stages as $stage)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 min-h-[500px] flex flex-col hover-stage" 
                     x-data="{ stageId: {{ $stage->id }} }" 
                     @dragover.prevent 
                     @drop="onDrop($event, stageId)"
                     :class="{ 'ring-2 ring-blue-300 bg-blue-50 transform scale-105': draggedCarId && hoveredStage === stageId }"
                     @dragenter="hoveredStage = stageId"
                     @dragleave="if ($event.target === $el) hoveredStage = null">
                    
                    <div class="flex items-center justify-between mb-6 p-2">
                        <h2 class="font-bold text-lg text-gray-800">{{ $stage->name }}</h2>
                        <div class="flex items-center gap-2">
                            <span class="bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full shadow-sm">
                                {{ $stage->cars->count() }} auto's
                            </span>
                        </div>
                    </div>
                
                <div class="flex flex-wrap gap-3 flex-1">
                    @forelse($stage->cars as $car)
                        @php
                            $completion = $car->stage_completion;
                            $canMove = $car->canMoveToNextStage();
                        @endphp
                        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-sm p-4 cursor-move border border-gray-200 hover:shadow-lg hover:border-blue-300 transition-all duration-300 w-full sm:w-64 flex-shrink-0 car-card" 
                             draggable="true" 
                             @dragstart="onDragStart($event, {{ $car->id }})"
                             @dragend="onDragEnd()"
                             :class="{ 'opacity-50 transform rotate-3': draggedCarId === {{ $car->id }} }">
                            
                            <!-- Car Header -->
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                    <i class="fa-solid fa-car text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-gray-900 text-sm truncate">
                                        {{ $car->license_plate }}
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        {{ $car->brand }} {{ $car->model }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Price -->
                            <div class="mb-3">
                                <div class="text-lg font-bold text-green-600">
                                    €{{ number_format($car->price, 0, ',', '.') }}
                                </div>
                            </div>
                            
                            <!-- Progress Section -->
                            <div class="mb-4">
                                <div class="flex justify-between text-xs font-medium text-gray-700 mb-2">
                                    <span>Checklist voortgang</span>
                                    <span class="text-blue-600">{{ $completion }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full transition-all duration-500 {{ $completion === 100 ? 'bg-gradient-to-r from-green-400 to-green-500' : 'bg-gradient-to-r from-blue-400 to-blue-500' }}" 
                                         style="width: {{ $completion }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $car->checklists->where('stage_id', $stage->id)->where('is_completed', true)->count() }} / 
                                    {{ $car->checklists->where('stage_id', $stage->id)->count() }} taken voltooid
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="flex justify-between items-center">
                                @if($canMove)
                                    <div class="inline-flex items-center px-2 py-1 text-xs font-semibold bg-gradient-to-r from-green-100 to-green-200 text-green-800 rounded-full">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                        Klaar voor volgende fase
                                    </div>
                                @else
                                    <div class="inline-flex items-center px-2 py-1 text-xs font-semibold bg-gradient-to-r from-yellow-100 to-orange-200 text-orange-800 rounded-full">
                                        <span class="w-2 h-2 bg-orange-500 rounded-full mr-1"></span>
                                        In bewerking
                                    </div>
                                @endif
                                
                                <a href="{{ route('pipeline.checklist', $car) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium hover:bg-blue-200 transition-all duration-200"
                                   @click.stop>
                                    <i class="fa-solid fa-list-check mr-1"></i>
                                    Checklist
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-400 text-center py-16 border-2 border-dashed border-gray-200 rounded-xl w-full hover:border-gray-300 transition-colors duration-300">
                            <div class="text-4xl mb-3">📋</div>
                            <div class="text-sm font-medium text-gray-600 mb-1">Geen auto's in deze fase</div>
                            <div class="text-xs text-gray-500">Sleep een auto hiernaartoe om te beginnen</div>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
/* Custom animations */
@keyframes slideInDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes slideInUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes wiggle {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(1deg); }
    75% { transform: rotate(-1deg); }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Stage column animations */
.hover-stage {
    animation: slideInDown 0.6s ease-out;
}

.hover-stage:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

/* Car card animations */
.car-card {
    animation: slideInUp 0.5s ease-out;
}

.car-card:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Drag effects */
.car-card[draggable="true"]:active {
    animation: wiggle 0.2s ease-in-out infinite;
}

/* Staggered animations */
.hover-stage:nth-child(1) { animation-delay: 0.1s; }
.hover-stage:nth-child(2) { animation-delay: 0.2s; }
.hover-stage:nth-child(3) { animation-delay: 0.3s; }
.hover-stage:nth-child(4) { animation-delay: 0.4s; }
.hover-stage:nth-child(5) { animation-delay: 0.5s; }

.car-card:nth-child(odd) { animation-delay: 0.1s; }
.car-card:nth-child(even) { animation-delay: 0.15s; }

/* Progress bar animation */
.car-card .h-2 {
    transition: width 0.8s ease-in-out;
}

/* Success/Error message styling */
.fixed.top-4.right-4 {
    animation: slideInDown 0.3s ease-out;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Drag hover effects */
.ring-2.ring-blue-300 {
    animation: pulse 1s ease-in-out infinite;
}
</style>

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
