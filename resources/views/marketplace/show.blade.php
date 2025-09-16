@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* Enhanced Marketplace Styling */
.platform-tab {
    transition: all 0.2s ease;
    position: relative;
}

.platform-tab:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.image-selector {
    cursor: pointer;
    transition: all 0.2s ease;
}

.image-selector:hover {
    transform: scale(1.02);
}

.live-preview {
    transition: all 0.3s ease;
}

.preview-card {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.preview-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.toast-notification {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.loading-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Enhanced card styling */
.card-enhanced {
    border: 1px solid rgba(229, 231, 235, 0.8);
    backdrop-filter: blur(5px);
    transition: all 0.2s ease;
}

.card-enhanced:hover {
    border-color: rgba(156, 163, 175, 0.5);
    background-color: rgba(255, 255, 255, 0.95);
}
</style>

<div class="bg-[var(--background-main)] min-h-full">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('autos.show', $car) }}" 
                       class="text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors duration-200">
                        <i class="fa-solid fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-[var(--text-primary)] flex items-center gap-2">
                            <i class="fa-solid fa-bullhorn text-[var(--status-info)]"></i>
                            Marketplace Publishing
                        </h1>
                        <p class="text-[var(--text-secondary)]">{{ $car->license_plate }} - {{ $car->brand }} {{ $car->model }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($car->stage && $car->stage->name === 'Verkoop klaar')
                        <span class="bg-[var(--status-success-light)] text-[var(--status-success)] px-3 py-1 rounded-full text-sm font-medium">
                            <i class="fa-solid fa-check-circle mr-1"></i>
                            Klaar voor verkoop
                        </span>
                    @else
                        <span class="bg-[var(--status-warning-light)] text-[var(--status-warning)] px-3 py-1 rounded-full text-sm font-medium">
                            <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                            Nog niet verkoop klaar
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Car Info & Images -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Car Summary -->
                <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-car text-[var(--status-info)]"></i>
                        Auto Overzicht
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="text-sm font-medium text-[var(--text-secondary)]">Merk & Model</label>
                            <p class="text-[var(--text-primary)] font-semibold">{{ $car->brand }} {{ $car->model }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-[var(--text-secondary)]">Jaar</label>
                            <p class="text-[var(--text-primary)] font-semibold">{{ $car->year }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-[var(--text-secondary)]">Kilometerstand</label>
                            <p class="text-[var(--text-primary)] font-semibold">{{ number_format($car->mileage) }} km</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-[var(--text-secondary)]">Prijs</label>
                            <p class="text-[var(--text-primary)] font-semibold">€{{ number_format($car->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Available Images -->
                <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-images text-[var(--status-info)]"></i>
                        Beschikbare Foto's ({{ $car->images->count() }})
                    </h2>
                    
                    @if($car->images->count() > 0)
                        <div class="grid grid-cols-3 md:grid-cols-4 gap-3" id="image-selector">
                            @foreach($car->images->sortBy('sort_order') as $image)
                                <div class="relative cursor-pointer group image-selector" data-image-id="{{ $image->id }}">
                                    <img src="{{ $image->thumbnail_url }}" 
                                         alt="{{ $image->alt_text }}"
                                         class="w-full h-20 object-cover rounded-lg border-2 border-transparent transition-all duration-200"
                                         id="image-{{ $image->id }}">
                                    
                                    <!-- Selection Overlay -->
                                    <div class="absolute inset-0 bg-[var(--status-info)]/0 rounded-lg flex items-center justify-center transition-all duration-200" 
                                         id="overlay-{{ $image->id }}">
                                        <i class="fa-solid fa-check text-white text-xl opacity-0 transition-opacity duration-200" 
                                           id="check-{{ $image->id }}"></i>
                                    </div>
                                    
                                    @if($image->is_primary)
                                        <div class="absolute top-1 left-1 bg-[var(--status-success)] text-white text-xs px-1 py-0.5 rounded">
                                            Hoofd
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <p class="text-sm text-[var(--text-secondary)] mt-3">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            Klik op foto's om ze te selecteren voor de advertentie
                        </p>
                    @else
                        <div class="text-center py-8">
                            <i class="fa-solid fa-images text-[var(--text-tertiary)] text-4xl mb-3"></i>
                            <p class="text-[var(--text-secondary)] mb-4">Geen foto's beschikbaar</p>
                            <a href="{{ route('autos.show', $car) }}" 
                               class="bg-[var(--status-info)] text-white px-4 py-2 rounded-lg hover:bg-[var(--status-info-dark)] transition-colors">
                                Foto's Toevoegen
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Active Listings -->
                @if($car->listings->count() > 0)
                    <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6">
                        <h2 class="text-xl font-semibold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-list text-[var(--status-info)]"></i>
                            Actieve Advertenties
                        </h2>
                        
                        <div class="space-y-3">
                            @foreach($car->listings as $listing)
                                <div class="flex items-center justify-between p-4 bg-[var(--background-main)] rounded-lg border border-[var(--border-light)]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-[var(--status-info-light)] flex items-center justify-center">
                                            <i class="fa-solid fa-{{ $listing->platform === 'marktplaats' ? 'shopping-cart' : ($listing->platform === 'instagram' ? 'camera' : 'share-alt') }} text-[var(--status-info)] text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-[var(--text-primary)]">{{ ucfirst($listing->platform) }}</h4>
                                            <p class="text-sm text-[var(--text-secondary)]">
                                                Status: 
                                                @php
                                                    $statusColors = [
                                                        'draft' => 'text-[var(--text-tertiary)]',
                                                        'pending' => 'text-[var(--status-warning)]',
                                                        'published' => 'text-[var(--status-success)]',
                                                        'error' => 'text-[var(--status-danger)]'
                                                    ];
                                                @endphp
                                                <span class="{{ $statusColors[$listing->status] ?? 'text-[var(--text-secondary)]' }}">
                                                    {{ ucfirst($listing->status) }}
                                                </span>
                                                @if($listing->published_at)
                                                    - {{ $listing->published_at->format('d-m-Y') }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($listing->listing_url)
                                            <a href="{{ $listing->listing_url }}" target="_blank"
                                               class="text-[var(--status-info)] hover:text-[var(--status-info-dark)] transition-colors">
                                                <i class="fa-solid fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                        <button data-action="deleteListing" data-listing-id="{{ $listing->id }}"
                                                class="text-[var(--status-danger)] hover:text-[var(--status-danger-dark)] transition-colors">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Platform Previews -->
            <div class="space-y-6">
                <!-- Platform Tabs -->
                <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-rocket text-[var(--status-special)]"></i>
                        Live Preview & Publish
                    </h2>
                    
                    <!-- Platform Selector -->
                    <div class="flex border-b border-gray-200 mb-6">
                        <button data-platform="marktplaats" 
                                class="platform-tab flex-1 py-3 px-4 text-center font-medium border-b-2 transition-colors duration-200 border-orange-500 text-orange-600"
                                id="tab-marktplaats">
                            <i class="fa-solid fa-shopping-cart mr-2"></i>
                            Marktplaats
                        </button>
                        <button data-platform="instagram" 
                                class="platform-tab flex-1 py-3 px-4 text-center font-medium border-b-2 transition-colors duration-200 border-transparent text-gray-500 hover:text-gray-700"
                                id="tab-instagram">
                            <i class="fa-brands fa-instagram mr-2"></i>
                            Instagram
                        </button>
                        <button data-platform="facebook" 
                                class="platform-tab flex-1 py-3 px-4 text-center font-medium border-b-2 transition-colors duration-200 border-transparent text-gray-500 hover:text-gray-700"
                                id="tab-facebook">
                            <i class="fa-brands fa-facebook mr-2"></i>
                            Facebook
                        </button>
                    </div>
                    
                    <!-- Live Preview Container -->
                    <div id="livePreviewContainer" class="mb-6">
                        <!-- Preview will be injected here -->
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex gap-3">
                        <button data-action="openEditModal" 
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-edit"></i>
                            Bewerken
                        </button>
                        <button data-action="publishCurrentPlatform" 
                                class="flex-1 font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2"
                                id="publishButton"
                                {{ $car->images->count() === 0 ? 'disabled' : '' }}>
                            <i class="fa-solid fa-bullhorn mr-2"></i>
                            Publiceren
                        </button>
                    </div>
                    
                    @if($car->images->count() === 0)
                        <p class="text-sm text-[var(--status-warning)] mt-3 text-center">
                            <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                            Voeg eerst foto's toe om te kunnen publiceren
                        </p>
                    @endif
                </div>

                <!-- Quick Stats -->
                <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6">
                    <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-chart-bar text-[var(--status-info)]"></i>
                        Advertentie Stats
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-[var(--text-secondary)]">Geselecteerde foto's</span>
                            <span class="font-semibold text-[var(--text-primary)]" id="selectedCount">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[var(--text-secondary)]">Geschatte bereik</span>
                            <span class="font-semibold text-[var(--status-success)]" id="estimatedReach">~2.5k views</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[var(--text-secondary)]">Platform optimalisatie</span>
                            <span class="font-semibold text-[var(--status-success)]" id="optimizationScore">95%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" style="display: flex; align-items: center; justify-content: center;">
    <div class="bg-white rounded-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-edit text-blue-600"></i>
                <span id="editPlatformTitle">Bewerk Advertentie</span>
            </h3>
            <button class="text-gray-500 hover:text-gray-700" data-action="closeEditModal">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
            <!-- Left: Edit Form -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Titel
                        <button type="button" data-action="clearTitleField" 
                                class="ml-2 text-xs text-blue-600 hover:text-blue-800 underline">
                            Wis (gebruik automatisch)
                        </button>
                    </label>
                    <input type="text" id="editTitle" 
                           class="w-full border border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Typ je eigen titel hier... (laat leeg voor automatisch)">
                    <p class="text-xs text-gray-500 mt-1">Live preview toont automatische titel totdat je eigen tekst typt</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Beschrijving
                        <button type="button" data-action="clearDescriptionField" 
                                class="ml-2 text-xs text-blue-600 hover:text-blue-800 underline">
                            Wis (gebruik automatisch)
                        </button>
                    </label>
                    <textarea id="editDescription" rows="8"
                              class="w-full border border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Typ je eigen beschrijving hier... (laat leeg voor automatisch)"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Live preview toont automatische beschrijving totdat je eigen tekst typt</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Platform-specifieke opties</label>
                    <div id="platformSpecificOptions" class="space-y-3">
                        <!-- Dynamic options will be injected here -->
                    </div>
                </div>
            </div>
            
            <!-- Right: Live Preview -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-700 mb-3">Live Preview</h4>
                <div id="editPreviewContainer" class="bg-white rounded-lg border border-gray-200 p-4">
                    <!-- Preview will be updated here -->
                </div>
            </div>
        </div>
        
        <div class="flex justify-between gap-3 p-6 border-t border-gray-200">
            <button data-action="resetToDefaults" 
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200">
                <i class="fa-solid fa-undo mr-2"></i>
                Reset naar standaard
            </button>
            <div class="flex gap-3">
                <button data-action="closeEditModal" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Annuleren
                </button>
                <button data-action="saveAndPublish" 
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fa-solid fa-bullhorn mr-2"></i>
                    Opslaan & Publiceren
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" style="display: flex; align-items: center; justify-content: center;">
    <div class="bg-white rounded-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-eye text-blue-600"></i>
                <span id="previewPlatformTitle">Preview</span>
            </h3>
            <button data-action="closePreviewModal" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
            <!-- Platform-specific preview content will be injected here -->
            <div id="previewContent">
                <div class="text-center py-12">
                    <i class="fa-solid fa-spinner fa-spin text-gray-400 text-3xl mb-4"></i>
                    <p class="text-gray-500">Genereren van preview...</p>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
            <button data-action="closePreviewModal" 
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200">
                Sluiten
            </button>
            <button data-action="publishFromPreview" 
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                <i class="fa-solid fa-bullhorn mr-2"></i>
                Publiceren
            </button>
        </div>
    </div>
</div>

<script>
// New simplified marketplace system
let selectedImages = [];
let currentPlatform = 'marktplaats';
let carData = @json($car);
let platformPresets = {
    marktplaats: {
        color: 'orange',
        maxImages: 10,
        titleTemplate: '{brand} {model} ({year}) - {price}',
        features: ['Handelaarprijs', 'Garantie mogelijk', 'Inruil mogelijk']
    },
    instagram: {
        color: 'purple',
        maxImages: 10,
        titleTemplate: '🚗 {brand} {model} | {year} | €{price}',
        features: ['Stories', 'Hashtags', 'Location tagging']
    },
    facebook: {
        color: 'blue',
        maxImages: 10,
        titleTemplate: '{brand} {model} - {year} | {mileage}km',
        features: ['Marketplace', 'Local audience', 'Messenger contact']
    }
};

// Debug helper
function debugLog(message, data = null) {
    console.log(`[Marketplace Debug] ${message}`, data || '');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    debugLog('Marketplace system initializing...');
    
    // Auto-select primary images
    autoSelectImages();
    
    // Set initial platform state
    switchPlatform('marktplaats');
    
    // Update UI counters
    updateImageCounter();
    
    // Setup global event listeners
    setupGlobalEventListeners();
    
    debugLog('Marketplace system initialized with images:', selectedImages.length);
});

// Setup global event listeners
function setupGlobalEventListeners() {
    debugLog('Setting up global event listeners...');
    
    // Use event delegation for all buttons with data-action attributes
    document.addEventListener('click', function(e) {
        const action = e.target.closest('[data-action]')?.getAttribute('data-action');
        if (!action) return;
        
        debugLog('Button clicked with action:', action);
        
        switch(action) {
            case 'openEditModal':
                e.preventDefault();
                openEditModal();
                break;
            case 'closeEditModal':
                e.preventDefault();
                closeEditModal();
                break;
            case 'publishCurrentPlatform':
                e.preventDefault();
                publishCurrentPlatform();
                break;
            case 'saveAndPublish':
                e.preventDefault();
                saveAndPublish();
                break;
            case 'resetToDefaults':
                e.preventDefault();
                resetToDefaults();
                break;
            case 'clearTitleField':
                e.preventDefault();
                clearTitleField();
                break;
            case 'clearDescriptionField':
                e.preventDefault();
                clearDescriptionField();
                break;
            case 'closePreviewModal':
                e.preventDefault();
                closePreviewModal();
                break;
            case 'publishFromPreview':
                e.preventDefault();
                publishFromPreview();
                break;
            case 'deleteListing':
                e.preventDefault();
                const listingId = e.target.closest('[data-listing-id]')?.getAttribute('data-listing-id');
                if (listingId) deleteListing(parseInt(listingId));
                break;
        }
    });
    
    // Platform switching
    document.addEventListener('click', function(e) {
        const platform = e.target.closest('[data-platform]')?.getAttribute('data-platform');
        if (platform) {
            e.preventDefault();
            debugLog('Platform switch to:', platform);
            switchPlatform(platform);
        }
    });
    
    // Image selection
    document.addEventListener('click', function(e) {
        const imageId = e.target.closest('[data-image-id]')?.getAttribute('data-image-id');
        if (imageId) {
            e.preventDefault();
            debugLog('Image clicked:', imageId);
            toggleImageSelection(parseInt(imageId));
        }
    });
    
    debugLog('Event listeners setup complete');
}

// Auto-select best images
function autoSelectImages() {
    const images = @json($car->images->sortBy('sort_order')->take(5)->pluck('id'));
    selectedImages = [...images]; // Create a copy
    
    console.log('Auto-selecting images:', selectedImages);
    
    // Update UI for selected images
    selectedImages.forEach(imageId => {
        const image = document.getElementById(`image-${imageId}`);
        const overlay = document.getElementById(`overlay-${imageId}`);
        const check = document.getElementById(`check-${imageId}`);

        if (image) {
            image.classList.add('border-blue-500');
            overlay.classList.remove('bg-[var(--status-info)]/0');
            overlay.classList.add('bg-[var(--status-info)]/30');
            check.classList.add('opacity-100');
        }
    });
}

// Image selection (enhanced)
function toggleImageSelection(imageId) {
    console.log('Toggling image selection for:', imageId);
    
    const image = document.getElementById(`image-${imageId}`);
    const overlay = document.getElementById(`overlay-${imageId}`);
    const check = document.getElementById(`check-${imageId}`);
    const maxImages = platformPresets[currentPlatform].maxImages;

    if (!image || !overlay || !check) {
        console.error('Image elements not found for ID:', imageId);
        return;
    }

    if (selectedImages.includes(imageId)) {
        // Deselect
        selectedImages = selectedImages.filter(id => id !== imageId);
        image.classList.remove('border-blue-500');
        overlay.classList.remove('bg-[var(--status-info)]/30');
        overlay.classList.add('bg-[var(--status-info)]/0');
        check.classList.remove('opacity-100');
        console.log('Deselected image:', imageId);
    } else {
        // Select (max per platform)
        if (selectedImages.length < maxImages) {
            selectedImages.push(imageId);
            image.classList.add('border-blue-500');
            overlay.classList.remove('bg-[var(--status-info)]/0');
            overlay.classList.add('bg-[var(--status-info)]/30');
            check.classList.add('opacity-100');
            console.log('Selected image:', imageId);
        } else {
            showToast(`Maximaal ${maxImages} foto's voor ${currentPlatform}`, 'warning');
            return;
        }
    }

    updateImageCounter();
    generateLivePreview();
}

// Platform switching
function switchPlatform(platform) {
    currentPlatform = platform;
    
    // Update tabs - clear all active states first
    document.querySelectorAll('.platform-tab').forEach(tab => {
        tab.classList.remove('border-orange-500', 'text-orange-600', 'border-purple-500', 'text-purple-600', 'border-blue-500', 'text-blue-600');
        tab.classList.add('border-transparent', 'text-gray-500');
        tab.classList.remove('border-b-2');
        tab.style.borderBottomWidth = '2px';
        tab.style.borderBottomColor = 'transparent';
    });
    
    // Set active tab with specific colors per platform
    const activeTab = document.getElementById(`tab-${platform}`);
    if (activeTab) {
        activeTab.classList.remove('border-transparent', 'text-gray-500');
        
        if (platform === 'marktplaats') {
            activeTab.style.borderBottomColor = '#ff6100';
            activeTab.style.color = '#ff6100';
        } else if (platform === 'instagram') {
            activeTab.style.borderBottomColor = '#e4405f';
            activeTab.style.color = '#e4405f';
        } else if (platform === 'facebook') {
            activeTab.style.borderBottomColor = '#1877f2';
            activeTab.style.color = '#1877f2';
        }
    }
    
    // Update publish button color
    updatePlatformButtons();
    
    // Regenerate preview
    generateLivePreview();
    
    // Check image limits
    const preset = platformPresets[platform];
    if (selectedImages.length > preset.maxImages) {
        selectedImages = selectedImages.slice(0, preset.maxImages);
        updateImageSelectionUI();
        showToast(`Aantal foto's beperkt tot ${preset.maxImages} voor ${platform}`, 'info');
    }
    
    updateImageCounter();
}

// Generate smart content
function generateContent(platform) {
    const preset = platformPresets[platform];
    
    // Smart title generation
    let title = preset.titleTemplate
        .replace('{brand}', carData.brand)
        .replace('{model}', carData.model)
        .replace('{year}', carData.year)
        .replace('{price}', new Intl.NumberFormat('nl-NL').format(carData.price))
        .replace('{mileage}', new Intl.NumberFormat('nl-NL').format(carData.mileage));
    
    // Smart description generation
    let description = generateSmartDescription(platform);
    
    return { title, description };
}

function generateSmartDescription(platform) {
    let desc = '';
    
    if (platform === 'marktplaats') {
        desc = `Te koop: ${carData.brand} ${carData.model}\n\n`;
        desc += `🚗 Bouwjaar: ${carData.year}\n`;
        desc += `📊 Kilometerstand: ${new Intl.NumberFormat('nl-NL').format(carData.mileage)} km\n`;
        desc += `💰 Prijs: €${new Intl.NumberFormat('nl-NL').format(carData.price)}\n\n`;
        desc += `✅ Dealer occasie\n✅ Garantie mogelijk\n✅ Inruil welkom\n\n`;
        desc += `Interesse? Neem contact op voor meer informatie of een proefrit!`;
    } else if (platform === 'instagram') {
        desc = `🚗 ${carData.brand} ${carData.model} (${carData.year})\n\n`;
        desc += `📍 Nu beschikbaar bij ons!\n`;
        desc += `🔥 ${new Intl.NumberFormat('nl-NL').format(carData.mileage)}km | €${new Intl.NumberFormat('nl-NL').format(carData.price)}\n\n`;
        desc += `#${carData.brand.toLowerCase()} #${carData.model.toLowerCase().replace(' ', '')} #auto #occasions #dealer #${carData.year}`;
    } else if (platform === 'facebook') {
        desc = `${carData.brand} ${carData.model} te koop!\n\n`;
        desc += `Bouwjaar: ${carData.year}\n`;
        desc += `Kilometerstand: ${new Intl.NumberFormat('nl-NL').format(carData.mileage)} km\n`;
        desc += `Dealer prijs: €${new Intl.NumberFormat('nl-NL').format(carData.price)}\n\n`;
        desc += `Betrouwbare dealer met garantie en service.\nBericht ons voor meer info!`;
    }
    
    return desc;
}

// Live preview generation
function generateLivePreview() {
    const container = document.getElementById('livePreviewContainer');
    if (!container) {
        console.error('Live preview container not found');
        return;
    }
    
    console.log('Generating preview for platform:', currentPlatform, 'with images:', selectedImages);
    
    const content = generateContent(currentPlatform);
    const images = selectedImages.slice(0, platformPresets[currentPlatform].maxImages);
    
    // Get image URLs
    const imageElements = images.map(id => {
        const img = document.getElementById(`image-${id}`);
        return img ? {
            url: img.src,
            alt: img.alt || 'Car image'
        } : null;
    }).filter(Boolean);
    
    console.log('Using images for preview:', imageElements.length);
    
    let previewHtml = '';
    
    if (currentPlatform === 'marktplaats') {
        previewHtml = generateMarktplaatsPreview(content, imageElements);
    } else if (currentPlatform === 'instagram') {
        previewHtml = generateInstagramPreview(content, imageElements);
    } else if (currentPlatform === 'facebook') {
        previewHtml = generateFacebookPreview(content, imageElements);
    }
    
    if (previewHtml) {
        container.innerHTML = previewHtml;
    } else {
        container.innerHTML = '<div class="text-center py-8 text-gray-500">Geen preview beschikbaar</div>';
    }
}

// Platform-specific preview generators (simplified)
function generateMarktplaatsPreview(content, images) {
    const imageHtml = images.length > 0 ? `
        <div class="relative">
            <img src="${images[0].url}" class="w-full h-48 object-cover" alt="Auto foto">
            ${images.length > 1 ? `<div class="absolute bottom-2 right-2 bg-black bg-opacity-60 text-white px-2 py-1 rounded text-xs">1/${images.length}</div>` : ''}
        </div>
    ` : '<div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">Geen foto geselecteerd</div>';

    return `
        <div class="bg-white border border-orange-200 rounded-lg overflow-hidden shadow-sm">
            <div class="bg-orange-500 p-3 text-white text-sm font-medium">
                <i class="fa-solid fa-shopping-cart mr-2"></i>Marktplaats Preview
            </div>
            ${imageHtml}
            <div class="p-4">
                <h3 class="font-bold text-lg mb-2">${content.title}</h3>
                <div class="text-2xl font-bold text-orange-600 mb-3">€${new Intl.NumberFormat('nl-NL').format(carData.price)}</div>
                <p class="text-gray-700 text-sm whitespace-pre-line">${content.description.substring(0, 150)}${content.description.length > 150 ? '...' : ''}</p>
            </div>
        </div>
    `;
}

function generateInstagramPreview(content, images) {
    const imageHtml = images.length > 0 ? `
        <div class="relative">
            <img src="${images[0].url}" class="w-full h-64 object-cover" alt="Auto foto">
            ${images.length > 1 ? `<div class="absolute top-2 right-2 bg-black bg-opacity-60 text-white px-2 py-1 rounded text-xs">1/${images.length}</div>` : ''}
        </div>
    ` : '<div class="w-full h-64 bg-gray-200 flex items-center justify-center text-gray-500">Geen foto geselecteerd</div>';

    return `
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm max-w-xs mx-auto">
            <div class="flex items-center p-3 border-b">
                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fa-solid fa-car text-white text-xs"></i>
                </div>
                <span class="font-semibold text-sm">jouw_autobedrijf</span>
            </div>
            ${imageHtml}
            <div class="p-3">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex gap-4">
                        <i class="fa-regular fa-heart text-xl"></i>
                        <i class="fa-regular fa-comment text-xl"></i>
                        <i class="fa-regular fa-paper-plane text-xl"></i>
                    </div>
                    <i class="fa-regular fa-bookmark text-xl"></i>
                </div>
                <p class="text-sm"><span class="font-semibold">jouw_autobedrijf</span> ${content.description.substring(0, 100)}${content.description.length > 100 ? '...' : ''}</p>
            </div>
        </div>
    `;
}

function generateFacebookPreview(content, images) {
    const imageHtml = images.length > 0 ? `
        <div class="relative">
            <img src="${images[0].url}" class="w-full h-48 object-cover" alt="Auto foto">
            ${images.length > 1 ? `<div class="absolute bottom-2 right-2 bg-black bg-opacity-60 text-white px-2 py-1 rounded text-xs">1/${images.length}</div>` : ''}
        </div>
    ` : '<div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">Geen foto geselecteerd</div>';

    return `
        <div class="bg-white border border-blue-200 rounded-lg overflow-hidden shadow-sm">
            <div class="bg-blue-600 p-3 text-white text-sm font-medium">
                <i class="fa-brands fa-facebook mr-2"></i>Facebook Marketplace Preview
            </div>
            ${imageHtml}
            <div class="p-4">
                <h3 class="font-bold text-lg mb-2">${content.title}</h3>
                <div class="text-2xl font-bold text-blue-600 mb-3">€${new Intl.NumberFormat('nl-NL').format(carData.price)}</div>
                <p class="text-gray-700 text-sm whitespace-pre-line">${content.description.substring(0, 150)}${content.description.length > 150 ? '...' : ''}</p>
            </div>
        </div>
    `;
}

// UI Update functions
function updateImageCounter() {
    const counter = document.getElementById('selectedCount');
    if (counter) {
        counter.textContent = selectedImages.length;
    }
}

function updatePlatformButtons() {
    const publishButton = document.getElementById('publishButton');
    if (!publishButton) return;
    
    // Remove all possible background colors
    publishButton.classList.remove('bg-orange-600', 'hover:bg-orange-700', 'bg-purple-600', 'hover:bg-purple-700', 'bg-blue-600', 'hover:bg-blue-700');
    
    // Add platform-specific colors
    if (currentPlatform === 'marktplaats') {
        publishButton.style.backgroundColor = '#ff6100';
        publishButton.onmouseover = function() { this.style.backgroundColor = '#cc4d00'; };
        publishButton.onmouseout = function() { this.style.backgroundColor = '#ff6100'; };
    } else if (currentPlatform === 'instagram') {
        publishButton.style.backgroundColor = '#e4405f';
        publishButton.onmouseover = function() { this.style.backgroundColor = '#b6334c'; };
        publishButton.onmouseout = function() { this.style.backgroundColor = '#e4405f'; };
    } else if (currentPlatform === 'facebook') {
        publishButton.style.backgroundColor = '#1877f2';
        publishButton.onmouseover = function() { this.style.backgroundColor = '#1565c0'; };
        publishButton.onmouseout = function() { this.style.backgroundColor = '#1877f2'; };
    }
    
    // Ensure button has text color and other necessary classes
    publishButton.classList.add('text-white');
}

function updateImageSelectionUI() {
    // Reset all images
    document.querySelectorAll('[id^="image-"]').forEach(img => {
        const id = img.id.replace('image-', '');
        const overlay = document.getElementById(`overlay-${id}`);
        const check = document.getElementById(`check-${id}`);
        
        img.classList.remove('border-blue-500');
        overlay.classList.remove('bg-[var(--status-info)]/30');
        overlay.classList.add('bg-[var(--status-info)]/0');
        check.classList.remove('opacity-100');
    });
    
    // Apply selected state
    selectedImages.forEach(imageId => {
        const image = document.getElementById(`image-${imageId}`);
        const overlay = document.getElementById(`overlay-${imageId}`);
        const check = document.getElementById(`check-${imageId}`);

        if (image) {
            image.classList.add('border-blue-500');
            overlay.classList.remove('bg-[var(--status-info)]/0');
            overlay.classList.add('bg-[var(--status-info)]/30');
            check.classList.add('opacity-100');
        }
    });
}

// Publishing functions
function publishCurrentPlatform() {
    debugLog('Publishing to platform:', currentPlatform);
    debugLog('Selected images:', selectedImages);
    
    if (selectedImages.length === 0) {
        debugLog('ERROR: No images selected');
        showToast('Selecteer eerst foto\'s om te publiceren', 'error');
        return;
    }
    
    const content = generateContent(currentPlatform);
    debugLog('Generated content:', content);
    
    publishListing(currentPlatform, content);
}

function publishListing(platform, content) {
    debugLog('Starting publishListing function');
    
    // Check for CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        debugLog('ERROR: CSRF token not found');
        showToast('CSRF token niet gevonden. Herlaad de pagina.', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('_token', csrfToken.getAttribute('content'));
    formData.append('platform', platform);
    formData.append('title', content.title);
    formData.append('description', content.description);
    
    selectedImages.forEach(id => formData.append('image_ids[]', id));
    
    debugLog('FormData prepared, sending request...');
    
    // Show loading state
    const publishButton = document.getElementById('publishButton');
    const originalText = publishButton.innerHTML;
    publishButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Publiceren...';
    publishButton.disabled = true;
    
    fetch(`/cars/${carData.id}/listings`, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        debugLog('Received response:', response.status);
        return response.json();
    })
    .then(data => {
        debugLog('Response data:', data);
        if (data.success) {
            // Immediately publish the created listing
            return fetch(`/listings/${data.listing_id}/publish`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
        } else {
            throw new Error(data.message || 'Unknown error');
        }
    })
    .then(response => {
        debugLog('Publish response:', response.status);
        return response.json();
    })
    .then(data => {
        debugLog('Publish data:', data);
        if (data.success) {
            showToast(`Advertentie succesvol gepubliceerd op ${currentPlatform}!`, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            throw new Error(data.message || 'Publish failed');
        }
    })
    .catch(error => {
        debugLog('ERROR in publishListing:', error);
        showToast('Er is een fout opgetreden: ' + error.message, 'error');
    })
    .finally(() => {
        publishButton.innerHTML = originalText;
        publishButton.disabled = false;
        debugLog('publishListing completed');
    });
}

// Edit Modal functions
function openEditModal() {
    debugLog('Opening edit modal for platform:', currentPlatform);
    
    const modal = document.getElementById('editModal');
    if (!modal) {
        debugLog('ERROR: Edit modal not found');
        showToast('Modal niet gevonden', 'error');
        return;
    }
    
    const titleInput = document.getElementById('editTitle');
    const descInput = document.getElementById('editDescription');
    const titleElement = document.getElementById('editPlatformTitle');
    
    if (!titleInput || !descInput) {
        debugLog('ERROR: Input fields not found');
        showToast('Input velden niet gevonden', 'error');
        return;
    }
    
    // Clear any existing content first
    titleInput.value = '';
    descInput.value = '';
    
    // Only pre-fill if fields are empty (preserve user input)
    const content = generateContent(currentPlatform);
    
    // Don't auto-fill, let user type their own content
    debugLog('Modal opened with empty fields - user can type custom content');
    
    if (titleElement) {
        titleElement.textContent = `Bewerk ${currentPlatform} Advertentie`;
    }
    
    // Generate platform-specific options
    generatePlatformSpecificOptions();
    
    // Setup listeners for real-time updates FIRST
    setupEditModalListeners();
    
    // Generate initial preview (will show auto content since fields are empty)
    updateEditPreview();
    
    modal.style.display = 'flex';
    modal.classList.remove('hidden');
    
    // Focus on title input so user can start typing immediately
    setTimeout(() => {
        titleInput.focus();
    }, 100);
    
    debugLog('Edit modal opened successfully with real-time preview');
}

function closeEditModal() {
    debugLog('Closing edit modal');
    const modal = document.getElementById('editModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        debugLog('Edit modal closed');
    } else {
        debugLog('ERROR: Edit modal not found for closing');
    }
}

function generatePlatformSpecificOptions() {
    const container = document.getElementById('platformSpecificOptions');
    const preset = platformPresets[currentPlatform];
    
    let html = '';
    
    if (currentPlatform === 'marktplaats') {
        html = `
            <div class="bg-orange-50 p-3 rounded border">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fa-solid fa-shopping-cart text-orange-600"></i>
                    <span class="font-medium text-orange-800">Marktplaats opties</span>
                </div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" checked class="rounded">
                    <span class="text-sm">Handelaarprijs vermelden</span>
                </label>
            </div>
        `;
    } else if (currentPlatform === 'instagram') {
        html = `
            <div class="bg-purple-50 p-3 rounded border">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fa-brands fa-instagram text-purple-600"></i>
                    <span class="font-medium text-purple-800">Instagram opties</span>
                </div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" checked class="rounded">
                    <span class="text-sm">Hashtags toevoegen</span>
                </label>
            </div>
        `;
    } else if (currentPlatform === 'facebook') {
        html = `
            <div class="bg-blue-50 p-3 rounded border">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fa-brands fa-facebook text-blue-600"></i>
                    <span class="font-medium text-blue-800">Facebook opties</span>
                </div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" checked class="rounded">
                    <span class="text-sm">Contactgegevens toevoegen</span>
                </label>
            </div>
        `;
    }
    
    container.innerHTML = html;
}

function updateEditPreview() {
    debugLog('Updating edit preview...');
    
    const container = document.getElementById('editPreviewContainer');
    if (!container) {
        debugLog('ERROR: Preview container not found');
        return;
    }
    
    // Get current values from form - preserve user input!
    const titleInput = document.getElementById('editTitle');
    const descInput = document.getElementById('editDescription');
    
    if (!titleInput || !descInput) {
        debugLog('ERROR: Input fields not found');
        return;
    }
    
    // Get generated content as fallback
    const generatedContent = generateContent(currentPlatform);
    
    // Use user input if available, otherwise use generated content
    const userTitle = titleInput.value.trim();
    const userDescription = descInput.value.trim();
    
    const title = userTitle !== '' ? userTitle : generatedContent.title;
    const description = userDescription !== '' ? userDescription : generatedContent.description;
    
    debugLog('Preview content:', {
        userTitle: userTitle,
        userDescription: userDescription.substring(0, 50) + '...',
        finalTitle: title,
        finalDescription: description.substring(0, 50) + '...'
    });
    
    const content = { title, description };
    const images = selectedImages.slice(0, platformPresets[currentPlatform].maxImages);
    
    // Get image URLs
    const imageElements = images.map(id => {
        const img = document.getElementById(`image-${id}`);
        return img ? { url: img.src, alt: img.alt } : null;
    }).filter(Boolean);
    
    let previewHtml = '';
    
    if (currentPlatform === 'marktplaats') {
        previewHtml = generateMarktplaatsPreview(content, imageElements);
    } else if (currentPlatform === 'instagram') {
        previewHtml = generateInstagramPreview(content, imageElements);
    } else if (currentPlatform === 'facebook') {
        previewHtml = generateFacebookPreview(content, imageElements);
    }
    
    if (previewHtml) {
        container.innerHTML = previewHtml;
        debugLog('Preview updated successfully');
    } else {
        container.innerHTML = '<div class="text-center py-8 text-gray-500">Geen preview beschikbaar</div>';
        debugLog('ERROR: No preview HTML generated');
    }
}

function resetToDefaults() {
    const content = generateContent(currentPlatform);
    const titleInput = document.getElementById('editTitle');
    const descInput = document.getElementById('editDescription');
    
    if (titleInput) titleInput.value = content.title;
    if (descInput) descInput.value = content.description;
    
    updateEditPreview();
    showToast('Tekst gereset naar standaard waarden', 'info');
}

function saveAndPublish() {
    const titleInput = document.getElementById('editTitle');
    const descInput = document.getElementById('editDescription');
    
    // Use user input if provided, otherwise fall back to generated content
    const generatedContent = generateContent(currentPlatform);
    const title = (titleInput && titleInput.value.trim() !== '') ? titleInput.value : generatedContent.title;
    const description = (descInput && descInput.value.trim() !== '') ? descInput.value : generatedContent.description;
    
    console.log('Saving with custom content:', { title, description });
    
    closeEditModal();
    publishListing(currentPlatform, { title, description });
}

// Field management functions
function clearTitleField() {
    const titleInput = document.getElementById('editTitle');
    if (titleInput) {
        titleInput.value = '';
        updateEditPreview();
        showToast('Titel gewist - automatische titel wordt gebruikt', 'info');
    }
}

function clearDescriptionField() {
    const descInput = document.getElementById('editDescription');
    if (descInput) {
        descInput.value = '';
        updateEditPreview();
        showToast('Beschrijving gewist - automatische beschrijving wordt gebruikt', 'info');
    }
}

function setupEditModalListeners() {
    const titleInput = document.getElementById('editTitle');
    const descInput = document.getElementById('editDescription');
    
    debugLog('Setting up edit modal input listeners');
    
    if (titleInput) {
        // Remove any existing listeners first
        titleInput.removeEventListener('input', updateEditPreview);
        titleInput.removeEventListener('keyup', updateEditPreview);
        titleInput.removeEventListener('change', updateEditPreview);
        
        // Add new listeners
        titleInput.addEventListener('input', function() {
            debugLog('Title input changed:', this.value);
            updateEditPreview();
        });
        titleInput.addEventListener('keyup', updateEditPreview);
        titleInput.addEventListener('change', updateEditPreview);
        debugLog('Title input listeners added');
    }
    
    if (descInput) {
        // Remove any existing listeners first
        descInput.removeEventListener('input', updateEditPreview);
        descInput.removeEventListener('keyup', updateEditPreview);
        descInput.removeEventListener('change', updateEditPreview);
        
        // Add new listeners
        descInput.addEventListener('input', function() {
            debugLog('Description input changed:', this.value.substring(0, 50) + '...');
            updateEditPreview();
        });
        descInput.addEventListener('keyup', updateEditPreview);
        descInput.addEventListener('change', updateEditPreview);
        debugLog('Description input listeners added');
    }
}

// Toast notification system
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 toast-notification max-w-sm`;
    
    const bgColors = {
        'success': 'bg-green-500',
        'error': 'bg-red-500',
        'warning': 'bg-yellow-500',
        'info': 'bg-blue-500'
    };
    
    const icons = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    };
    
    toast.classList.add(bgColors[type] || bgColors.info);
    toast.innerHTML = `
        <div class="flex items-center text-white">
            <i class="fa-solid ${icons[type] || icons.info} mr-3"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 4000);
}

// Preview Modal functions
function closePreviewModal() {
    const modal = document.getElementById('previewModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }
}

function publishFromPreview() {
    closePreviewModal();
    publishCurrentPlatform();
}

// Utility functions
function deleteListing(listingId) {
    if (!confirm('Weet je zeker dat je deze advertentie wilt verwijderen?')) {
        return;
    }
    
    fetch(`/listings/${listingId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Advertentie verwijderd', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Fout bij verwijderen: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Er is een fout opgetreden', 'error');
    });
}
</script>

@endsection
