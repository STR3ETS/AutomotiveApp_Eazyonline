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
        Schema::create('car_listings', function (Blueprint $table) {
            $table->id();            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('listing_template_id')->constrained()->onDelete('cascade');
            $table->enum('platform', ['marktplaats', 'instagram', 'facebook', 'autotrack', 'custom']);
            $table->string('external_id')->nullable(); // ID from external platform
            $table->string('listing_url')->nullable(); // URL to the published listing
            $table->enum('status', ['draft', 'pending', 'published', 'sold', 'expired', 'error'])->default('draft');
            $table->text('generated_title');
            $table->text('generated_description');
            $table->json('used_images')->nullable(); // Array of image IDs used
            $table->json('platform_response')->nullable(); // Response from platform API
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['car_id', 'platform']);
            $table->index(['company_id', 'status']);
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_listings');
    }
};
