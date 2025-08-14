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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('question_count');
            $table->decimal('price', 8, 2)->default(0);
            $table->string('link')->nullable();
            $table->integer('duration_minutes');
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->string('quiz_type')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
