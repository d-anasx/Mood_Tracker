@extends('layouts.app')

@section('title', "User: {$user->name}")

@section('orbs')
    <div class="orb orb-purple"></div>
    <div class="orb orb-rose"></div>
@endsection

@push('styles')
    <style>
        .user-profile-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .user-avatar-large {
            font-size: 4rem;
            background: rgba(255, 255, 255, 0.05);
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 2px solid rgba(212, 165, 181, 0.3);
        }
        
        .user-info h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: #f0e6ee;
        }
        
        .stats-mini-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .stat-mini {
            background: rgba(255, 255, 255, 0.04);
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
        }
        
        .stat-mini-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--bloom);
        }
        
        .action-buttons {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    
    <a href="{{ route('admin.users.index') }}" class="text-bloom hover:opacity-80 mb-4 inline-block">← Back to Users</a>

    {{-- User Profile Header --}}
    <div class="glass-card p-6 mb-6">
        <div class="user-profile-header">
            <div class="user-avatar-large">{{ $user->avatar ?? '😊' }}</div>
            <div class="user-info">
                <h2>{{ $user->name }}</h2>
                <p class="text-mist">{{ $user->email }}</p>
                <p class="text-sm mt-1">
                    <span class="status-badge status-{{ $user->status }}">{{ ucfirst($user->status) }}</span>
                    <span class="text-mist ml-3">Joined {{ $user->created_at->format('F j, Y') }}</span>
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="action-buttons">
            @if($user->status === 'pending')
                <form method="POST" action="{{ route('admin.users.approve', $user->id) }}">
                    @csrf
                    <button type="submit" class="btn-primary" style="width: auto; padding: 0.6rem 1.2rem; background: linear-gradient(135deg, #10b981, #059669);">✅ Approve User</button>
                </form>
            @endif
            
            @if($user->status === 'active' && !$user->isAdmin())
                <button onclick="showBlockModal()" class="btn-primary" style="width: auto; padding: 0.6rem 1.2rem; background: linear-gradient(135deg, #ef4444, #dc2626);">🚫 Block User</button>
            @endif
            
            @if($user->status === 'blocked')
                <form method="POST" action="{{ route('admin.users.unblock', $user->id) }}">
                    @csrf
                    <button type="submit" class="btn-primary" style="width: auto; padding: 0.6rem 1.2rem; background: linear-gradient(135deg, #10b981, #059669);">🔓 Unblock User</button>
                </form>
            @endif
            
            <a href="{{ route('admin.notifications.create') }}?user_id={{ $user->id }}" class="btn-primary" style="width: auto; padding: 0.6rem 1.2rem; background: linear-gradient(135deg, #4ecdc4, #2fa89f);">📨 Send Notification</a>
        </div>
    </div>

    {{-- User Stats --}}
    <div class="stats-mini-grid">
        <div class="stat-mini">
            <div class="stat-mini-value">{{ $stats['total_entries'] }}</div>
            <div class="text-mist text-sm">Total Entries</div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-value">{{ $stats['avg_mood'] }}/10</div>
            <div class="text-mist text-sm">Average Mood</div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-value">{{ $stats['streak'] }} days</div>
            <div class="text-mist text-sm">Current Streak</div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-value">{{ $user->notifications()->count() }}</div>
            <div class="text-mist text-sm">Notifications</div>
        </div>
    </div>

    {{-- Admin Actions History --}}
    @if($adminActions->count() > 0)
    <div class="glass-card p-6">
        <h3 class="text-xl font-display text-white mb-4">Admin Action History</h3>
        
        <div class="space-y-2">
            @foreach($adminActions as $action)
            <div class="p-3 rounded-lg" style="background: rgba(255, 255, 255, 0.02);">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="capitalize font-medium">{{ $action->action_type }}</span>
                        <span class="text-mist text-sm ml-2">by {{ $action->admin->name }}</span>
                    </div>
                    <span class="text-mist text-xs">{{ $action->created_at->diffForHumans() }}</span>
                </div>
                @if($action->reason)
                    <p class="text-xs text-mist mt-1">Reason: {{ $action->reason }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Block Modal --}}
<div id="blockModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="glass-card p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-display text-white mb-2">Block User</h3>
        <p class="text-mist mb-4">Are you sure you want to block <strong>{{ $user->name }}</strong>?</p>
        <form method="POST" action="{{ route('admin.users.block', $user->id) }}">
            @csrf
            <textarea name="reason" class="w-full p-3 rounded-lg bg-white/5 border border-white/10 text-white" rows="3" placeholder="Reason for blocking..." required></textarea>
            <div class="flex gap-3 mt-4">
                <button type="button" onclick="closeBlockModal()" class="btn-secondary flex-1">Cancel</button>
                <button type="submit" class="btn-primary flex-1" style="background: linear-gradient(135deg, #ef4444, #dc2626);">Block User</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showBlockModal() {
        document.getElementById('blockModal').style.display = 'flex';
    }
    
    function closeBlockModal() {
        document.getElementById('blockModal').style.display = 'none';
    }
    
    document.getElementById('blockModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeBlockModal();
    });
</script>
@endsection