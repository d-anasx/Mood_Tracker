<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MoodEntry;
use App\Models\AdminAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'pending_users' => User::where('status', 'pending')->count(),
            'blocked_users' => User::where('status', 'blocked')->count(),
            'total_entries' => MoodEntry::count(),
            'entries_today' => MoodEntry::whereDate('entry_date', today())->count(),
        ];

        $recentUsers = User::with('role')
            ->orderByRaw("FIELD(status, 'pending', 'active', 'blocked')")
            ->latest()
            ->take(10)
            ->get();

        $recentActions = AdminAction::with(['admin', 'targetUser'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentActions'));
    }
}