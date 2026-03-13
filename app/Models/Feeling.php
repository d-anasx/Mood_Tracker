<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feeling extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name', 'icon', 'color'];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function moodEntries(): BelongsToMany
    {
        return $this->belongsToMany(MoodEntry::class, 'mood_entry_feelings');
    }
}
