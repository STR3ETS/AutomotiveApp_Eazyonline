<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CarImageController extends Controller
{
    public function store(Request $request, Car $car)
    {
        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB max
            'category' => 'string|in:exterior,interior,engine,damage,documents,other',
            'alt_text' => 'string|max:255'
        ]);

        $uploadedImages = [];
        $category = $request->input('category', 'exterior');

        foreach ($request->file('images') as $index => $image) {
            $filename = $this->generateUniqueFilename($image);
            
            // Store original image
            $path = $image->storeAs('car-images', $filename, 'public');
            
            // Create thumbnail
            $this->createThumbnail($image, $filename);
            
            // Get image metadata
            $metadata = $this->getImageMetadata($image);
            
            // Create database record
            $carImage = $car->addImage($filename, [
                'original_filename' => $image->getClientOriginalName(),
                'alt_text' => $request->input('alt_text'),
                'category' => $category,
                'sort_order' => $car->images()->count() + $index,
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
                'metadata' => $metadata
            ]);

            $uploadedImages[] = $carImage;
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedImages) . ' afbeelding(en) succesvol geüpload',
            'images' => $uploadedImages
        ]);
    }

    public function destroy(CarImage $carImage)
    {
        $carImage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Afbeelding succesvol verwijderd'
        ]);
    }

    public function makePrimary(CarImage $carImage)
    {
        $carImage->makePrimary();

        return response()->json([
            'success' => true,
            'message' => 'Afbeelding ingesteld als hoofdafbeelding'
        ]);
    }

    public function updateOrder(Request $request, Car $car)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|exists:car_images,id',
            'images.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->input('images') as $imageData) {
            CarImage::where('id', $imageData['id'])
                ->where('car_id', $car->id)
                ->update(['sort_order' => $imageData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Volgorde succesvol bijgewerkt'
        ]);
    }

    private function generateUniqueFilename($image): string
    {
        $extension = $image->getClientOriginalExtension();
        return Str::uuid() . '.' . $extension;
    }

    private function createThumbnail($image, string $filename): void
    {
        $thumbnailPath = 'car-images/thumbnails/';
        $thumbnailFilename = pathinfo($filename, PATHINFO_FILENAME) . '_thumb.' . pathinfo($filename, PATHINFO_EXTENSION);
        
        // Create thumbnail directory if it doesn't exist
        Storage::disk('public')->makeDirectory($thumbnailPath);
        
        // Create image manager with GD driver
        $manager = new ImageManager(new Driver());
        
        // Create and save thumbnail (API changed in v3.x)
        $thumbnail = $manager->read($image->getRealPath())
            ->scaleDown(300, 200);
            
        Storage::disk('public')->put($thumbnailPath . $thumbnailFilename, $thumbnail->encode());
    }

    private function getImageMetadata($image): array
    {
        // Create image manager with GD driver
        $manager = new ImageManager(new Driver());
        $imageResource = $manager->read($image->getRealPath());
        
        return [
            'width' => $imageResource->width(),
            'height' => $imageResource->height(),
            'size_formatted' => $this->formatBytes($image->getSize())
        ];
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = array('B', 'KB', 'MB', 'GB');
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
