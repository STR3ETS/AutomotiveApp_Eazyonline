@extends('layouts.app')

@section('content')
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
                                <div class="relative cursor-pointer group" onclick="toggleImageSelection({{ $image->id }})">
                                    <img src="{{ $image->thumbnail_url }}" 
                                         alt="{{ $image->alt_text }}"
                                         class="w-full h-20 object-cover rounded-lg border-2 border-transparent transition-all duration-200"
                                         id="image-{{ $image->id }}">
                                    
                                    <!-- Selection Overlay -->
                                    <div class="absolute inset-0 bg-[var(--status-info)] bg-opacity-0 rounded-lg flex items-center justify-center transition-all duration-200" 
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
                                        <button onclick="deleteListing({{ $listing->id }})"
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

            <!-- Right Column - Publishing Options -->
            <div class="space-y-6">
                <!-- Quick Publish -->
                <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-rocket text-[var(--status-special)]"></i>
                        Snel Publiceren
                    </h2>
                    
                    <div class="space-y-3">
                        <button onclick="quickPublish('marktplaats')" 
                                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2"
                                {{ $car->images->count() === 0 ? 'disabled' : '' }}>
                            <i class="fa-solid fa-shopping-cart"></i>
                            Marktplaats
                        </button>
                        
                        <button onclick="quickPublish('instagram')" 
                                class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2"
                                {{ $car->images->count() === 0 ? 'disabled' : '' }}>
                            <i class="fa-brands fa-instagram"></i>
                            Instagram
                        </button>
                        
                        <button onclick="quickPublish('facebook')" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2"
                                {{ $car->images->count() === 0 ? 'disabled' : '' }}>
                            <i class="fa-brands fa-facebook"></i>
                            Facebook Marketplace
                        </button>
                    </div>
                    
                    @if($car->images->count() === 0)
                        <p class="text-sm text-[var(--status-warning)] mt-3 text-center">
                            <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                            Voeg eerst foto's toe om te kunnen publiceren
                        </p>
                    @endif
                </div>

                <!-- Advanced Options -->
                <div class="bg-[var(--background-card)] rounded-xl shadow-sm border border-[var(--border-light)] p-6">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-cogs text-[var(--status-info)]"></i>
                        Geavanceerde Opties
                    </h2>
                    
                    <button onclick="openAdvancedModal()" 
                            class="w-full border-2 border-[var(--border-light)] hover:border-[var(--status-info)] text-[var(--text-primary)] font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-edit"></i>
                        Aangepaste Advertentie
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Advanced Modal -->
<div id="advancedModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" style="display: flex; align-items: center; justify-content: center;">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Aangepaste Advertentie Maken</h3>
            <button onclick="closeAdvancedModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="advancedForm">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Template</label>
                    <select id="templateSelect" name="template_id" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $template->name }} ({{ ucfirst($template->platform) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                    <select id="platformSelect" name="platform" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500">
                        <option value="marktplaats">Marktplaats</option>
                        <option value="instagram">Instagram</option>
                        <option value="facebook">Facebook Marketplace</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titel</label>
                    <input type="text" id="customTitle" name="custom_title" 
                           class="w-full border border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Laat leeg voor automatische titel">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Beschrijving</label>
                    <textarea id="customDescription" name="custom_description" rows="6"
                              class="w-full border border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Laat leeg voor automatische beschrijving"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Geselecteerde Foto's</label>
                    <div id="selectedImagesPreview" class="grid grid-cols-4 gap-2 min-h-[80px] border border-gray-200 rounded-lg p-3">
                        <div class="text-center text-gray-500 col-span-4 py-4">
                            Selecteer foto's aan de linkerkant
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeAdvancedModal()" 
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-3 px-4 rounded-lg transition duration-200">
                    Annuleren
                </button>
                <button type="button" onclick="previewListing()" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                    <i class="fa-solid fa-eye mr-2"></i>
                    Preview
                </button>
                <button type="submit" id="publishBtn"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                    <i class="fa-solid fa-bullhorn mr-2"></i>
                    Publiceren
                </button>
            </div>
        </form>
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
            <button onclick="closePreviewModal()" class="text-gray-500 hover:text-gray-700">
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
            <button onclick="closePreviewModal()" 
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200">
                Sluiten
            </button>
            <button onclick="publishFromPreview()" 
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                <i class="fa-solid fa-bullhorn mr-2"></i>
                Publiceren
            </button>
        </div>
    </div>
</div>

<script>
let selectedImages = [];

// Image selection
function toggleImageSelection(imageId) {
    const image = document.getElementById(`image-${imageId}`);
    const overlay = document.getElementById(`overlay-${imageId}`);
    const check = document.getElementById(`check-${imageId}`);
    
    if (selectedImages.includes(imageId)) {
        // Deselect
        selectedImages = selectedImages.filter(id => id !== imageId);
        image.classList.remove('border-blue-500');
        overlay.classList.remove('bg-opacity-30');
        check.classList.remove('opacity-100');
    } else {
        // Select (max 10 images)
        if (selectedImages.length < 10) {
            selectedImages.push(imageId);
            image.classList.add('border-blue-500');
            overlay.classList.add('bg-opacity-30');
            check.classList.add('opacity-100');
        } else {
            alert('Maximaal 10 foto\'s kunnen worden geselecteerd');
        }
    }
    
    updateSelectedImagesPreview();
}

function updateSelectedImagesPreview() {
    const preview = document.getElementById('selectedImagesPreview');
    
    if (selectedImages.length === 0) {
        preview.innerHTML = '<div class="text-center text-gray-500 col-span-4 py-4">Selecteer foto\'s aan de linkerkant</div>';
        return;
    }
    
    let html = '';
    selectedImages.forEach(imageId => {
        const originalImg = document.getElementById(`image-${imageId}`);
        html += `<img src="${originalImg.src}" class="w-full h-16 object-cover rounded border">`;
    });
    
    preview.innerHTML = html;
}

// Quick publish
function quickPublish(platform) {
    if (selectedImages.length === 0) {
        // Auto-select primary image or first few images
        const images = @json($car->images->take(5)->pluck('id'));
        selectedImages = images;
        
        // Update UI
        images.forEach(imageId => {
            const image = document.getElementById(`image-${imageId}`);
            const overlay = document.getElementById(`overlay-${imageId}`);
            const check = document.getElementById(`check-${imageId}`);
            
            if (image) {
                image.classList.add('border-blue-500');
                overlay.classList.add('bg-opacity-30');
                check.classList.add('opacity-100');
            }
        });
    }
    
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('template_id', 1); // Default template
    formData.append('platform', platform);
    selectedImages.forEach(id => formData.append('image_ids[]', id));
    
    publishListing(formData);
}

// Advanced modal
function openAdvancedModal() {
    document.getElementById('advancedModal').style.display = 'flex';
    document.getElementById('advancedModal').classList.remove('hidden');
    updateSelectedImagesPreview();
}

function closeAdvancedModal() {
    document.getElementById('advancedModal').style.display = 'none';
    document.getElementById('advancedModal').classList.add('hidden');
}

function closePreviewModal() {
    document.getElementById('previewModal').style.display = 'none';
    document.getElementById('previewModal').classList.add('hidden');
}

function previewListing() {
    const form = document.getElementById('advancedForm');
    const formData = new FormData(form);
    
    // Add selected images
    selectedImages.forEach(imageId => {
        formData.append('image_ids[]', imageId);
    });

    // Show loading state
    const previewContent = document.getElementById('previewContent');
    previewContent.innerHTML = `
        <div class="text-center py-12">
            <i class="fa-solid fa-spinner fa-spin text-gray-400 text-3xl mb-4"></i>
            <p class="text-gray-500">Genereren van preview...</p>
        </div>
    `;

    // Show preview modal
    document.getElementById('previewModal').style.display = 'flex';
    document.getElementById('previewModal').classList.remove('hidden');

    // Fetch preview
    fetch(`/cars/{{ $car->id }}/marketplace/preview`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.log('Error response text:', text);
                throw new Error(`HTTP ${response.status}: ${text.substring(0, 100)}...`);
            });
        }
        
        return response.text().then(text => {
            console.log('Response text:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.log('JSON parse error:', e);
                throw new Error(`Invalid JSON response: ${text.substring(0, 100)}...`);
            }
        });
    })
    .then(data => {
        if (data.success) {
            showPreview(data.preview);
        } else {
            throw new Error(data.message || 'Preview kon niet worden gegenereerd');
        }
    })
    .catch(error => {
        console.error('Preview error:', error);
        previewContent.innerHTML = `
            <div class="text-center py-12">
                <i class="fa-solid fa-exclamation-triangle text-red-400 text-3xl mb-4"></i>
                <p class="text-red-600">Fout bij genereren preview: ${error.message}</p>
                <button onclick="console.log('Debug info logged to console')" class="mt-2 text-sm text-blue-600 underline">Check console voor details</button>
            </div>
        `;
    });
}

function showPreview(preview) {
    const platformTitle = document.getElementById('previewPlatformTitle');
    const previewContent = document.getElementById('previewContent');
    
    // Update platform title
    const platformNames = {
        'marktplaats': 'Marktplaats Preview',
        'instagram': 'Instagram Preview',
        'facebook': 'Facebook Marketplace Preview',
        'autotrack': 'AutoTrack Preview'
    };
    platformTitle.textContent = platformNames[preview.platform] || 'Preview';
    
    // Generate platform-specific preview
    let html = '';
    
    if (preview.platform === 'marktplaats') {
        html = generateMarktplaatsPreview(preview);
    } else if (preview.platform === 'instagram') {
        html = generateInstagramPreview(preview);
    } else if (preview.platform === 'facebook') {
        html = generateFacebookPreview(preview);
    } else {
        html = generateGenericPreview(preview);
    }
    
    previewContent.innerHTML = html;
}

function generateMarktplaatsPreview(preview) {
    const imagesHtml = preview.images.map(img => 
        `<img src="${img.url}" alt="${img.alt_text}" class="w-full h-64 object-cover rounded-lg">`
    ).join('');
    
    return `
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden max-w-2xl mx-auto shadow-lg">
            <!-- Marktplaats Header -->
            <div class="bg-orange-500 p-4">
                <div class="flex items-center gap-2 text-white">
                    <i class="fa-solid fa-shopping-cart text-xl"></i>
                    <span class="font-bold text-lg">Marktplaats</span>
                </div>
            </div>
            
            <!-- Main Image -->
            ${preview.images.length > 0 ? `
                <div class="relative">
                    <img src="${preview.images[0].url}" alt="${preview.images[0].alt_text}" class="w-full h-80 object-cover">
                    ${preview.images.length > 1 ? `
                        <div class="absolute bottom-4 right-4 bg-black bg-opacity-60 text-white px-3 py-1 rounded-full text-sm">
                            1 / ${preview.images.length}
                        </div>
                    ` : ''}
                </div>
            ` : ''}
            
            <!-- Content -->
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-3">${preview.title}</h2>
                
                <div class="flex items-center justify-between mb-4">
                    <div class="text-3xl font-bold text-orange-600">€ ${new Intl.NumberFormat('nl-NL').format(preview.car.price)}</div>
                    <div class="text-gray-600">${preview.car.year} • ${new Intl.NumberFormat('nl-NL').format(preview.car.mileage)} km</div>
                </div>
                
                <div class="prose prose-sm max-w-none text-gray-700 mb-6">
                    ${preview.description.replace(/\\n/g, '<br>')}
                </div>
                
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 bg-gray-50 p-4 rounded-lg">
                    <div><strong>Merk:</strong> ${preview.car.brand}</div>
                    <div><strong>Model:</strong> ${preview.car.model}</div>
                    <div><strong>Bouwjaar:</strong> ${preview.car.year}</div>
                    <div><strong>Kilometerstand:</strong> ${new Intl.NumberFormat('nl-NL').format(preview.car.mileage)} km</div>
                </div>
            </div>
        </div>
    `;
}

function generateInstagramPreview(preview) {
    return `
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden max-w-lg mx-auto shadow-lg">
            <!-- Instagram Header -->
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-car text-white text-sm"></i>
                    </div>
                    <span class="font-semibold">jouw_autobedrijf</span>
                </div>
                <i class="fa-solid fa-ellipsis text-gray-600"></i>
            </div>
            
            <!-- Main Image -->
            ${preview.images.length > 0 ? `
                <div class="relative">
                    <img src="${preview.images[0].url}" alt="${preview.images[0].alt_text}" class="w-full h-96 object-cover">
                    ${preview.images.length > 1 ? `
                        <div class="absolute top-4 right-4 bg-black bg-opacity-60 text-white px-2 py-1 rounded-full text-xs">
                            1/${preview.images.length}
                        </div>
                    ` : ''}
                </div>
            ` : ''}
            
            <!-- Actions -->
            <div class="p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-4">
                        <i class="fa-regular fa-heart text-2xl"></i>
                        <i class="fa-regular fa-comment text-2xl"></i>
                        <i class="fa-regular fa-paper-plane text-2xl"></i>
                    </div>
                    <i class="fa-regular fa-bookmark text-2xl"></i>
                </div>
                
                <div class="text-sm">
                    <p class="font-semibold mb-2">${new Intl.NumberFormat('nl-NL').format(Math.floor(Math.random() * 1000) + 100)} likes</p>
                    <p><span class="font-semibold">jouw_autobedrijf</span> ${preview.title}</p>
                    <p class="text-gray-600 mt-1">${preview.description.substring(0, 100)}${preview.description.length > 100 ? '... meer' : ''}</p>
                    <p class="text-gray-500 text-xs mt-2">${Math.floor(Math.random() * 24) + 1} uur geleden</p>
                </div>
            </div>
        </div>
    `;
}

function generateFacebookPreview(preview) {
    return `
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden max-w-2xl mx-auto shadow-lg">
            <!-- Facebook Header -->
            <div class="bg-blue-600 p-4">
                <div class="flex items-center gap-2 text-white">
                    <i class="fab fa-facebook-f text-xl"></i>
                    <span class="font-bold text-lg">Facebook Marketplace</span>
                </div>
            </div>
            
            <!-- Main Image -->
            ${preview.images.length > 0 ? `
                <div class="relative">
                    <img src="${preview.images[0].url}" alt="${preview.images[0].alt_text}" class="w-full h-80 object-cover">
                    ${preview.images.length > 1 ? `
                        <div class="absolute bottom-4 right-4 bg-black bg-opacity-60 text-white px-3 py-1 rounded-full text-sm">
                            1 / ${preview.images.length}
                        </div>
                    ` : ''}
                </div>
            ` : ''}
            
            <!-- Content -->
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-3">${preview.title}</h2>
                
                <div class="flex items-center justify-between mb-4">
                    <div class="text-3xl font-bold text-blue-600">€${new Intl.NumberFormat('nl-NL').format(preview.car.price)}</div>
                    <div class="text-gray-600">Auto's & Motors</div>
                </div>
                
                <div class="prose prose-sm max-w-none text-gray-700 mb-6">
                    ${preview.description.replace(/\\n/g, '<br>')}
                </div>
                
                <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                    <span><i class="fa-solid fa-map-marker-alt mr-1"></i> Je locatie</span>
                    <span><i class="fa-solid fa-clock mr-1"></i> Zojuist geplaatst</span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 bg-gray-50 p-4 rounded-lg">
                    <div><strong>Merk:</strong> ${preview.car.brand}</div>
                    <div><strong>Model:</strong> ${preview.car.model}</div>
                    <div><strong>Bouwjaar:</strong> ${preview.car.year}</div>
                    <div><strong>Kilometerstand:</strong> ${new Intl.NumberFormat('nl-NL').format(preview.car.mileage)} km</div>
                </div>
            </div>
        </div>
    `;
}

function generateGenericPreview(preview) {
    return `
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden max-w-2xl mx-auto shadow-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-3">${preview.title}</h2>
                
                ${preview.images.length > 0 ? `
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        ${preview.images.slice(0, 4).map(img => 
                            `<img src="${img.url}" alt="${img.alt_text}" class="w-full h-48 object-cover rounded-lg">`
                        ).join('')}
                    </div>
                ` : ''}
                
                <div class="prose prose-sm max-w-none text-gray-700 mb-6">
                    ${preview.description.replace(/\\n/g, '<br>')}
                </div>
                
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 bg-gray-50 p-4 rounded-lg">
                    <div><strong>Prijs:</strong> €${new Intl.NumberFormat('nl-NL').format(preview.car.price)}</div>
                    <div><strong>Merk:</strong> ${preview.car.brand}</div>
                    <div><strong>Model:</strong> ${preview.car.model}</div>
                    <div><strong>Bouwjaar:</strong> ${preview.car.year}</div>
                    <div><strong>Kilometerstand:</strong> ${new Intl.NumberFormat('nl-NL').format(preview.car.mileage)} km</div>
                </div>
            </div>
        </div>
    `;
}

function publishFromPreview() {
    closePreviewModal();
    
    // Trigger the original form submission
    const form = document.getElementById('advancedForm');
    if (form) {
        form.dispatchEvent(new Event('submit'));
    }
}

// Form submission
document.getElementById('advancedForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    selectedImages.forEach(id => formData.append('image_ids[]', id));
    formData.append('template_id', 1); // Default template
    
    publishListing(formData);
});

function publishListing(formData) {
    const publishBtn = document.getElementById('publishBtn');
    const originalText = publishBtn?.innerHTML || 'Publiceren';
    
    if (publishBtn) {
        publishBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Publiceren...';
        publishBtn.disabled = true;
    }
    
    fetch(`/cars/{{ $car->id }}/listings`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Immediately publish the created listing
            return fetch(`/listings/${data.listing_id}/publish`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
        } else {
            throw new Error(data.message);
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Advertentie succesvol gepubliceerd!');
            location.reload();
        } else {
            alert('Fout: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Er is een fout opgetreden: ' + error.message);
    })
    .finally(() => {
        if (publishBtn) {
            publishBtn.innerHTML = originalText;
            publishBtn.disabled = false;
        }
    });
}

function deleteListing(listingId) {
    if (confirm('Weet je zeker dat je deze advertentie wilt verwijderen?')) {
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
                location.reload();
            } else {
                alert('Fout bij verwijderen');
            }
        });
    }
}

// Close modal with escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAdvancedModal();
        closePreviewModal();
    }
});
</script>
@endsection
