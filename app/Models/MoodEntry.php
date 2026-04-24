<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MoodEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mood_level',
        'sleep_hours',
        'reflection',
        'entry_date',
    ];

    protected $casts = [
        'entry_date'  => 'date',
        'sleep_hours' => 'float',
        'mood_level'  => 'integer',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feelings(): BelongsToMany
    {
        return $this->belongsToMany(Feeling::class, 'mood_entry_feelings');
    }

    // -------------------------------------------------------
    // Scopes
    // -------------------------------------------------------

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('entry_date', '>=', now()->subDays($days));
    }

    public function scopeOrderedByDate($query, string $direction = 'desc')
    {
        return $query->orderBy('entry_date', $direction);
    }

    // -------------------------------------------------------
    // Helper methods
    // -------------------------------------------------------

    /**
     * Check if the entry can still be edited (same day, before midnight).
     */
    public function isEditable(): bool
    {
        return $this->entry_date->isToday();
        
    }
}
