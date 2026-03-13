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
        Schema::create('mood_entry_feelings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mood_entry_id')->constrained('mood_entries')->onDelete('cascade');
            $table->foreignId('feeling_id')->constrained('feelings')->onDelete('cascade');

            // Prevent duplicate feeling per entry
            $table->unique(['mood_entry_id', 'feeling_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mood_entry_feelings');
    }
};
