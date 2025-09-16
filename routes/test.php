<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/test-marketplace', function () {
    return view('test-marketplace');
});

Route::post('/test-marketplace/preview', function (Request $request) {
    $platform = $request->input('platform', 'marktplaats');
    
    // Mock car data
    $carData = [
        'brand' => 'BMW',
        'model' => 'X5',
        'year' => 2020,
        'price' => 45000,
        'mileage' => 75000
    ];
    
    // Generate content based on platform
    $platformTemplates = [
        'marktplaats' => [
            'title' => '{brand} {model} ({year}) - €{price}',
            'description' => "Te koop: {brand} {model}\n\nBouwjaar: {year}\nKilometerstand: {mileage} km\nPrijs: €{price}\n\nDealer occasie met garantie!"
        ],
        'instagram' => [
            'title' => '🚗 {brand} {model} | {year} | €{price}',
            'description' => "🚗 {brand} {model} ({year})\n\n📍 Nu beschikbaar!\n🔥 {mileage}km | €{price}\n\n#bmw #x5 #auto #occasions"
        ],
        'facebook' => [
            'title' => '{brand} {model} - {year} | {mileage}km',
            'description' => "{brand} {model} te koop!\n\nBouwjaar: {year}\nKilometerstand: {mileage} km\nPrijs: €{price}\n\nBetrouwbare dealer!"
        ]
    ];
    
    $template = $platformTemplates[$platform] ?? $platformTemplates['marktplaats'];
    
    // Replace placeholders
    $replacements = [
        '{brand}' => $carData['brand'],
        '{model}' => $carData['model'],
        '{year}' => $carData['year'],
        '{price}' => number_format($carData['price'], 0, ',', '.'),
        '{mileage}' => number_format($carData['mileage'], 0, ',', '.')
    ];
    
    $title = str_replace(array_keys($replacements), array_values($replacements), $template['title']);
    $description = str_replace(array_keys($replacements), array_values($replacements), $template['description']);
    
    return response()->json([
        'success' => true,
        'platform' => $platform,
        'title' => $title,
        'description' => $description,
        'car' => $carData
    ]);
});
