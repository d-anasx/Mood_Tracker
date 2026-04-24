<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPushSubscriptions;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ══════════════════════════════════════════════════════════
    // RELATIONSHIPS
    // ══════════════════════════════════════════════════════════

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function moodEntries()
    {
        return $this->hasMany(MoodEntry::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function adminActions()
    {
        return $this->hasMany(AdminAction::class, 'admin_id');
    }

    public function receivedAdminActions()
    {
        return $this->hasMany(AdminAction::class, 'target_user_id');
    }

    // ══════════════════════════════════════════════════════════
    // HELPER METHODS
    // ══════════════════════════════════════════════════════════

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role->name === 'admin';
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get today's mood entry.
     */
    public function todayEntry()
    {
        return $this->moodEntries()
            ->where('entry_date', today()->toDateString())
            ->with('feelings')
            ->first();

    }

    /**
     * Get last N entries.
     */
    public function lastEntries(int $count = 5)
    {
        return $this->moodEntries()
            ->orderBy('entry_date', 'desc')
            ->take($count)
            ->get();
    }

    // get streak 

    public function getStreak(): int
    {
        $dates = $this->moodEntries()
            ->orderByDesc('entry_date')
            ->pluck('entry_date')
            ->map(fn($d) => Carbon::parse($d)->startOfDay())
            ->unique()
            ->values();

        if ($dates->isEmpty()) {
            return 0;
        }

        $streak    = 0;
        $checkDate = Carbon::today();

        // If no entry today, start checking from yesterday
        if (! $dates->first()->isSameDay($checkDate)) {
            $checkDate = Carbon::yesterday();
        }

        foreach ($dates as $date) {
            if ($date->isSameDay($checkDate)) {
                $streak++;
                $checkDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}