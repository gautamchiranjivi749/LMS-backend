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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            //student
            $table->foreignId('user_id')
            ->constrained()->cascadeonDelete();
            //course
            $table->foreignId('course_id')
             ->constrained()->cascadeonDelete();

            // Enrollment information
            $table->timestamp('enrolled_at')->useCurrent();

            $table->enum('status', [
                'active',
                'completed',
                'cancelled'
            ])->default('active');

            // Progress percentage (0–100)
            $table->unsignedTinyInteger('progress')
                ->default(0);

            $table->timestamps();

            // Prevent duplicate enrollments
            $table->unique([
                'user_id',
                'course_id'
            ]);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
