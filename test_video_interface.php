<?php

require_once 'vendor/autoload.php';

use App\Models\Car;
use App\Models\CarVideo;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Auto Video Interface Test ===\n\n";

try {
    // Find a car
    $car = Car::with('videos', 'company')->first();
    if (!$car) {
        echo "❌ Geen auto gevonden\n";
        exit;
    }
    
    echo "✅ Test auto: {$car->brand} {$car->model} (ID: {$car->id})\n";
    echo "   Huidige video's: {$car->videos->count()}\n\n";
    
    // Test add a demo video if none exist
    if ($car->videos->count() == 0) {
        echo "📹 Demo video toevoegen...\n";
        $demoVideo = CarVideo::create([
            'car_id' => $car->id,
            'company_id' => $car->company_id,
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'platform' => 'youtube',
            'video_title' => 'Auto Showcase Video',
            'video_description' => 'Een mooie showcase van deze auto',
            'category' => 'overview',
            'is_featured' => true,
            'sort_order' => 1,
        ]);
        
        $demoVideo->generateThumbnail();
        
        echo "✅ Demo video toegevoegd!\n";
        echo "   Video ID: {$demoVideo->id}\n";
        echo "   Embed URL: {$demoVideo->embed_url}\n";
        echo "   Thumbnail: {$demoVideo->thumbnail_url}\n\n";
    }
    
    // Show the URL to test the interface
    echo "🌐 Test de interface op:\n";
    echo "   http://localhost/autos/{$car->id}\n\n";
    
    echo "✅ Video systeem is klaar!\n";
    echo "🎬 Je kunt nu:\n";
    echo "   - Video's toevoegen via de interface\n";
    echo "   - YouTube, Vimeo en directe links gebruiken\n";
    echo "   - Video's afspelen in de modal\n";
    echo "   - Featured video's instellen\n";
    echo "   - Video's verwijderen\n";
    
} catch (Exception $e) {
    echo "❌ Fout: " . $e->getMessage() . "\n";
}
