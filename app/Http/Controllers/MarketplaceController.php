<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarListing;
use App\Models\ListingTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarketplaceController extends Controller
{
    public function show(Car $car)
    {
        $car->load(['images' => function($query) {
            $query->orderBy('sort_order')->orderBy('created_at');
        }, 'listings', 'stage']);

        return view('marketplace.show', compact('car'));
    }

    public function preview(Request $request, Car $car)
    {
        try {
            $request->validate([
                'platform' => 'required|in:marktplaats,instagram,facebook,autotrack',
                'image_ids' => 'array',
                'image_ids.*' => 'exists:car_images,id',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:5000'
            ]);

            // Generate smart content based on platform
            $content = $this->generateSmartContent($car, $request->platform);
            
            // Use custom content if provided
            $title = $request->title ?: $content['title'];
            $description = $request->description ?: $content['description'];
            
            // Select images to use
            $imageIds = $request->image_ids ?: $car->images->take(5)->pluck('id')->toArray();
            $selectedImages = $car->images->whereIn('id', $imageIds);

            return response()->json([
                'success' => true,
                'preview' => [
                    'title' => $title,
                    'description' => $description,
                    'platform' => $request->platform,
                    'images' => $selectedImages->map(function($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->url,
                            'thumbnail_url' => $image->thumbnail_url,
                            'alt_text' => $image->alt_text
                        ];
                    })->values(),
                    'car' => [
                        'brand' => $car->brand,
                        'model' => $car->model,
                        'year' => $car->year,
                        'price' => $car->price,
                        'mileage' => $car->mileage,
                        'license_plate' => $car->license_plate
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Er is een fout opgetreden: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createListing(Request $request, Car $car)
    {
        $request->validate([
            'platform' => 'required|in:marktplaats,instagram,facebook,autotrack',
            'image_ids' => 'array',
            'image_ids.*' => 'exists:car_images,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000'
        ]);

        // Generate smart content based on platform
        $content = $this->generateSmartContent($car, $request->platform);
        
        // Use custom content if provided
        $title = $request->title ?: $content['title'];
        $description = $request->description ?: $content['description'];
        
        // Select images to use
        $imageIds = $request->image_ids ?: $car->images->take(5)->pluck('id')->toArray();

        // Create listing record
        $listing = CarListing::create([
            'car_id' => $car->id,
            'company_id' => $car->company_id,
            'listing_template_id' => null, // No longer using templates
            'platform' => $request->platform,
            'status' => 'draft',
            'generated_title' => $title,
            'generated_description' => $description,
            'used_images' => $imageIds
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Listing concept aangemaakt',
            'listing_id' => $listing->id,
            'preview' => [
                'title' => $title,
                'description' => $description,
                'images' => $car->images->whereIn('id', $imageIds)->values()
            ]
        ]);
    }

    public function publishListing(Request $request, CarListing $listing)
    {
        try {
            // Here we would integrate with actual platform APIs
            // For now, we'll simulate publishing
            
            switch ($listing->platform) {
                case 'marktplaats':
                    $result = $this->publishToMarktplaats($listing);
                    break;
                case 'instagram':
                    $result = $this->publishToInstagram($listing);
                    break;
                case 'facebook':
                    $result = $this->publishToFacebook($listing);
                    break;
                default:
                    throw new \Exception('Platform not supported');
            }

            $listing->markAsPublished($result['external_id'], $result['url']);

            return response()->json([
                'success' => true,
                'message' => 'Advertentie succesvol gepubliceerd!',
                'listing_url' => $result['url']
            ]);

        } catch (\Exception $e) {
            $listing->markAsError($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Fout bij publiceren: ' . $e->getMessage()
            ], 422);
        }
    }

    public function previewListing(CarListing $listing)
    {
        $listing->load(['car.images', 'listingTemplate']);
        
        $images = $listing->car->images->whereIn('id', $listing->used_images)->map(function($image) {
            return [
                'url' => $image->url,
                'thumbnail_url' => $image->thumbnail_url,
                'alt_text' => $image->alt_text
            ];
        });

        return response()->json([
            'title' => $listing->generated_title,
            'description' => $listing->generated_description,
            'images' => $images,
            'platform' => $listing->platform,
            'car' => [
                'brand' => $listing->car->brand,
                'model' => $listing->car->model,
                'year' => $listing->car->year,
                'price' => $listing->car->price
            ]
        ]);
    }

    // Placeholder methods for platform integrations
    private function publishToMarktplaats(CarListing $listing): array
    {
        // In a real implementation, this would use Marktplaats API
        // For now, simulate a successful publish
        
        return [
            'external_id' => 'MP_' . time() . '_' . $listing->id,
            'url' => 'https://www.marktplaats.nl/v/auto/'. rand(1000000, 9999999)
        ];
    }

    private function publishToInstagram(CarListing $listing): array
    {
        // In a real implementation, this would use Instagram Business API
        
        return [
            'external_id' => 'IG_' . time() . '_' . $listing->id,
            'url' => 'https://www.instagram.com/p/' . Str::random(11)
        ];
    }

    private function publishToFacebook(CarListing $listing): array
    {
        // In a real implementation, this would use Facebook Marketplace API
        
        return [
            'external_id' => 'FB_' . time() . '_' . $listing->id,
            'url' => 'https://www.facebook.com/marketplace/item/' . rand(100000000000000, 999999999999999)
        ];
    }

    /**
     * Generate smart content based on car data and platform
     */
    private function generateSmartContent(Car $car, string $platform): array
    {
        $platformTemplates = [
            'marktplaats' => [
                'title' => '{brand} {model} ({year}) - €{price}',
                'description' => "Te koop: {brand} {model}\n\n🚗 Bouwjaar: {year}\n📊 Kilometerstand: {mileage} km\n💰 Prijs: €{price}\n\n✅ Dealer occasie\n✅ Garantie mogelijk\n✅ Inruil welkom\n\nInteresse? Neem contact op voor meer informatie of een proefrit!"
            ],
            'instagram' => [
                'title' => '🚗 {brand} {model} | {year} | €{price}',
                'description' => "🚗 {brand} {model} ({year})\n\n📍 Nu beschikbaar bij ons!\n🔥 {mileage}km | €{price}\n\n#{brand_lower} #{model_lower} #auto #occasions #dealer #{year}"
            ],
            'facebook' => [
                'title' => '{brand} {model} - {year} | {mileage}km',
                'description' => "{brand} {model} te koop!\n\nBouwjaar: {year}\nKilometerstand: {mileage} km\nDealer prijs: €{price}\n\nBetrouwbare dealer met garantie en service.\nBericht ons voor meer info!"
            ]
        ];

        $template = $platformTemplates[$platform] ?? $platformTemplates['marktplaats'];
        
        // Prepare replacement values
        $replacements = [
            '{brand}' => $car->brand,
            '{model}' => $car->model,
            '{year}' => $car->year,
            '{price}' => number_format($car->price, 0, ',', '.'),
            '{mileage}' => number_format($car->mileage, 0, ',', '.'),
            '{brand_lower}' => strtolower($car->brand),
            '{model_lower}' => strtolower(str_replace(' ', '', $car->model))
        ];

        // Generate title and description
        $title = str_replace(array_keys($replacements), array_values($replacements), $template['title']);
        $description = str_replace(array_keys($replacements), array_values($replacements), $template['description']);

        return [
            'title' => $title,
            'description' => $description
        ];
    }

    public function deleteListing(CarListing $listing)
    {
        // In a real implementation, you would also delete from the external platform
        
        $listing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Advertentie verwijderd'
        ]);
    }
}
