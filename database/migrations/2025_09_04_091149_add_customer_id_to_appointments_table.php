<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Voeg customer_id toe
            $table->foreignId('customer_id')->nullable()->after('car_id')->constrained('customers')->nullOnDelete();
            
            // Maak customer_name nullable zodat we later kunnen overstappen
            $table->string('customer_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
            $table->string('customer_name')->nullable(false)->change();
        });
    }
};
