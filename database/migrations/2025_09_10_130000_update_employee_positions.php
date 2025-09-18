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
        // First add job_title column to preserve existing position data
        Schema::table('employees', function (Blueprint $table) {
            $table->string('job_title')->nullable()->after('position');
        });

        // Move existing position data to job_title
        DB::statement("UPDATE employees SET job_title = position");

        // Set all positions to 'medewerker' first
        DB::statement("UPDATE employees SET position = 'medewerker'");

        // Change position column to enum
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('position', ['eigenaar', 'medewerker'])->change();
        });

        // Set first employee of each company as owner (safer approach)
        $companies = DB::table('companies')->get();
        foreach ($companies as $company) {
            $firstEmployee = DB::table('employees')
                ->where('company_id', $company->id)
                ->orderBy('id')
                ->first();
            
            if ($firstEmployee) {
                DB::table('employees')
                    ->where('id', $firstEmployee->id)
                    ->update(['position' => 'eigenaar']);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('position')->change();
            $table->dropColumn('job_title');
        });
    }
};
