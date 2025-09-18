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
        // Auto placeholder URLs gebaseerd op merk
        $carImagesByBrand = [
            'BMW' => [
                'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&q=80',
                'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&q=80',
                'https://images.unsplash.com/photo-1617886322078-3574d985fb26?w=800&q=80',
                'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=800&q=80'
            ],
            'Mercedes' => [
                'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=800&q=80',
                'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=800&q=80',
                'https://images.unsplash.com/photo-1606016159991-0c45f7b20ba3?w=800&q=80'
            ],
            'Audi' => [
                'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&q=80',
                'https://images.unsplash.com/photo-1542362567-b07e54358753?w=800&q=80',
                'https://images.unsplash.com/photo-1614200187524-dc4b892acf16?w=800&q=80'
            ],
            'Volkswagen' => [
                'https://images.unsplash.com/photo-1502877338535-766e1452684a?w=800&q=80',
                'https://images.unsplash.com/photo-1533407988329-8b3a0ad4ea8d?w=800&q=80'
            ],
            'Toyota' => [
                'https://images.unsplash.com/photo-1562158079-6d4bec9e5a2d?w=800&q=80',
                'https://images.unsplash.com/photo-1609521263047-f8f205293f24?w=800&q=80'
            ],
            'Ford' => [
                'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&q=80',
                'https://images.unsplash.com/photo-1523983302122-73e869e1f850?w=800&q=80'
            ],
            'Honda' => [
                'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&q=80'
            ],
            'Nissan' => [
                'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=800&q=80'
            ]
        ];

        // Generic car images als fallback
        $genericCarImages = [
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&q=80',
            'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=800&q=80',
            'https://images.unsplash.com/photo-1485463611174-f302f6a5c1c9?w=800&q=80',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&q=80',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&q=80'
        ];

        // Get all cars
        $cars = Car::all();

        $this->command->info("Seeding images for {$cars->count()} cars...");

        foreach ($cars as $car) {
            $this->command->info("Processing car {$car->license_plate} ({$car->brand} {$car->model})...");

            // Determine brand-specific images or use generic
            $brandImages = $carImagesByBrand[$car->brand] ?? $genericCarImages;
            
            // Add 2-4 images per car
            $imageCount = rand(2, 4);
            
            for ($i = 0; $i < $imageCount; $i++) {
                // Determine category
                $categories = ['exterior', 'interior', 'engine', 'other'];
                $category = $categories[$i % count($categories)];

                // Try to download real image first, fallback to colored rectangle
                $imageUrl = $brandImages[$i % count($brandImages)];
                
                try {
                    $this->downloadAndStoreImage($car, $imageUrl, $category, $i === 0);
                } catch (\Exception $e) {
                    $this->command->warn("Failed to download image for {$car->license_plate}: {$e->getMessage()}");
                    // Create a fallback image based on car brand color
                    $this->createBrandBasedImage($car, $category, $i === 0);
                }
            }
        }

        $this->command->info('Car images seeded successfully!');
    }

    private function downloadAndStoreImage(Car $car, string $imageUrl, string $category, bool $isPrimary): void
    {
        // Create directories if they don't exist
        Storage::disk('public')->makeDirectory('car-images');
        Storage::disk('public')->makeDirectory('car-images/thumbnails');

        // Set context options to handle SSL and user agent
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'timeout' => 30
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);

        // Download image
        $imageData = @file_get_contents($imageUrl, false, $context);
        if (!$imageData) {
            throw new \Exception("Could not download image from {$imageUrl}");
        }

        // Generate unique filename
        $filename = Str::uuid() . '.jpg';
        
        // Store original image
        Storage::disk('public')->put('car-images/' . $filename, $imageData);

        // Create thumbnail using Intervention Image
        $manager = new ImageManager(new Driver());
        $image = $manager->read($imageData);
        
        // Create thumbnail
        $thumbnail = $image->scaleDown(300, 200);
        $thumbnailFilename = pathinfo($filename, PATHINFO_FILENAME) . '_thumb.' . pathinfo($filename, PATHINFO_EXTENSION);
        
        Storage::disk('public')->put('car-images/thumbnails/' . $thumbnailFilename, $thumbnail->toJpeg(quality: 80));

        // Get image metadata
        $metadata = [
            'width' => $image->width(),
            'height' => $image->height(),
            'size_formatted' => $this->formatBytes(strlen($imageData))
        ];

        // Create database record
        CarImage::create([
            'car_id' => $car->id,
            'company_id' => $car->company_id,
            'filename' => $filename,
            'original_filename' => 'real_' . strtolower($car->brand) . '_' . $category . '.jpg',
            'alt_text' => "{$car->brand} {$car->model} - {$category}",
            'category' => $category,
            'sort_order' => CarImage::where('car_id', $car->id)->count(),
            'is_primary' => $isPrimary,
            'file_size' => strlen($imageData),
            'mime_type' => 'image/jpeg',
            'metadata' => $metadata
        ]);
    }

    private function createBrandBasedImage(Car $car, string $category, bool $isPrimary): void
    {
        // Create directories if they don't exist
        Storage::disk('public')->makeDirectory('car-images');
        Storage::disk('public')->makeDirectory('car-images/thumbnails');

        // Create a gradient image based on brand
        $manager = new ImageManager(new Driver());
        
        // Brand-specific colors
        $brandColors = [
            'BMW' => ['#1C69D4', '#0F4C9C'], // BMW Blue
            'Mercedes' => ['#00ADEF', '#003963'], // Mercedes Blue
            'Audi' => ['#BB0A30', '#660617'], // Audi Red
            'Volkswagen' => ['#041E42', '#001122'], // VW Dark Blue
            'Toyota' => ['#EB0A1E', '#C40E23'], // Toyota Red
            'Ford' => ['#003478', '#001E4A'], // Ford Blue
            'Honda' => ['#E60012', '#B8000E'], // Honda Red
            'Nissan' => ['#C3002F', '#9B0024'] // Nissan Red
        ];
        
        $colors = $brandColors[$car->brand] ?? ['#4B5563', '#374151']; // Default gray
        
        // Create a 800x600 gradient
        $image = $manager->create(800, 600)->fill($colors[0]);
        
        // Add brand text overlay if we wanted to make it more obvious
        
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
            'original_filename' => 'brand_' . strtolower($car->brand) . '_' . $category . '.jpg',
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
