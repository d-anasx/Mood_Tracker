<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminAction;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->has('status') && in_array($request->status, ['active', 'pending', 'blocked'])) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with(['moodEntries' => function($q) {
                $q->latest()->take(30);
            }, 'moodEntries.feelings', 'notifications'])
            ->findOrFail($id);

        $stats = [
            'total_entries' => $user->moodEntries()->count(),
            'avg_mood' => round($user->moodEntries()->avg('mood_level') ?? 0, 1),
            'streak' => $this->getUserStreak($user),
            'last_entry' => $user->moodEntries()->latest()->first(),
        ];

        $adminActions = AdminAction::with('admin')
            ->where('target_user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        return view('admin.users.show', compact('user', 'stats', 'adminActions'));
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);

        if ($user->status !== 'pending') {
            return redirect()->back()->with('error', 'User is not pending approval.');
        }

        DB::transaction(function () use ($user) {
            $user->update(['status' => 'active']);

            AdminAction::create([
                'admin_id' => auth()->id(),
                'target_user_id' => $user->id,
                'action_type' => 'approve',
                'reason' => 'Account approved by admin',
            ]);

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Account Approved! 🎉',
                'message' => 'Your account has been approved by an administrator. You can now log in and start tracking your mood!',
                'is_read' => false,
                'created_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', "User {$user->name} has been approved.");
    }

    public function block(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $user = User::findOrFail($id);

        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot block an admin user.');
        }

        DB::transaction(function () use ($user, $request) {
            $user->update(['status' => 'blocked']);

            AdminAction::create([
                'admin_id' => auth()->id(),
                'target_user_id' => $user->id,
                'action_type' => 'block',
                'reason' => $request->reason,
            ]);

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Account Blocked',
                'message' => "Your account has been blocked by an administrator. Reason: {$request->reason}",
                'is_read' => false,
                'created_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', "User {$user->name} has been blocked.");
    }

    public function unblock($id)
    {
        $user = User::findOrFail($id);

        if ($user->status !== 'blocked') {
            return redirect()->back()->with('error', 'User is not blocked.');
        }

        DB::transaction(function () use ($user) {
            $user->update(['status' => 'active']);

            AdminAction::create([
                'admin_id' => auth()->id(),
                'target_user_id' => $user->id,
                'action_type' => 'unblock',
                'reason' => 'Account unblocked by admin',
            ]);

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Account Unblocked ✅',
                'message' => 'Your account has been unblocked by an administrator. You can now log in again.',
                'is_read' => false,
                'created_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', "User {$user->name} has been unblocked.");
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot delete an admin user.');
        }

        $userName = $user->name;

        DB::transaction(function () use ($user) {
            $user->moodEntries()->delete();
            $user->notifications()->delete();
            if ($user->settings) {
                $user->settings()->delete();
            }
            $user->delete();
        });

        return redirect()->route('admin.users.index')->with('success', "User {$userName} has been deleted.");
    }

    private function getUserStreak($user): int
    {
        $dates = $user->moodEntries()
            ->orderByDesc('entry_date')
            ->pluck('entry_date')
            ->map(fn($d) => Carbon::parse($d)->startOfDay())
            ->unique()
            ->values();

        if ($dates->isEmpty()) return 0;

        $streak = 0;
        $checkDate = Carbon::today();

        if (!$dates->first()->isSameDay($checkDate)) {
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