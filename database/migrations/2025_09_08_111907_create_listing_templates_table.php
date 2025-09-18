<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('listing_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name'); // "Marktplaats Standaard", "Instagram Stories", etc.
            $table->enum('platform', ['marktplaats', 'instagram', 'facebook', 'autotrack', 'custom']);
            $table->string('title_template'); // "{brand} {model} {year} - {mileage}km"
            $table->text('description_template'); // Long description with placeholders
            $table->json('image_settings')->nullable(); // Which images to use, watermark settings
            $table->json('branding_settings')->nullable(); // Logo position, colors, etc.
            $table->json('platform_specific')->nullable(); // Platform specific settings
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['company_id', 'platform']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_templates');
    }
};
