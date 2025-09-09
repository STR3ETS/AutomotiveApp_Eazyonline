<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Car;
use App\Models\CarImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CarImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all cars
        $cars = Car::all();

        $this->command->info("Seeding images for {$cars->count()} cars...");

        foreach ($cars as $index => $car) {
            $this->command->info("Processing car {$car->license_plate}...");

            // Add 2-4 images per car
            $imageCount = rand(2, 4);
            
            for ($i = 0; $i < $imageCount; $i++) {
                // Determine category
                $categories = ['exterior', 'interior', 'engine', 'other'];
                $category = $categories[$i % count($categories)];

                // Create colored images for each car
                $this->createFallbackImage($car, $category, $i === 0);
            }
        }

        $this->command->info('Car images seeded successfully!');
    }

    private function createFallbackImage(Car $car, string $category, bool $isPrimary): void
    {
        // Create directories if they don't exist
        Storage::disk('public')->makeDirectory('car-images');
        Storage::disk('public')->makeDirectory('car-images/thumbnails');

        // Create a simple colored rectangle as fallback
        $manager = new ImageManager(new Driver());
        
        // Different colors for different categories and cars
        $colors = [
            'exterior' => ['#3B82F6', '#1D4ED8', '#2563EB', '#1E40AF'],
            'interior' => ['#EF4444', '#DC2626', '#B91C1C', '#991B1B'],
            'engine' => ['#10B981', '#059669', '#047857', '#065F46'],
            'other' => ['#6B7280', '#4B5563', '#374151', '#1F2937']
        ];
        
        $colorOptions = $colors[$category] ?? $colors['other'];
        $color = $colorOptions[$car->id % count($colorOptions)];
        
        // Create a 800x600 colored rectangle
        $image = $manager->create(800, 600)->fill($color);
        
        // Generate unique filename
        $filename = Str::uuid() . '.jpg';
        
        // Store original image
        $originalData = $image->toJpeg(quality: 80);
        Storage::disk('public')->put('car-images/' . $filename, $originalData);
        
        // Create thumbnail
        $thumbnail = $image->scaleDown(300, 200);
        $thumbnailFilename = pathinfo($filename, PATHINFO_FILENAME) . '_thumb.' . pathinfo($filename, PATHINFO_EXTENSION);
        
        $thumbnailData = $thumbnail->toJpeg(quality: 80);
        Storage::disk('public')->put('car-images/thumbnails/' . $thumbnailFilename, $thumbnailData);

        // Create database record
        CarImage::create([
            'car_id' => $car->id,
            'company_id' => $car->company_id,
            'filename' => $filename,
            'original_filename' => 'seeded_' . $category . '_' . $car->id . '.jpg',
            'alt_text' => "{$car->brand} {$car->model} - {$category}",
            'category' => $category,
            'sort_order' => CarImage::where('car_id', $car->id)->count(),
            'is_primary' => $isPrimary,
            'file_size' => strlen($originalData),
            'mime_type' => 'image/jpeg',
            'metadata' => [
                'width' => 800,
                'height' => 600,
                'size_formatted' => $this->formatBytes(strlen($originalData))
            ]
        ]);
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
