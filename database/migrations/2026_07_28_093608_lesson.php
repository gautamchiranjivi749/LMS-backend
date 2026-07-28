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
        Schema::create('lessons',function(Blueprint $table)

        {

        $table->id();
        $table->foreignId('course_id')->constrained()->cascadeonDelete();
        $table->string('title');
            $table->text('description')->nullable();

        $table->string('video')->nullable();

        $table->integer('duration')->nullable(); // minutes

        $table->integer('lesson_order')->default(1);

        $table->boolean('is_preview')->default(false);

        $table->boolean('status')->default(true);

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
