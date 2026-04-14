@extends('layouts.app')

@section('title', 'Manage Users')

@section('orbs')
    <div class="orb orb-purple"></div>
    <div class="orb orb-teal"></div>
    <div class="orb orb-rose" style="opacity:0.3;"></div>
@endsection

@push('styles')
    <style>
        /* Admin specific styles matching MoodTrace aesthetic */
        .admin-container {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem 2rem 4rem;
        }
        
        .admin-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .admin-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #f0e6ee;
            margin-bottom: 0.3rem;
        }
        
        .filter-bar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }
        
        .filter-btn {
            font-size: 0.78rem;
            font-family: 'DM Sans', sans-serif;
            color: rgba(169, 180, 194, 0.55);
            text-decoration: none;
            padding: 6px 18px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.2s ease;
            align-self: center;
        }
        
        .filter-btn:hover {
            color: #f0e6ee;
            background: rgba(255, 255, 255, 0.06);
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, #c0587a, #7c3fa0);
            color: #fff;
            border-color: transparent;
        }
        
        .search-form {
            display: flex;
            gap: 0.5rem;
            margin-left: auto;
        }
        
        .search-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 999px;
            padding: 0.6rem 1.2rem;
            color: #f0e6ee;
            font-size: 0.85rem;
            min-width: 220px;
        }
        
        .search-input:focus {
            outline: none;
            border-color: rgba(212, 165, 181, 0.4);
            box-shadow: 0 0 0 3px rgba(212, 165, 181, 0.07);
        }
        
        .search-input::placeholder {
            color: rgba(169, 180, 194, 0.4);
        }
        
        .search-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 999px;
            padding: 0.6rem 1.2rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .search-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(212, 165, 181, 0.25);
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .users-table th {
            text-align: left;
            padding: 1.2rem 1rem;
            color: var(--bloom);
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }
        
        .users-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: rgba(240, 230, 238, 0.8);
            font-size: 0.85rem;
        }
        
        .users-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }
        
        .user-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .user-avatar-sm {
            width: 36px;
            height: 36px;
            background: rgba(192, 88, 122, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 500;
            letter-spacing: 0.03em;
        }
        
        .status-active {
            background: rgba(78, 205, 196, 0.12);
            color: #4ecdc4;
            border: 1px solid rgba(78, 205, 196, 0.25);
        }
        
        .status-pending {
            background: rgba(244, 162, 97, 0.12);
            color: #f4a261;
            border: 1px solid rgba(244, 162, 97, 0.25);
        }
        
        .status-blocked {
            background: rgba(224, 82, 99, 0.12);
            color: #e05263;
            border: 1px solid rgba(224, 82, 99, 0.25);
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .action-btn {
            padding: 0.35rem 0.8rem;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            font-family: 'DM Sans', sans-serif;
        }
        
        .action-view {
            background: rgba(167, 139, 250, 0.12);
            color: #a78bfa;
            border: 1px solid rgba(167, 139, 250, 0.2);
        }
        
        .action-view:hover {
            background: rgba(167, 139, 250, 0.2);
            transform: translateY(-1px);
        }
        
        .action-approve {
            background: rgba(78, 205, 196, 0.12);
            color: #4ecdc4;
            border: 1px solid rgba(78, 205, 196, 0.2);
        }
        
        .action-approve:hover {
            background: rgba(78, 205, 196, 0.2);
            transform: translateY(-1px);
        }
        
        .action-block {
            background: rgba(224, 82, 99, 0.12);
            color: #e05263;
            border: 1px solid rgba(224, 82, 99, 0.2);
        }
        
        .action-block:hover {
            background: rgba(224, 82, 99, 0.2);
            transform: translateY(-1px);
        }
        
        .action-unblock {
            background: rgba(78, 205, 196, 0.12);
            color: #4ecdc4;
            border: 1px solid rgba(78, 205, 196, 0.2);
        }
        
        .action-unblock:hover {
            background: rgba(78, 205, 196, 0.2);
            transform: translateY(-1px);
        }
        
        .action-delete {
            background: rgba(224, 82, 99, 0.08);
            color: #e05263;
            border: 1px solid rgba(224, 82, 99, 0.15);
        }
        
        .action-delete:hover {
            background: rgba(224, 82, 99, 0.15);
            transform: translateY(-1px);
        }
        
        .entries-count {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            color: var(--bloom);
        }
        
        .pagination-container {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
        }
        
        .pagination {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        
        .pagination a, 
        .pagination span {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 0.75rem;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: rgba(169, 180, 194, 0.6);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        
        .pagination a:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(212, 165, 181, 0.25);
            color: #f0e6ee;
        }
        
        .pagination .active span {
            background: linear-gradient(135deg, #c0587a, #7c3fa0);
            color: #fff;
            border-color: transparent;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem;
            color: rgba(169, 180, 194, 0.45);
        }
        
        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
        
        /* Modal styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .modal-content {
            max-width: 450px;
            width: 90%;
            padding: 2rem;
        }
        
        .modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #f0e6ee;
            margin-bottom: 0.5rem;
        }
        
        .modal-text {
            color: rgba(169, 180, 194, 0.6);
            margin-bottom: 1.5rem;
        }
        
        .modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        
        .btn-modal-cancel {
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: rgba(169, 180, 194, 0.6);
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-modal-cancel:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #f0e6ee;
        }
        
        .btn-modal-confirm {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #e05263, #c0587a);
            border: none;
            border-radius: 10px;
            color: #fff;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-modal-confirm:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(224, 82, 99, 0.3);
        }
        
        textarea.modal-reason {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 12px;
            padding: 0.75rem;
            color: #f0e6ee;
            font-family: 'DM Sans', sans-serif;
            margin-bottom: 1rem;
            resize: vertical;
        }
        
        textarea.modal-reason:focus {
            outline: none;
            border-color: rgba(212, 165, 181, 0.4);
        }
        
        @media (max-width: 768px) {
            .admin-container {
                padding: 1rem;
            }
            .users-table th,
            .users-table td {
                padding: 0.75rem 0.5rem;
            }
            .action-buttons {
                flex-direction: column;
                gap: 0.3rem;
            }
            .search-form {
                margin-left: 0;
                width: 100%;
            }
            .search-input {
                flex: 1;
            }
        }
    </style>
@endpush

@section('content')
<div class="admin-container">
    
    <div class="admin-header">
        <div>
            <p class="eyebrow">Admin Panel</p>
            <h1 class="admin-title">Manage Users</h1>
            <p class="muted" style="font-size:0.85rem;">Approve, block, or manage user accounts</p>
        </div>
        
        <a href="{{ route('admin.notifications.create') }}" class="btn-primary" style="width: auto; padding: 0.85rem 1.8rem;">
            📨 Send Notification
        </a>
    </div>

    {{-- Filters --}}
    <div class="filter-bar">
        <a href="{{ route('admin.users.index') }}" class="filter-btn {{ !request('status') ? 'active' : '' }}">All</a>
        <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="filter-btn {{ request('status') === 'pending' ? 'active' : '' }}">Pending</a>
        <a href="{{ route('admin.users.index', ['status' => 'active']) }}" class="filter-btn {{ request('status') === 'active' ? 'active' : '' }}">Active</a>
        <a href="{{ route('admin.users.index', ['status' => 'blocked']) }}" class="filter-btn {{ request('status') === 'blocked' ? 'active' : '' }}">Blocked</a>
        
        <form method="GET" action="{{ route('admin.users.index') }}" class="search-form">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" placeholder="Search by name or email..." value="{{ request('search') }}" class="search-input">
            <button type="submit" class="search-btn">🔍</button>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="glass-card" style="overflow-x: auto; padding: 0;">
        <table class="users-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Entries</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-sm">{{ $user->avatar ?? '😊' }}</div>
                            <span>{{ $user->name }}</span>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="status-badge status-{{ $user->status }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td class="entries-count">{{ $user->moodEntries()->count() }}</td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="action-btn action-view">View</a>
                            
                            @if($user->status === 'pending')
                                <form method="POST" action="{{ route('admin.users.approve', $user->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="action-btn action-approve">Approve</button>
                                </form>
                            @endif
                            
                            @if($user->status === 'active' && !$user->isAdmin())
                                <button onclick="showBlockModal({{ $user->id }}, '{{ $user->name }}')" class="action-btn action-block">Block</button>
                            @endif
                            
                            @if($user->status === 'blocked')
                                <form method="POST" action="{{ route('admin.users.unblock', $user->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="action-btn action-unblock">Unblock</button>
                                </form>
                            @endif
                            
                            @if(!$user->isAdmin())
                                <button onclick="showDeleteModal({{ $user->id }}, '{{ $user->name }}')" class="action-btn action-delete">Delete</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon">👥</div>
                            <p>No users found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pagination-container">
        {{ $users->links() }}
    </div>
</div>

{{-- Block Modal --}}
<div id="blockModal" class="modal-overlay" style="display: none;">
    <div class="glass-card modal-content">
        <h3 class="modal-title">Block User</h3>
        <p class="modal-text" id="blockUserName"></p>
        <form method="POST" id="blockForm">
            @csrf
            <textarea name="reason" class="modal-reason" rows="3" placeholder="Reason for blocking..." required></textarea>
            <div class="modal-actions">
                <button type="button" onclick="closeBlockModal()" class="btn-modal-cancel">Cancel</button>
                <button type="submit" class="btn-modal-confirm">Block User</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Modal --}}
<div id="deleteModal" class="modal-overlay" style="display: none;">
    <div class="glass-card modal-content">
        <h3 class="modal-title">Delete User</h3>
        <p class="modal-text" id="deleteUserName"></p>
        <p class="modal-text" style="font-size:0.8rem; color:#e05263;">⚠️ This action cannot be undone. All user data including mood entries will be permanently deleted.</p>
        <form method="POST" id="deleteForm">
            @csrf
            @method('DELETE')
            <div class="modal-actions">
                <button type="button" onclick="closeDeleteModal()" class="btn-modal-cancel">Cancel</button>
                <button type="submit" class="btn-modal-confirm" style="background: linear-gradient(135deg, #e05263, #b91c1c);">Delete User</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showBlockModal(userId, userName) {
        document.getElementById('blockUserName').textContent = `Are you sure you want to block ${userName}?`;
        document.getElementById('blockForm').action = `/admin/users/${userId}/block`;
        document.getElementById('blockModal').style.display = 'flex';
    }
    
    function closeBlockModal() {
        document.getElementById('blockModal').style.display = 'none';
    }
    
    function showDeleteModal(userId, userName) {
        document.getElementById('deleteUserName').textContent = `Delete ${userName}?`;
        document.getElementById('deleteForm').action = `/admin/users/${userId}`;
        document.getElementById('deleteModal').style.display = 'flex';
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }
    
    // Close modals on outside click
    document.getElementById('blockModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeBlockModal();
    });
    
    document.getElementById('deleteModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>
@endsection