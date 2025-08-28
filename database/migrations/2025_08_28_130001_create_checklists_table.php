<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_id');
            $table->unsignedBigInteger('stage_id');
            $table->string('task');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
            $table->foreign('stage_id')->references('id')->on('car_stages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklists');
    }
};
