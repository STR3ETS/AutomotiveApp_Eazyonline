@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-full">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('sold-cars.index') }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <i class="fa-solid fa-arrow-left mr-2"></i>
                        Terug naar overzicht
                    </a>
                    
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $soldCar->license_plate }}</h1>
                        <p class="text-gray-600">{{ $soldCar->brand }} {{ $soldCar->model }} ({{ $soldCar->year }})</p>
                    </div>
                </div>
                
                <div class="text-right">
                    <div class="text-sm text-gray-500">Verkocht op</div>
                    <div class="text-xl font-bold text-gray-900">{{ $soldCar->sold_at->format('d-m-Y H:i') }}</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Car Images -->
                @if($soldCar->images && count($soldCar->images) > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fa-solid fa-images text-blue-600"></i>
                                Foto's
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($soldCar->images as $image)
                                    <div class="relative group">
                                        <img src="{{ $image['url'] ?? '/storage/cars/' . $image['filename'] }}" 
                                             alt="{{ $soldCar->brand }} {{ $soldCar->model }}"
                                             class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity"
                                             onclick="openLightbox('{{ $image['url'] ?? '/storage/cars/' . $image['filename'] }}')">
                                        @if($image['is_primary'])
                                            <div class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded">
                                                Hoofdfoto
                                            </div>
                                        @endif
                                        @if(isset($image['category']))
                                            <div class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                                {{ ucfirst($image['category']) }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Car Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-car text-blue-600"></i>
                            Voertuig Details
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Kenteken</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $soldCar->license_plate }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Merk & Model</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $soldCar->brand }} {{ $soldCar->model }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Bouwjaar</label>
                                        <p class="text-lg text-gray-900">{{ $soldCar->year }}</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Kilometerstand</label>
                                        <p class="text-lg text-gray-900">{{ number_format($soldCar->mileage) }} km</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Oorspronkelijke Prijs</label>
                                        <p class="text-lg text-gray-900">€ {{ number_format($soldCar->original_price, 2) }}</p>
                                    </div>
                                    @if($soldCar->purchase_price)
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Inkoopprijs</label>
                                            <p class="text-lg text-gray-900">€ {{ number_format($soldCar->purchase_price, 2) }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-user text-green-600"></i>
                            Klant Informatie
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Naam</label>
                                        <p class="text-lg font-semibold text-gray-900">{{ $soldCar->customer_name }}</p>
                                    </div>
                                    @if($soldCar->customer_email)
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">E-mail</label>
                                            <p class="text-lg text-gray-900">
                                                <a href="mailto:{{ $soldCar->customer_email }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                                                    {{ $soldCar->customer_email }}
                                                </a>
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="space-y-4">
                                    @if($soldCar->customer_phone)
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Telefoon</label>
                                            <p class="text-lg text-gray-900">
                                                <a href="tel:{{ $soldCar->customer_phone }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                                                    {{ $soldCar->customer_phone }}
                                                </a>
                                            </p>
                                        </div>
                                    @endif
                                    @if($soldCar->customer_address)
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Adres</label>
                                            <p class="text-lg text-gray-900">{{ $soldCar->customer_address }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($soldCar->notes)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fa-solid fa-sticky-note text-yellow-600"></i>
                                Notities
                            </h2>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-900 whitespace-pre-line">{{ $soldCar->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                
                <!-- Sale Summary -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-green-600"></i>
                            Verkoop Samenvatting
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Verkoopprijs</span>
                            <span class="text-lg font-bold text-gray-900">€ {{ number_format($soldCar->sale_price, 2) }}</span>
                        </div>
                        
                        @if($soldCar->deposit_amount)
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500">Aanbetaling</span>
                                <span class="text-lg font-semibold text-green-600">€ {{ number_format($soldCar->deposit_amount, 2) }}</span>
                            </div>
                        @endif

                        @if($soldCar->purchase_price)
                            <div class="pt-4 border-t">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-500">Inkoopprijs</span>
                                    <span class="text-lg text-gray-900">€ {{ number_format($soldCar->purchase_price, 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500">Bruto Winst</span>
                                    <span class="text-lg font-bold @if($soldCar->profit >= 0) text-green-600 @else text-red-600 @endif">
                                        € {{ number_format($soldCar->profit, 2) }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500">Winstmarge</span>
                                    <span class="text-lg font-semibold @if($soldCar->profit_margin >= 0) text-green-600 @else text-red-600 @endif">
                                        {{ number_format($soldCar->profit_margin, 1) }}%
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-clock text-purple-600"></i>
                            Tijdlijn
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Auto verkocht</p>
                                    <p class="text-xs text-gray-500">{{ $soldCar->sold_at->format('d-m-Y H:i') }}</p>
                                </div>
                            </div>
                            
                            @if($soldCar->delivery_date)
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Afleverdatum</p>
                                        <p class="text-xs text-gray-500">{{ $soldCar->delivery_date->format('d-m-Y') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Reference Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-link text-gray-600"></i>
                            Referentie
                        </h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Originele Auto ID</label>
                            <p class="text-sm text-gray-900">#{{ $soldCar->original_car_id }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Verkoop ID</label>
                            <p class="text-sm text-gray-900">#{{ $soldCar->original_sale_id }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Klant ID</label>
                            <p class="text-sm text-gray-900">#{{ $soldCar->original_customer_id }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="lightboxModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden" onclick="closeLightbox()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative max-w-4xl max-h-full">
            <button type="button" onclick="closeLightbox()" class="absolute top-4 right-4 text-white hover:text-gray-300 text-2xl z-10">
                <i class="fa-solid fa-times"></i>
            </button>
            <img id="lightboxImage" src="" alt="Car Image" class="max-w-full max-h-full object-contain">
        </div>
    </div>
</div>

<script>
function openLightbox(imageSrc) {
    document.getElementById('lightboxImage').src = imageSrc;
    document.getElementById('lightboxModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightboxModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close lightbox on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeLightbox();
    }
});
</script>
@endsection
