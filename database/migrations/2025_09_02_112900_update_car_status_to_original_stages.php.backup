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
        // First extend the ENUM to include new values
        DB::statement("ALTER TABLE cars MODIFY COLUMN status ENUM('intake','technical','ready_for_sale','test_drive','sold','Intake','Technische controle','Herstel & Onderhoud','Commercieel gereed','Verkoop klaar')");
        
        // Update existing data to new format
        DB::table('cars')
            ->where('status', 'intake')
            ->update(['status' => 'Intake']);
            
        DB::table('cars')
            ->where('status', 'technical')
            ->update(['status' => 'Technische controle']);
            
        DB::table('cars')
            ->where('status', 'ready_for_sale')
            ->update(['status' => 'Verkoop klaar']);
        
        // Now remove the old enum values
        DB::statement("ALTER TABLE cars MODIFY COLUMN status ENUM('Intake','Technische controle','Herstel & Onderhoud','Commercieel gereed','Verkoop klaar','test_drive','sold')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Update data back to old format
        DB::table('cars')
            ->where('status', 'Intake')
            ->update(['status' => 'intake']);
            
        DB::table('cars')
            ->where('status', 'Technische controle')
            ->update(['status' => 'technical']);
            
        DB::table('cars')
            ->where('status', 'Verkoop klaar')
            ->update(['status' => 'ready_for_sale']);
        
        // Restore old enum values
        DB::statement("ALTER TABLE cars MODIFY COLUMN status ENUM('intake','technical','ready_for_sale','test_drive','sold') NOT NULL");
    }
};
