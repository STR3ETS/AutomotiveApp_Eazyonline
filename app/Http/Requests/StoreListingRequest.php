<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Add your authorization logic here
        // For now, allow if user is authenticated
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'price' => 'required|numeric|min:0|max:999999.99',
            'currency' => 'sometimes|string|in:EUR,USD,GBP',
            'condition' => 'sometimes|string|in:new,used,damaged',
            'category' => 'sometimes|string|max:100',
            'attributes' => 'sometimes|array',
            'attributes.*' => 'string|max:255',
            'images' => 'sometimes|array|max:10',
            'images.*' => 'file|image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB max per image
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Een titel is verplicht.',
            'title.max' => 'De titel mag maximaal 255 karakters bevatten.',
            'description.required' => 'Een beschrijving is verplicht.',
            'description.max' => 'De beschrijving mag maximaal 5000 karakters bevatten.',
            'price.required' => 'Een prijs is verplicht.',
            'price.numeric' => 'De prijs moet een geldig getal zijn.',
            'price.min' => 'De prijs moet minimaal €0 zijn.',
            'price.max' => 'De prijs mag maximaal €999.999,99 zijn.',
            'currency.in' => 'De valuta moet EUR, USD of GBP zijn.',
            'condition.in' => 'De conditie moet new, used of damaged zijn.',
            'category.max' => 'De categorie mag maximaal 100 karakters bevatten.',
            'attributes.array' => 'Attributen moeten een array zijn.',
            'images.array' => 'Afbeeldingen moeten een array zijn.',
            'images.max' => 'Maximaal 10 afbeeldingen toegestaan.',
            'images.*.file' => 'Elke afbeelding moet een geldig bestand zijn.',
            'images.*.image' => 'Alleen afbeeldingsbestanden zijn toegestaan.',
            'images.*.mimes' => 'Alleen JPEG, PNG, JPG en WebP bestanden zijn toegestaan.',
            'images.*.max' => 'Elke afbeelding mag maximaal 10MB groot zijn.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'titel',
            'description' => 'beschrijving',
            'price' => 'prijs',
            'currency' => 'valuta',
            'condition' => 'conditie',
            'category' => 'categorie',
            'attributes' => 'attributen',
            'images' => 'afbeeldingen',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            $response = response()->json([
                'success' => false,
                'message' => 'Validatie fouten gevonden.',
                'errors' => $validator->errors(),
            ], 422);

            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }

        parent::failedValidation($validator);
    }

    /**
     * Transform the validated input
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated();

        // Convert price to cents if needed
        if (isset($validated['price'])) {
            $validated['price_cents'] = (int) ($validated['price'] * 100);
        }

        // Set defaults
        $validated['currency'] = $validated['currency'] ?? 'EUR';
        $validated['condition'] = $validated['condition'] ?? 'used';
        $validated['category'] = $validated['category'] ?? 'cars';
        $validated['attributes'] = $validated['attributes'] ?? [];

        return $key ? data_get($validated, $key, $default) : $validated;
    }
}
