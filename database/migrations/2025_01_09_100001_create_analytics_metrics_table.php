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
        Schema::create('analytics_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->index()->comment('Company/Tenant ID for multi-tenancy');
            $table->string('provider')->default('ga4')->comment('Analytics provider: ga4, etc');
            $table->string('metric_name')->comment('active_users_30min, sessions, etc');
            $table->bigInteger('metric_value')->comment('Numeric value of the metric');
            $table->json('metadata')->nullable()->comment('Additional metric context');
            $table->timestamp('recorded_at')->comment('When this metric was recorded');
            $table->timestamps();

            // Indexes for performance
            $table->index(['tenant_id', 'provider', 'metric_name']);
            $table->index(['recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_metrics');
    }
};
