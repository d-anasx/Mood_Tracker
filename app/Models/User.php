<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'name',
        'avatar',
        'role_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function moodEntries(): HasMany
    {
        return $this->hasMany(MoodEntry::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(UserSettings::class);
    }

    public function adminActions(): HasMany
    {
        // Actions this user performed as admin
        return $this->hasMany(AdminAction::class, 'admin_id');
    }

    public function receivedAdminActions(): HasMany
    {
        // Actions performed on this user
        return $this->hasMany(AdminAction::class, 'target_user_id');
    }

    // -------------------------------------------------------
    // Helper methods
    // -------------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function todayEntry(): ?MoodEntry
    {
        return $this->moodEntries()
            ->whereDate('entry_date', today())
            ->first();
    }

    public function lastEntries(int $count = 11)
    {
        return $this->moodEntries()
            ->orderByDesc('entry_date')
            ->limit($count)
            ->get();
    }
}
