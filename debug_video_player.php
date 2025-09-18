<?php

require_once 'vendor/autoload.php';

use App\Models\Car;
use App\Models\CarVideo;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Video Player Debug ===\n\n";

try {
    // Find car with videos
    $car = Car::with('videos')->whereHas('videos')->first();
    if (!$car) {
        echo "❌ Geen auto met video's gevonden\n";
        exit;
    }
    
    echo "✅ Auto met video's: {$car->brand} {$car->model} (ID: {$car->id})\n";
    echo "   Aantal video's: {$car->videos->count()}\n\n";
    
    foreach ($car->videos as $video) {
        echo "📹 Video ID: {$video->id}\n";
        echo "   Titel: " . ($video->video_title ?: 'Geen titel') . "\n";
        echo "   Originele URL: {$video->video_url}\n";
        echo "   Platform: {$video->platform}\n";
        echo "   Embed URL: {$video->embed_url}\n";
        echo "   Platform Video ID: " . ($video->platform_video_id ?: 'Geen ID') . "\n";
        echo "   Thumbnail URL: " . ($video->thumbnail_url ?: 'Geen thumbnail') . "\n";
        echo "   Featured: " . ($video->is_featured ? 'Ja' : 'Nee') . "\n";
        echo "   ---\n";
    }
    
    // Test een specifieke video
    $testVideo = $car->videos->first();
    if ($testVideo) {
        echo "\n🧪 Test details voor video ID {$testVideo->id}:\n";
        
        // Test YouTube URL parsing
        if ($testVideo->platform === 'youtube') {
            echo "   YouTube test:\n";
            echo "   - Video ID extracted: " . $testVideo->platform_video_id . "\n";
            echo "   - Expected embed: https://www.youtube.com/embed/" . $testVideo->platform_video_id . "\n";
            echo "   - Actual embed: " . $testVideo->embed_url . "\n";
            echo "   - Thumbnail: " . $testVideo->thumbnail_url . "\n";
        } elseif ($testVideo->platform === 'vimeo') {
            echo "   Vimeo test:\n";
            echo "   - Video ID extracted: " . $testVideo->platform_video_id . "\n";
            echo "   - Expected embed: https://player.vimeo.com/video/" . $testVideo->platform_video_id . "\n";
            echo "   - Actual embed: " . $testVideo->embed_url . "\n";
        } else {
            echo "   Direct URL test:\n";
            echo "   - Direct URL: " . $testVideo->video_url . "\n";
            echo "   - Embed URL: " . $testVideo->embed_url . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Fout: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
