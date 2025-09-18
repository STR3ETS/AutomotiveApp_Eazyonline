<?php

namespace App\Mappers;

use App\Models\Car;
use App\DTO\ListingDTO;

class ListingMapper
{
    /**
     * Map Car model to ListingDTO
     */
    public static function fromCar(Car $car): ListingDTO
    {
        $title = self::generateTitle($car);
        $description = self::generateDescription($car);
        $images = self::getCarImages($car);
        $attributes = self::getCarAttributes($car);

        return new ListingDTO(
            title: $title,
            description: $description,
            priceCents: (int) ($car->price * 100),
            currency: 'EUR',
            condition: self::mapCondition($car),
            category: 'cars',
            attributes: $attributes,
            images: $images
        );
    }

    /**
     * Generate listing title
     */
    private static function generateTitle(Car $car): string
    {
        $title = "{$car->brand} {$car->model}";
        
        if ($car->year) {
            $title .= " ({$car->year})";
        }

        // Add mileage if reasonable
        if ($car->mileage && $car->mileage < 500000) {
            $formattedMileage = number_format($car->mileage, 0, ',', '.');
            $title .= " - {$formattedMileage} km";
        }

        return $title;
    }

    /**
     * Generate listing description
     */
    private static function generateDescription(Car $car): string
    {
        $description = "Te koop: {$car->brand} {$car->model}";
        
        if ($car->year) {
            $description .= " uit {$car->year}";
        }

        $description .= "\n\n";

        // Technical specifications
        $description .= "📋 Specificaties:\n";
        $description .= "• Merk: {$car->brand}\n";
        $description .= "• Model: {$car->model}\n";
        
        if ($car->year) {
            $description .= "• Bouwjaar: {$car->year}\n";
        }
        
        if ($car->mileage) {
            $formattedMileage = number_format($car->mileage, 0, ',', '.');
            $description .= "• Kilometerstand: {$formattedMileage} km\n";
        }
        
        $description .= "• Kenteken: {$car->license_plate}\n";

        // Price
        if ($car->price) {
            $formattedPrice = number_format($car->price, 0, ',', '.');
            $description .= "• Vraagprijs: €{$formattedPrice}\n";
        }

        // Additional notes
        if (!empty($car->notes)) {
            $description .= "\n📝 Opmerkingen:\n{$car->notes}\n";
        }

        // Status/condition
        if ($car->status) {
            $description .= "\n🔧 Status: {$car->status}\n";
        }

        // Contact information
        $description .= "\n📞 Voor meer informatie of een bezichtiging, neem contact met ons op!\n";

        return $description;
    }

    /**
     * Get car images
     */
    private static function getCarImages(Car $car): array
    {
        if (!$car->hasImages()) {
            return [];
        }

        return $car->images()
            ->orderBy('is_primary', 'desc')
            ->orderBy('sort_order')
            ->limit(10) // Marketplace usually has image limits
            ->get()
            ->map(function ($image) {
                // Return full URL or file path depending on marketplace requirements
                return $image->url;
            })
            ->toArray();
    }

    /**
     * Get car attributes for marketplace
     */
    private static function getCarAttributes(Car $car): array
    {
        $attributes = [
            'brand' => $car->brand,
            'model' => $car->model,
            'license_plate' => $car->license_plate,
        ];

        if ($car->year) {
            $attributes['year'] = (string) $car->year;
        }

        if ($car->mileage) {
            $attributes['mileage'] = (string) $car->mileage;
            $attributes['mileage_unit'] = 'km';
        }

        if ($car->price) {
            $attributes['price_eur'] = (string) $car->price;
        }

        // Add stage/status
        if ($car->status) {
            $attributes['status'] = $car->status;
        }

        // Add stage information
        if ($car->stage) {
            $attributes['stage'] = $car->stage->name;
        }

        return $attributes;
    }

    /**
     * Map car condition to marketplace standards
     */
    private static function mapCondition(Car $car): string
    {
        // Map based on car year or status
        if ($car->year && $car->year >= date('Y')) {
            return 'new';
        }

        // Could also check car status or notes for damage indicators
        if ($car->notes && str_contains(strtolower($car->notes), 'schade')) {
            return 'damaged';
        }

        return 'used';
    }

    /**
     * Validate that car is ready for listing
     */
    public static function validateCar(Car $car): array
    {
        $errors = [];

        if (empty($car->brand)) {
            $errors[] = 'Merk is verplicht';
        }

        if (empty($car->model)) {
            $errors[] = 'Model is verplicht';
        }

        if (empty($car->license_plate)) {
            $errors[] = 'Kenteken is verplicht';
        }

        if (!$car->year || $car->year < 1950 || $car->year > date('Y') + 1) {
            $errors[] = 'Geldig bouwjaar is verplicht';
        }

        if (!$car->price || $car->price <= 0) {
            $errors[] = 'Geldige prijs is verplicht';
        }

        if ($car->mileage < 0) {
            $errors[] = 'Kilometerstand kan niet negatief zijn';
        }

        // Check if car is in publishable stage
        $publishableStages = ['Commercieel gereed', 'Verkoop klaar'];
        if (!in_array($car->status, $publishableStages)) {
            $errors[] = "Auto moet in fase '{$publishableStages[0]}' of '{$publishableStages[1]}' zijn om gepubliceerd te kunnen worden";
        }

        return $errors;
    }

    /**
     * Check if car can be published
     */
    public static function canPublish(Car $car): bool
    {
        return empty(self::validateCar($car));
    }
}
