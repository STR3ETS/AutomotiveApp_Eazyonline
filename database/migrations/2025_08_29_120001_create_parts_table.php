<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('repair_id');
            $table->string('name');
            $table->enum('status', ['besteld','geleverd','gemonteerd']);
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('repair_id')->references('id')->on('repairs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
