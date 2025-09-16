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
        Schema::table('car_listings', function (Blueprint $table) {
            // Make listing_template_id nullable for new simplified system
            $table->unsignedBigInteger('listing_template_id')->nullable()->change();
            
            // Add metadata for enhanced functionality
            $table->json('platform_config')->nullable()->after('used_images');
            $table->timestamp('scheduled_publish_at')->nullable()->after('published_at');
            $table->text('custom_hashtags')->nullable()->after('platform_config');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_listings', function (Blueprint $table) {
            $table->dropColumn(['platform_config', 'scheduled_publish_at', 'custom_hashtags']);
            
            // Note: Cannot easily reverse nullable change without data loss
            // You may need to handle this manually if reverting
        });
    }
};
