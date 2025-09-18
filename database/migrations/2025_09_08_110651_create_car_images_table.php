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
        Schema::create('car_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('alt_text')->nullable();
            $table->enum('category', ['exterior', 'interior', 'engine', 'damage', 'documents', 'other'])->default('exterior');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->string('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->json('metadata')->nullable(); // EXIF data, dimensions, etc.
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['car_id', 'sort_order']);
            $table->index(['car_id', 'is_primary']);
            $table->index(['company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_images');
    }
};
