@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-full">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('autos.index') }}" 
                       class="text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        <i class="fa-solid fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $auto->license_plate }}</h1>
                        <p class="text-gray-600">{{ $auto->brand }} {{ $auto->model }} ({{ $auto->year }})</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('autos.edit', $auto) }}" 
                       class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                        <i class="fa-solid fa-edit"></i>
                        Bewerken
                    </a>
                    @if($auto->stage && $auto->stage->name === 'Verkoop klaar')
                        <a href="{{ route('sales.create', ['car_id' => $auto->id]) }}" 
                           class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-handshake"></i>
                            Verkoop Starten
                        </a>
                        <a href="{{ route('cars.marketplace.show', $auto) }}" 
                           class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-bullhorn"></i>
                            Publiceren
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Image Gallery Section -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-images text-blue-600"></i>
                        Foto's ({{ $auto->images->count() }})
                    </h2>
                    <button onclick="openUploadModal()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        Foto's Toevoegen
                    </button>
                </div>

                @if($auto->images->count() > 0)
                    <!-- Image Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="image-gallery">
                        @foreach($auto->images->sortBy('sort_order') as $image)
                            <div class="relative group cursor-pointer" onclick="openLightbox('{{ $image->url }}', '{{ $image->alt_text }}')">
                                <img src="{{ $image->thumbnail_url }}" 
                                     alt="{{ $image->alt_text }}"
                                     class="w-full h-32 object-cover rounded-lg border border-gray-200 group-hover:shadow-lg transition-all duration-200">
                                
                                <!-- Primary Badge -->
                                @if($image->is_primary)
                                    <div class="absolute top-2 left-2 bg-green-600 text-white text-xs px-2 py-1 rounded">
                                        Hoofdfoto
                                    </div>
                                @endif

                                <!-- Category Badge -->
                                <div class="absolute top-2 right-2 bg-gray-800 bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                                    {{ ucfirst($image->category) }}
                                </div>

                                <!-- Action Buttons (shown on hover) -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <div class="flex gap-2">
                                        @if(!$image->is_primary)
                                            <button onclick="event.stopPropagation(); makePrimary({{ $image->id }})" 
                                                    class="bg-green-600 hover:bg-green-700 text-white p-2 rounded-full">
                                                <i class="fa-solid fa-star text-sm"></i>
                                            </button>
                                        @endif
                                        <button onclick="event.stopPropagation(); deleteImage({{ $image->id }})" 
                                                class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- No Images State -->
                    <div class="text-center py-12">
                        <i class="fa-solid fa-images text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-500 mb-2">Nog geen foto's</h3>
                        <p class="text-gray-400 mb-4">Voeg foto's toe om deze auto te presenteren</p>
                        <button onclick="openUploadModal()" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            Eerste foto toevoegen
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Car Details Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">🚗 Auto Details</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Kenteken</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $auto->license_plate }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Merk & Model</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $auto->brand }} {{ $auto->model }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Bouwjaar</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $auto->year }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Kilometerstand</label>
                            <p class="text-lg font-semibold text-gray-900">{{ number_format($auto->mileage) }} km</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Vraagprijs</label>
                            <p class="text-lg font-semibold text-green-600">€{{ number_format($auto->price, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Toegevoegd op</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $auto->created_at->format('d-m-Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Current Status -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">📍 Huidige Status</h2>
                    @if($auto->stage)
                        @php
                            $stageColors = [
                                'Intake' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'Technische controle' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'Herstel & Onderhoud' => 'bg-orange-100 text-orange-800 border-orange-200',
                                'Commercieel gereed' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'Verkoop klaar' => 'bg-green-100 text-green-800 border-green-200'
                            ];
                            $colorClass = $stageColors[$auto->stage->name] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                        @endphp
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $colorClass }} border">
                                {{ $auto->stage->name }}
                            </span>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Voltooiing</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $auto->stage_completion }}%</p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                                 style="width: {{ $auto->stage_completion }}%"></div>
                        </div>
                    @else
                        <p class="text-gray-500">Geen fase toegewezen</p>
                    @endif
                </div>

                <!-- Checklist Progress -->
                @if($auto->checklists->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">✅ Checklist Voortgang</h2>
                        @foreach($auto->checklists->groupBy('stage.name') as $stageName => $checklists)
                            <div class="mb-6 last:mb-0">
                                <h3 class="font-medium text-gray-900 mb-3">{{ $stageName }}</h3>
                                <div class="space-y-2">
                                    @foreach($checklists as $item)
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0">
                                                @if($item->is_completed)
                                                    <i class="fa-solid fa-check-circle text-green-500"></i>
                                                @else
                                                    <i class="fa-regular fa-circle text-gray-400"></i>
                                                @endif
                                            </div>
                                            <span class="text-sm {{ $item->is_completed ? 'text-gray-900 line-through' : 'text-gray-700' }}">
                                                {{ $item->task }}
                                            </span>
                                            @if($item->repair)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    Reparatie
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Repairs -->
                @if($auto->repairs->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">🔧 Reparaties</h2>
                        <div class="space-y-4">
                            @foreach($auto->repairs as $repair)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-medium text-gray-900">{{ $repair->description }}</h3>
                                        @php
                                            $statusColors = [
                                                'gepland' => 'bg-yellow-100 text-yellow-800',
                                                'bezig' => 'bg-blue-100 text-blue-800',
                                                'wachten_op_onderdeel' => 'bg-orange-100 text-orange-800',
                                                'gereed' => 'bg-green-100 text-green-800'
                                            ];
                                            $statusColor = $statusColors[$repair->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $repair->status)) }}
                                        </span>
                                    </div>
                                    @if($repair->cost_estimate)
                                        <p class="text-sm text-gray-600 mb-2">
                                            Geschatte kosten: <span class="font-medium">€{{ number_format($repair->cost_estimate, 2, ',', '.') }}</span>
                                        </p>
                                    @endif
                                    @if($repair->parts->count() > 0)
                                        <div class="mt-3">
                                            <p class="text-sm font-medium text-gray-700 mb-1">Onderdelen:</p>
                                            <div class="space-y-1">
                                                @foreach($repair->parts as $part)
                                                    <div class="flex items-center justify-between text-sm">
                                                        <span class="text-gray-600">{{ $part->name }}</span>
                                                        <div class="flex items-center gap-2">
                                                            @if($part->price)
                                                                <span class="text-gray-900">€{{ number_format($part->price, 2, ',', '.') }}</span>
                                                            @endif
                                                            @php
                                                                $partStatusColors = [
                                                                    'besteld' => 'bg-yellow-100 text-yellow-800',
                                                                    'geleverd' => 'bg-blue-100 text-blue-800',
                                                                    'gemonteerd' => 'bg-green-100 text-green-800'
                                                                ];
                                                                $partStatusColor = $partStatusColors[$part->status] ?? 'bg-gray-100 text-gray-800';
                                                            @endphp
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $partStatusColor }}">
                                                                {{ ucfirst($part->status) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Actions & Info -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">⚡ Snelle Acties</h2>
                    <div class="space-y-3">
                        <a href="{{ route('pipeline.checklist', $auto) }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg transition duration-200 block">
                            <i class="fa-solid fa-list-check mr-2"></i>
                            Checklist Beheren
                        </a>
                        <a href="{{ route('repairs.index', ['car_id' => $auto->id]) }}" 
                           class="w-full bg-orange-600 hover:bg-orange-700 text-white text-center py-2 px-4 rounded-lg transition duration-200 block">
                            <i class="fa-solid fa-wrench mr-2"></i>
                            Reparatie Toevoegen
                        </a>
                        <a href="{{ route('agenda.index', ['car_id' => $auto->id]) }}" 
                           class="w-full bg-purple-600 hover:bg-purple-700 text-white text-center py-2 px-4 rounded-lg transition duration-200 block">
                            <i class="fa-solid fa-calendar mr-2"></i>
                            Afspraak Plannen
                        </a>
                    </div>
                </div>

                <!-- Sales Information -->
                @if($auto->sales->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">💰 Verkoop Informatie</h2>
                        <div class="space-y-4">
                            @foreach($auto->sales as $sale)
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-gray-900">{{ $sale->customer->name }}</span>
                                        @php
                                            $saleStatusColors = [
                                                'option' => 'bg-yellow-100 text-yellow-800',
                                                'contract_signed' => 'bg-blue-100 text-blue-800',
                                                'ready_for_delivery' => 'bg-orange-100 text-orange-800',
                                                'delivered' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800'
                                            ];
                                            $saleStatusColor = $saleStatusColors[$sale->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $saleStatusColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $sale->status)) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        Verkoopprijs: <span class="font-medium">€{{ number_format($sale->sale_price, 0, ',', '.') }}</span>
                                    </p>
                                    @if($sale->delivery_date)
                                        <p class="text-sm text-gray-600">
                                            Aflevering: {{ $sale->delivery_date->format('d-m-Y') }}
                                        </p>
                                    @endif
                                    <div class="mt-2">
                                        <a href="{{ route('sales.show', $sale) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Verkoop Bekijken →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Appointments -->
                @if($auto->appointments->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">📅 Afspraken</h2>
                        <div class="space-y-3">
                            @foreach($auto->appointments->take(5) as $appointment)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $appointment->type }}</p>
                                        <p class="text-sm text-gray-600">{{ $appointment->customer_name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($appointment->date)->format('d-m') }}</p>
                                        <p class="text-sm text-gray-600">{{ $appointment->time }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Timeline -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">📈 Timeline</h2>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Auto toegevoegd</p>
                                <p class="text-xs text-gray-500">{{ $auto->created_at->format('d-m-Y H:i') }}</p>
                            </div>
                        </div>
                        @if($auto->updated_at != $auto->created_at)
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Laatst bijgewerkt</p>
                                    <p class="text-xs text-gray-500">{{ $auto->updated_at->format('d-m-Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                        @if($auto->sales->where('status', 'delivered')->first())
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Verkocht</p>
                                    <p class="text-xs text-gray-500">{{ $auto->sales->where('status', 'delivered')->first()->sold_at->format('d-m-Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" style="display: flex; align-items: center; justify-content: center;">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Foto's Uploaden</h3>
            <button onclick="closeUploadModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        
        <form id="uploadForm" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Selecteer foto's</label>
                <input type="file" id="imageInput" name="images[]" multiple accept="image/*" 
                       class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Maximaal 10 foto's, elk max 10MB</p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Categorie</label>
                <select id="categorySelect" name="category" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="exterior">Exterieur</option>
                    <option value="interior">Interieur</option>
                    <option value="engine">Motor</option>
                    <option value="damage">Schade</option>
                    <option value="documents">Documenten</option>
                    <option value="other">Overig</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Beschrijving (optioneel)</label>
                <input type="text" id="altTextInput" name="alt_text" 
                       class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Beschrijving van de foto's">
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeUploadModal()" 
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Annuleren
                </button>
                <button type="submit" id="uploadBtn"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Uploaden
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="lightboxModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden" onclick="closeLightbox()">
    <div class="relative max-w-4xl max-h-full w-full h-full flex items-center justify-center p-4" onclick="event.stopPropagation()">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center">
            <i class="fa-solid fa-times text-xl"></i>
        </button>
        <img id="lightboxImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
    </div>
</div>

<script>
// Modal functions
function openUploadModal() {
    document.getElementById('uploadModal').style.display = 'flex';
    document.getElementById('uploadModal').classList.remove('hidden');
}

function closeUploadModal() {
    document.getElementById('uploadModal').style.display = 'none';
    document.getElementById('uploadModal').classList.add('hidden');
    document.getElementById('uploadForm').reset();
}

function openLightbox(imageUrl, altText) {
    console.log('Opening lightbox with URL:', imageUrl);
    const modal = document.getElementById('lightboxModal');
    const image = document.getElementById('lightboxImage');
    
    image.src = imageUrl;
    image.alt = altText || '';
    
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const modal = document.getElementById('lightboxModal');
    modal.style.display = 'none';
    modal.classList.add('hidden');
    
    // Restore body scroll
    document.body.style.overflow = '';
}

// Upload handling
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const uploadBtn = document.getElementById('uploadBtn');
    const originalText = uploadBtn.innerHTML;
    
    uploadBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Uploaden...';
    uploadBtn.disabled = true;
    
    fetch(`/cars/{{ $auto->id }}/images`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Refresh to show new images
        } else {
            alert('Er is een fout opgetreden bij het uploaden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Er is een fout opgetreden bij het uploaden');
    })
    .finally(() => {
        uploadBtn.innerHTML = originalText;
        uploadBtn.disabled = false;
    });
});

// Image actions
function makePrimary(imageId) {
    if (confirm('Weet je zeker dat je deze foto als hoofdfoto wilt instellen?')) {
        fetch(`/images/${imageId}/make-primary`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Er is een fout opgetreden');
            }
        });
    }
}

function deleteImage(imageId) {
    if (confirm('Weet je zeker dat je deze foto wilt verwijderen?')) {
        fetch(`/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Er is een fout opgetreden');
            }
        });
    }
}

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeUploadModal();
        closeLightbox();
    }
});
</script>
@endsection
