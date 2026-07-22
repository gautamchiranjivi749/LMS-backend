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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
             $table->foreignId('teacher_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->string('title');

            $table->string('slug')->unique();

            $table->text('description');

            $table->string('thumbnail')->nullable();

            $table->decimal('price',10,2)->default(0);

            $table->enum('level',[
                'beginner',
                'intermediate',
                'advanced'
            ])->default('beginner');

            $table->string('language')->default('English');

            $table->boolean('status')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
