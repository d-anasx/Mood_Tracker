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
                <form method="POST" action="{{ route('admin.users.approve', $user->id) }}" class="inline" style="display: inline;">
                    @csrf
                    <button type="submit" class="action-btn action-approve">Approve</button>
                </form>
            @endif
            
            @if($user->status === 'active' && !$user->isAdmin())
                <button type="button" class="action-btn action-block" onclick="showBlockModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
                    Block
                </button>
            @endif
            
            @if($user->status === 'blocked')
                <form method="POST" action="{{ route('admin.users.unblock', $user->id) }}" class="inline" style="display: inline;">
                    @csrf
                    <button type="submit" class="action-btn action-unblock">Unblock</button>
                </form>
            @endif
            
            @if(!$user->isAdmin())
                <button type="button" class="action-btn action-delete" onclick="showDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
                    Delete
                </button>
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