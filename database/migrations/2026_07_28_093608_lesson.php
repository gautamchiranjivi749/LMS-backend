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
        Schema::create('lessons', function (Blueprint $table) {

            $table->id();

            // Relationship
            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();

            // Lesson Information
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Video
            $table->enum('video_type', [
                'upload',
                'youtube',
                'vimeo'
            ])->default('upload');

            $table->string('video_url')->nullable();
            $table->string('video_file')->nullable();

            // Lesson Details
            $table->integer('duration')->nullable()
                ->comment('Duration in seconds');

            $table->integer('order')
                ->default(1);

            $table->boolean('is_preview')
                ->default(false);

            $table->boolean('status')
                ->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};