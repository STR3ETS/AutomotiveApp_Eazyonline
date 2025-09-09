<?php

namespace App\DTO;

class ListingDTO
{
    public function __construct(
        public string $title,
        public string $description,
        public int $priceCents,
        public string $currency = 'EUR',
        public string $condition = 'used',
        public string $category = 'cars',
        public array $attributes = [],
        public array $images = []
    ) {}

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'],
            priceCents: (int) ($data['price_cents'] ?? ($data['price'] * 100)),
            currency: $data['currency'] ?? 'EUR',
            condition: $data['condition'] ?? 'used',
            category: $data['category'] ?? 'cars',
            attributes: $data['attributes'] ?? [],
            images: $data['images'] ?? []
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'price_cents' => $this->priceCents,
            'currency' => $this->currency,
            'condition' => $this->condition,
            'category' => $this->category,
            'attributes' => $this->attributes,
            'images' => $this->images,
        ];
    }

    /**
     * Get price in euros
     */
    public function getPriceInEuros(): float
    {
        return $this->priceCents / 100;
    }

    /**
     * Validate the DTO
     */
    public function validate(): array
    {
        $errors = [];

        if (empty($this->title)) {
            $errors[] = 'Title is required';
        }

        if (empty($this->description)) {
            $errors[] = 'Description is required';
        }

        if ($this->priceCents <= 0) {
            $errors[] = 'Price must be greater than 0';
        }

        if (count($this->images) > 10) {
            $errors[] = 'Maximum 10 images allowed';
        }

        return $errors;
    }

    /**
     * Check if DTO is valid
     */
    public function isValid(): bool
    {
        return empty($this->validate());
    }
}
