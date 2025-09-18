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
        Schema::create('api_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->index()->comment('Company/Tenant ID for multi-tenancy');
            $table->string('provider')->comment('marketplace, ga4, etc');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('extra')->nullable()->comment('Provider-specific settings and metadata');
            $table->timestamps();

            // Indexes for performance
            $table->index(['tenant_id', 'provider']);
            $table->unique(['tenant_id', 'provider'], 'unique_tenant_provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_integrations');
    }
};
