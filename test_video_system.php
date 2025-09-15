<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Car;
use App\Models\CarVideo;
use App\Models\Company;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Car Video System ===\n\n";

try {
    // 1. Find first car
    $car = Car::with('company')->first();
    if (!$car) {
        echo "❌ Geen auto's gevonden in database\n";
        exit;
    }
    
    echo "✅ Auto gevonden: {$car->brand} {$car->model} (ID: {$car->id})\n";
    echo "   Bedrijf: {$car->company->name}\n\n";
    
    // 2. Test YouTube video toevoegen
    echo "📹 Test 1: YouTube video toevoegen...\n";
    $youtubeVideo = CarVideo::create([
        'car_id' => $car->id,
        'company_id' => $car->company_id,
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'platform' => 'youtube',
        'video_title' => 'Test YouTube Video',
        'video_description' => 'Dit is een test YouTube video',
        'category' => 'overview',
        'is_featured' => true,
        'sort_order' => 1,
    ]);
    
    echo "✅ YouTube video toegevoegd (ID: {$youtubeVideo->id})\n";
    echo "   Platform: {$youtubeVideo->platform}\n";
    echo "   Embed URL: {$youtubeVideo->embed_url}\n";
    echo "   Video ID: {$youtubeVideo->platform_video_id}\n\n";
    
    // 3. Test Vimeo video toevoegen
    echo "📹 Test 2: Vimeo video toevoegen...\n";
    $vimeoVideo = CarVideo::create([
        'car_id' => $car->id,
        'company_id' => $car->company_id,
        'video_url' => 'https://vimeo.com/148751763',
        'platform' => 'vimeo',
        'video_title' => 'Test Vimeo Video',
        'video_description' => 'Dit is een test Vimeo video',
        'category' => 'interior',
        'is_featured' => false,
        'sort_order' => 2,
    ]);
    
    echo "✅ Vimeo video toegevoegd (ID: {$vimeoVideo->id})\n";
    echo "   Platform: {$vimeoVideo->platform}\n";
    echo "   Embed URL: {$vimeoVideo->embed_url}\n";
    echo "   Video ID: {$vimeoVideo->platform_video_id}\n\n";
    
    // 4. Test direct URL video toevoegen
    echo "📹 Test 3: Direct URL video toevoegen...\n";
    $directVideo = CarVideo::create([
        'car_id' => $car->id,
        'company_id' => $car->company_id,
        'video_url' => 'https://example.com/test-video.mp4',
        'platform' => 'direct_url',
        'video_title' => 'Test Direct Video',
        'video_description' => 'Dit is een direct gehoste video',
        'category' => 'engine',
        'is_featured' => false,
        'sort_order' => 3,
    ]);
    
    echo "✅ Direct URL video toegevoegd (ID: {$directVideo->id})\n";
    echo "   Platform: {$directVideo->platform}\n";
    echo "   Embed URL: {$directVideo->embed_url}\n\n";
    
    // 5. Test auto relaties
    echo "🔗 Test 4: Auto relaties testen...\n";
    $carWithVideos = Car::with('videos', 'featuredVideo')->find($car->id);
    
    echo "✅ Auto heeft {$carWithVideos->videos->count()} video(s)\n";
    if ($carWithVideos->featuredVideo) {
        echo "✅ Featured video: {$carWithVideos->featuredVideo->video_title}\n";
    } else {
        echo "❌ Geen featured video gevonden\n";
    }
    
    // 6. Test featured video wijzigen
    echo "\n🌟 Test 5: Featured video wijzigen...\n";
    $vimeoVideo->makeFeatured();
    
    $updatedCar = Car::with('featuredVideo')->find($car->id);
    if ($updatedCar->featuredVideo && $updatedCar->featuredVideo->id === $vimeoVideo->id) {
        echo "✅ Featured video succesvol gewijzigd naar Vimeo video\n";
    } else {
        echo "❌ Featured video wijziging mislukt\n";
    }
    
    // 7. Test scopes
    echo "\n🔍 Test 6: Scopes testen...\n";
    $featuredVideos = CarVideo::featured()->where('car_id', $car->id)->count();
    $youtubeVideos = CarVideo::byPlatform('youtube')->where('car_id', $car->id)->count();
    $overviewVideos = CarVideo::byCategory('overview')->where('car_id', $car->id)->count();
    
    echo "✅ Featured videos: {$featuredVideos}\n";
    echo "✅ YouTube videos: {$youtubeVideos}\n";
    echo "✅ Overview videos: {$overviewVideos}\n";
    
    // 8. Test thumbnail generatie
    echo "\n🖼️ Test 7: Thumbnail generatie...\n";
    $youtubeVideo->generateThumbnail();
    $youtubeVideo->refresh();
    
    if ($youtubeVideo->thumbnail_url) {
        echo "✅ YouTube thumbnail gegenereerd: {$youtubeVideo->thumbnail_url}\n";
    } else {
        echo "❌ YouTube thumbnail generatie mislukt\n";
    }
    
    // 9. Test video verwijderen
    echo "\n🗑️ Test 8: Video verwijderen...\n";
    $directVideo->delete();
    
    $remainingVideos = CarVideo::where('car_id', $car->id)->count();
    echo "✅ Video verwijderd. Overgebleven videos: {$remainingVideos}\n";
    
    // 10. Toon finale overzicht
    echo "\n📊 Finale overzicht:\n";
    $finalVideos = CarVideo::where('car_id', $car->id)->orderBy('sort_order')->get();
    
    foreach ($finalVideos as $video) {
        echo "   - {$video->video_title} ({$video->platform}) - Featured: " . ($video->is_featured ? 'Ja' : 'Nee') . "\n";
    }
    
    echo "\n✅ Alle tests succesvol uitgevoerd!\n";
    echo "🎉 Video systeem is klaar voor gebruik!\n\n";
    
} catch (Exception $e) {
    echo "❌ Fout opgetreden: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
