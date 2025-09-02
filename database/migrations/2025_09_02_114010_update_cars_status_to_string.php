<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update existing data to new stage names
        DB::table('cars')->where('status', 'intake')->update(['status' => 'Intake']);
        DB::table('cars')->where('status', 'technical')->update(['status' => 'Technische controle']);
        DB::table('cars')->where('status', 'ready_for_sale')->update(['status' => 'Verkoop klaar']);
        
        // Change column type from enum to string
        Schema::table('cars', function (Blueprint $table) {
            $table->string('status')->default('Intake')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert data back to old format
        DB::table('cars')->where('status', 'Intake')->update(['status' => 'intake']);
        DB::table('cars')->where('status', 'Technische controle')->update(['status' => 'technical']);
        DB::table('cars')->where('status', 'Verkoop klaar')->update(['status' => 'ready_for_sale']);
        
        // Change back to enum
        Schema::table('cars', function (Blueprint $table) {
            $table->enum('status', ['intake','technical','ready_for_sale','test_drive','sold'])->change();
        });
    }
};
