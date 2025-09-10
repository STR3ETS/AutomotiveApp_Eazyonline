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
            // Add role and user_id to employees table
            Schema::table('employees', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->enum('role', ['eigenaar', 'medewerker'])->default('medewerker');
                $table->timestamp('last_login_at')->nullable();
                
                $table->index(['company_id', 'role']);
            });

            // Add role to users table  
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['eigenaar', 'manager', 'medewerker'])->default('medewerker')->after('company_id');
                $table->timestamp('last_login_at')->nullable();
                $table->boolean('active')->default(true);
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn(['user_id', 'role', 'last_login_at']);
            });

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['role', 'last_login_at', 'active']);
            });
        }
    };
