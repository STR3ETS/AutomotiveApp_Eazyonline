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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('email')->nullable()->after('subdomain');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('website')->nullable()->after('address');
            $table->string('kvk_number')->nullable()->after('website');
            $table->string('btw_number')->nullable()->after('kvk_number');
            $table->string('secondary_color')->nullable()->after('primary_color');
            $table->string('logo')->nullable()->after('secondary_color');
            $table->json('settings')->nullable()->after('logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'email', 'phone', 'address', 'website', 'kvk_number', 
                'btw_number', 'secondary_color', 'logo', 'settings'
            ]);
        });
    }
};
