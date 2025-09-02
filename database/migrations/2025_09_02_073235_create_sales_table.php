<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('cars')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();

            $table->decimal('sale_price', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->nullable();

            $table->enum('payment_status', ['open','deposit_paid','paid','refunded'])->default('open');
            $table->enum('status', ['option','contract_signed','ready_for_delivery','delivered','cancelled'])->default('option');

            $table->timestamp('contract_signed_at')->nullable();
            $table->date('delivery_date')->nullable();
            $table->time('delivery_time')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('sold_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};