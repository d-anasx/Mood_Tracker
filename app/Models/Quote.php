<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    use HasFactory;

    public $updatedAt = false;

    protected $fillable = ['text', 'author', 'category_id', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    // ── Relationships ──

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuoteCategory::class, 'category_id');
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Static helper ──
    // Returns a single random active Quote object for a given mood level.
    // Returns null if no matching quote exists.

    public static function forMoodLevel(int $moodLevel): ?self
    {
        $category = QuoteCategory::where('mood_level', $moodLevel)->first();

        if (!$category) {
            // No exact match — try any active quote
            return self::where('is_active', true)->inRandomOrder()->first();
        }

        return self::where('category_id', $category->id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->first();
    }
}