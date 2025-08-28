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
    Schema::create('afspraken', function (Blueprint $table) {
        $table->id();
        $table->string('titel');
        $table->dateTime('datum');
        $table->string('klant_naam')->nullable();
        $table->string('type')->nullable(); // proefrit, aflevering, levering onderdelen
        $table->foreignId('auto_id')->nullable()->constrained('autos')->onDelete('cascade');
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
