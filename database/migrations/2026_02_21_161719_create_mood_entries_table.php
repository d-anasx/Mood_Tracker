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
        Schema::create('mood_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('mood_level'); // 1-10
            $table->float('sleep_hours')->nullable(); // 0-24
            $table->text('reflection')->nullable(); // max 500 chars enforced at app level
            $table->date('entry_date'); // one entry per day per user
            $table->timestamps();
            // Enforce one entry per user per day
            $table->unique(['user_id', 'entry_date']);
            $table->index('entry_date'); // optimized for date-based queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mood_entries');
    }
};
