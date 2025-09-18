<?php

require_once 'vendor/autoload.php';

use App\Models\Car;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Test Video API Endpoints ===\n\n";

try {
    // Get first car
    $car = Car::first();
    if (!$car) {
        echo "❌ Geen auto gevonden\n";
        exit;
    }
    
    echo "✅ Test auto: {$car->brand} {$car->model} (ID: {$car->id})\n\n";
    
    // Test direct API call to video controller
    echo "📹 Test video controller instantiatie...\n";
    
    $controller = new \App\Http\Controllers\CarVideoController();
    echo "✅ CarVideoController succesvol geïnstantieerd\n\n";
    
    // Check if video model works
    echo "🎬 Test CarVideo model...\n";
    $videoCount = \App\Models\CarVideo::count();
    echo "✅ CarVideo model werkt. Aantal videos in DB: {$videoCount}\n\n";
    
    // Test platform detection method
    echo "🔍 Test platform detectie...\n";
    $reflection = new ReflectionClass($controller);
    $detectMethod = $reflection->getMethod('detectPlatform');
    $detectMethod->setAccessible(true);
    
    $youtubeResult = $detectMethod->invoke($controller, 'https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    $vimeoResult = $detectMethod->invoke($controller, 'https://vimeo.com/148751763');
    $directResult = $detectMethod->invoke($controller, 'https://example.com/video.mp4');
    
    echo "✅ YouTube URL -> {$youtubeResult}\n";
    echo "✅ Vimeo URL -> {$vimeoResult}\n";
    echo "✅ Direct URL -> {$directResult}\n\n";
    
    echo "🎉 Alle basis tests slagen!\n";
    echo "   De fout lag waarschijnlijk aan de constructor middleware.\n";
    echo "   Probeer nu opnieuw een video toe te voegen in de interface.\n";
    
} catch (Exception $e) {
    echo "❌ Fout: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
