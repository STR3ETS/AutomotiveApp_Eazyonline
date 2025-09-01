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
  Schema::create('afspraaks', function (Blueprint $table) {
    $table->id();
    $table->string('titel');
    $table->dateTime('datum');
    $table->string('klant_naam')->nullable();
    $table->string('type')->nullable(); // proefrit, aflevering, levering onderdelen
    $table->unsignedBigInteger('car_id')->nullable();
    $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
    $table->timestamps();
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afspraaks');
    }
};
