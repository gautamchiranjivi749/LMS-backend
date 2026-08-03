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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
              $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('course_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('quiz_attempt_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();

    $table->string('certificate_no')->unique();

    $table->date('issued_date');

    $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
