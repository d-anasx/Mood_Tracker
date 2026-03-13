<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteCategory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name', 'mood_level'];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'category_id');
    }

    // -------------------------------------------------------
    // Helper methods
    // -------------------------------------------------------

    /**
     * Get a random active quote from this category.
     */
    public function randomActiveQuote(): ?Quote
    {
        return $this->quotes()->where('is_active', true)->inRandomOrder()->first();
    }

    /**
     * Find the category that best matches a given mood level.
     */
    public static function forMoodLevel(int $moodLevel): ?self
    {
        return self::where('mood_level', $moodLevel)->first();
    }
}
