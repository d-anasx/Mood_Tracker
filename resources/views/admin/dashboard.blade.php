@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('orbs')
    <div class="orb orb-purple"></div>
    <div class="orb orb-rose"></div>
    <div class="orb orb-amber" style="opacity:0.4;"></div>
@endsection

@push('styles')
    <style>
        .admin-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .admin-stat-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .admin-stat-card:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-2px);
        }
        
        .admin-stat-value {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--bloom);
        }
        
        .admin-stat-label {
            font-size: 0.85rem;
            color: rgba(169, 180, 194, 0.6);
            margin-top: 0.5rem;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th,
        .admin-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .admin-table th {
            color: var(--bloom);
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-active {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        
        .status-pending {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        
        .status-blocked {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #f0e6ee;
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    
    <div class="mb-8">
        <h1 class="text-3xl font-display text-white">Admin Dashboard</h1>
        <p class="text-mist mt-1">Manage users, view analytics, and control the platform</p>
    </div>

    {{-- Stats Grid --}}
    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-value">{{ $stats['total_users'] }}</div>
            <div class="admin-stat-label">Total Users</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-value text-emerald-400">{{ $stats['active_users'] }}</div>
            <div class="admin-stat-label">Active Users</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-value text-amber-400">{{ $stats['pending_users'] }}</div>
            <div class="admin-stat-label">Pending Approval</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-value text-rose-400">{{ $stats['blocked_users'] }}</div>
            <div class="admin-stat-label">Blocked Users</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-value">{{ $stats['total_entries'] }}</div>
            <div class="admin-stat-label">Total Mood Entries</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-value">{{ $stats['entries_today'] }}</div>
            <div class="admin-stat-label">Entries Today</div>
        </div>
    </div>

    {{-- Recent Users --}}
    <div class="glass-card p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="section-title">Recent Users</h2>
            <a href="{{ route('admin.users.index') }}" class="text-bloom hover:opacity-80 text-sm">View All →</a>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentUsers as $user)
                <tr>
                    <td class="flex items-center gap-2">
                        <span class="text-xl">{{ $user->avatar ?? '😊' }}</span>
                        {{ $user->name }}
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="status-badge status-{{ $user->status }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td>{{ $user->created_at->diffForHumans() }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn-sm text-bloom hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-mist">No users found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Recent Admin Actions --}}
    <div class="glass-card p-6">
        <h2 class="section-title">Recent Admin Actions</h2>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Target User</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentActions as $action)
                <tr>
                    <td>{{ $action->admin->name }}</td>
                    <td>
                        <span class="capitalize">{{ $action->action_type }}</span>
                        @if($action->reason)
                            <span class="text-xs text-mist block">Reason: {{ $action->reason }}</span>
                        @endif
                    </td>
                    <td>{{ $action->targetUser->name ?? 'Deleted User' }}</td>
                    <td>{{ $action->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-mist">No admin actions recorded</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection