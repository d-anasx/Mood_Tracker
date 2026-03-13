<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    use HasFactory;

    public $updatedAt = false;

    protected $fillable = [
        'text',
        'author',
        'category_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuoteCategory::class, 'category_id');
    }

    // -------------------------------------------------------
    // Scopes
    // -------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // -------------------------------------------------------
    // Helper methods
    // -------------------------------------------------------

    /**
     * Get a random active quote for a given mood level.
     */
    public static function forMoodLevel(int $moodLevel): ?self
    {
        $category = QuoteCategory::forMoodLevel($moodLevel);

        if (!$category) {
            return null;
        }

        return self::active()
            ->where('category_id', $category->id)
            ->inRandomOrder()
            ->first();
    }
}
