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
        // Step 1: Add missing columns first
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
        });
        
        // Step 2: Rename column using raw SQL for MariaDB compatibility
        if (Schema::hasColumn('users', 'describtion')) {
            DB::statement('ALTER TABLE users CHANGE describtion description TEXT NULL');
        }
        
        // Step 3: Modify other columns
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile', 20)->nullable()->change();
            $table->smallInteger('age')->unsigned()->nullable()->change();
            $table->boolean('is_active')->default(true)->change();
            
            // Only modify description if it exists now
            if (Schema::hasColumn('users', 'description')) {
                $table->text('description')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse in opposite order
        Schema::table('users', function (Blueprint $table) {
            // Revert column changes
            $table->string('mobile')->nullable()->change();
            $table->smallInteger('age')->nullable()->change();
            $table->boolean('is_active')->nullable()->change();
            
            if (Schema::hasColumn('users', 'description')) {
                $table->string('description')->nullable()->change();
            }
        });
        
        // Rename back using raw SQL
        if (Schema::hasColumn('users', 'description')) {
            DB::statement('ALTER TABLE users CHANGE description describtion TEXT NULL');
        }
        
        // Remove added columns
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }
};
