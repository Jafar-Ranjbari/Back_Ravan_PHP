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
        // Step 1: Add missing columns first
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
        });
        
        // Step 2: Rename column if exists
        if (Schema::hasColumn('users', 'describtion')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('describtion', 'description');
            });
        }
        
        // Step 3: Modify columns after rename
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile', 20)->nullable()->change();
            $table->tinyInteger('age')->unsigned()->nullable()->change();
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
            $table->integer('age')->nullable()->change();
            $table->boolean('is_active')->nullable()->change();
            
            if (Schema::hasColumn('users', 'description')) {
                $table->string('description')->nullable()->change();
            }
        });
        
        // Rename back
        if (Schema::hasColumn('users', 'description')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('description', 'describtion');
            });
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
