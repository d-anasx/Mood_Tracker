<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MoodEntryFeeling extends Pivot
{
    public $timestamps = false;

    protected $table = 'mood_entry_feelings';

    protected $fillable = ['mood_entry_id', 'feeling_id'];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function moodEntry(): BelongsTo
    {
        return $this->belongsTo(MoodEntry::class);
    }

    public function feeling(): BelongsTo
    {
        return $this->belongsTo(Feeling::class);
    }
}
