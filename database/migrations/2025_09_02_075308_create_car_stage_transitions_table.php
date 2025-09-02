<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('car_stage_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('cars')->cascadeOnDelete()->index();
            $table->foreignId('from_stage_id')->nullable()->constrained('car_stages')->nullOnDelete();
            $table->foreignId('to_stage_id')->constrained('car_stages')->cascadeOnDelete();
            $table->timestamp('changed_at')->index(); // log-moment (UTC)
            $table->timestamps();

            // extra indexen voor rapportages
            $table->index(['car_id', 'changed_at']);
            $table->index(['to_stage_id', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_stage_transitions');
    }
};