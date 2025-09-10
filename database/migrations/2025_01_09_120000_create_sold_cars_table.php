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
        Schema::create('sold_cars', function (Blueprint $table) {
            $table->id();
            
            // Originele auto informatie
            $table->string('license_plate');
            $table->string('brand');
            $table->string('model');
            $table->integer('year');
            $table->integer('mileage');
            $table->decimal('original_price', 10, 2); // Oorspronkelijke vraagprijs
            $table->decimal('purchase_price', 10, 2)->nullable(); // Inkoopprijs
            
            // Verkoop informatie
            $table->decimal('sale_price', 10, 2); // Werkelijke verkoopprijs
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->timestamp('sold_at'); // Datum van verkoop
            $table->timestamp('delivery_date')->nullable(); // Datum van aflevering
            
            // Klant informatie (gekopieerd van customer tabel)
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            
            // Verkoop details
            $table->text('notes')->nullable();
            $table->json('images')->nullable(); // Kopie van auto foto's
            
            // Originele referenties (voor historie)
            $table->unsignedBigInteger('original_car_id'); // ID van originele auto
            $table->unsignedBigInteger('original_sale_id'); // ID van originele verkoop
            $table->unsignedBigInteger('original_customer_id'); // ID van originele klant
            
            // Company relatie
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['company_id', 'sold_at']);
            $table->index(['license_plate']);
            $table->index(['brand', 'model']);
            $table->index('sold_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sold_cars');
    }
};
